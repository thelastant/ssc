const cluster = require('cluster');
const encrypt_key = 'cc40bfe6d972ce96fe3a47d0f7342cb0';

let mysqlPool;

let played = {},
    mysql = require('mysql'),
    http = require('http'),
    url = require('url'),
    crypto = require('crypto'),
    querystring = require('querystring'),
    config = require('./config.js'),
    calc = require('./kj-data/kj-calc-time.js'),
    exec = require('child_process').exec,
    execPath = process.argv.join(" "),
    parse = require('./kj-data/parse-calc-count.js');

require('./plugins/String-ext.js');
const log4js = require('log4js');

const DB_PREFIX = "lottery_";

log4js.configure({
    appenders: [
        {type: 'console'},
        {type: 'file', filename: 'runtime/logs/debug.log', category: 'cheese'}
    ]
});
const logger = log4js.getLogger('cheese');

global.played = {};//玩法配置
let timers = {}; // 任务记时器列表

if (!mysqlPool) {
    mysqlPool = mysql.createPool(config.dbinfo);
}
global.mysqlPool = mysqlPool;
//开启自营CPServer
require("./server/cp_server").start();

// 抛出未知出错时处理
process.on('uncaughtException', function (e) {
    console.log(e);
});

http.request = (function (_request) {
    return function (options, callback) {
        var timeout = options['timeout'],
            timeoutEventId;
        var req = _request(options, function (res) {
            res.on('end', function () {
                clearTimeout(timeoutEventId);
                //console.log('response end...');
            });

            res.on('close', function () {
                clearTimeout(timeoutEventId);
                //console.log('response close...');
            });

            res.on('abort', function () {
                //console.log('abort...');
            });

            callback(res);
        });

        //超时
        req.on('timeout', function () {
            //req.res && req.res.abort();
            //req.abort();
            req.end();
        });

        //如果存在超时
        timeout && (timeoutEventId = setTimeout(function () {
            req.emit('timeout', {message: 'have been timeout...'});
        }, timeout));
        return req;
    };
})(http.request);

//inti playedfun
getPlayedFun(runTask);

/**
 * Import
 * @param cb 回调函数
 */
function getPlayedFun(cb) {
    mysqlPool.query("select id, ruleFun from lottery_played", function (err, data) {
        if (err) {
            log('读取玩法配置出错：' + err.message);
        } else {
            data.forEach(function (v) {
                played[v.id] = v.ruleFun;
                global.played[v.id] = v.ruleFun;
            });
            if (cb) {
                cb();
            }
        }
    });
}


/**
 * Import
 * 便利任务，开始任务系统
 */
function runTask() {
    if (config.cp.length)
        config.cp.forEach(function (conf) {
            timers[conf.name] = {};
            timers[conf.name][conf.timer] = {
                timer: null,
                option: conf
            };
            try {
                if (conf.enable) {
                    run(conf);
                }
            }
            catch (err) {
                restartTask(conf, config.errorSleepTime);
            }
        });
}

/**
 * Import
 * 任务重启
 * @param conf
 * @param sleepTimer
 */
function restartTask(conf, sleepTimer) {
    if (sleepTimer <= 0)
        sleepTimer = config.errorSleepTime;
    if (!timers[conf.name]) {
        timers[conf.name] = {};
    }

    if (!timers[conf.name][conf.timer]) {
        timers[conf.name][conf.timer] = {timer: null, option: conf};
    }
    //清理定时器
    clearTimeout(timers[conf.name][conf.timer].timer);
    //重启程序
    timers[conf.name][conf.timer].timer = setTimeout(run, sleepTimer, conf);
    log('休眠' + sleepTimer / 1000 + '秒后从【' + conf.source + '】采集' + conf.title + '数据...............' + `,【TypeID:${conf.type_id}】`);
}

/**
 * Import
 * 完整执行一个任务
 * @param conf
 */
function run(conf) {
    if (timers[conf.name][conf.timer].timer) {
        clearTimeout(timers[conf.name][conf.timer].timer);
    }
    log('***********开始从' + conf.source + '采集' + conf.title + '数据' + `,TypeID:${conf.type_id}` + "**********");
    let option = JSON.parse(JSON.stringify(conf.option));
    http.request(option, function (res) {
        let data = '';

        res.on("data", function (_data) {
            data += _data.toString();
        });

        res.on("end", function () {
            let isRestart = false;
            try {
                data = conf.parse(data);
                submitData(data, conf, function (timer) {
                    if (isRestart == false) {
                        restartTask(conf, timer.sleepTimer);
                    }
                });
            } catch (err) {
                //这里进行数据分析，任务重启
                log('解析' + conf.title + `数据出错,来源【${conf.source}】` + err);
                restartTask(conf, config.errorSleepTime);
            }
        });

        res.on("error", function (err) {
            log(err);
            restartTask(conf, config.errorSleepTime);
        });
    }).on('timeout', function (err) {
        log('从' + conf.source + '采集' + conf.title + '数据超时');
        restartTask(conf, config.errorSleepTime);
    }).on("error", function (err) {
        // 一般网络出问题会引起这个错
        log(err);
        restartTask(conf, config.errorSleepTime);
    }).end();
}


/**
 * Import
 * 提交数据
 * @param data
 * @param conf
 * @param callback
 */
function submitData(data, conf, callback) {
    console.log("\n");
    log('\n\n\n++++++++++++++++++++++++++++++++++++++++++\n\n\n');


    log('提交从' + conf.source + '采集的' + conf.title + '第' + data.number + '数据：' + data.data + `,【TypeID:${conf.type_id}】`);
    data.time = Math.floor((new Date(data.time)).getTime() / 1000);

    if (typeof data.expect === "undefined") {
        data.expect = 0;
    }
    //这个地方验证是否和上期数据相同，如果相同就需要去把用户提交的投注给撤掉
    check_same_codes(data.type, data.number, data.data, function () {
        mysqlPool.query("insert into lottery_data(type, time, number, data,expect) values(?,?,?,?,?)", [
            data.type, data.time, data.number, data.data, data.expect
        ], function (err, result) {
            let retTimer = {
                sleepTimer: config.errorSleepTime,
                title: conf.title
            };

            if (err) {
                // 普通出错,添加重复数据
                if (err.errno === 1062) {
                    try {
                        log(conf['title'] + '第' + data.number + '期数据已经存在数据');
                    } catch (ex) {
                        console.log(ex);
                    }
                    calcJ(data, true);
                    //计算任务重启时间
                    console.log("调用函数：" + conf.name);
                    let timerCount = 10000;
                    try {
                        let timerCount = calc[conf.name](data);
                    } catch (ex) {
                        timerCount = 6000;
                    }
                    retTimer.sleepTimer = parseInt(timerCount);
                    if (retTimer.sleepTimer < 0) {
                        retTimer.sleepTimer = config.errorSleepTime;
                    }
                } else {
                    log('ERR....运行出错Method:submitData ' + err);
                }
            } else if (result) {
                //数据正常提交开奖数据
                setTimeout(calcJ, 500, data);
            } else {
                log('ERR......未知运行出错Method:submitData');
            }
            log(`NOTICE......重启任务已经提交：${retTimer.sleepTimer / 1000}秒后重启`);
            if (callback) {
                callback(retTimer);
            }
        });
    });
}

function requestKj(type, number) {
    let option = {
        host: config.submit.host,
        path: '%s/%s/%s/%'.format(config.submit.path, type, number)
    };
    http.get(config.submit, function (res) {
    });
}

/**
 * Import
 * 计算派奖sql
 * @param data
 * @param flag
 */
function calcJ(data, flag) {
    let sql = "select * from lottery_bets where type=? and actionNo=? and isDelete=0";
    if (flag) {
        sql += " and lotteryNo=''";
    }
    mysqlPool.query(sql, [
        data.type, data.number
    ], function (err, bets) {
        if (err) {
            logger.debug("读取投注出错：" + err);
        } else {
            let sqls = [];
            bets.forEach(function (bet) {
                let fun, zjCount = 0, tmpSql = 'call kanJiang(?, ?, ?, ?)';
                try {
                    fun = parse[played[bet.playedId]];
                    if (typeof fun != 'function') {
                        logger.error('计算玩法[%f]中奖号码算法不可用：%s'.format(bet.playedId, err.message));
                    }
                    zjCount = fun(bet.actionData, data.data, bet.weiShu) || 0;
                } catch (err) {
                    logger.error('计算中奖号码时出错：' + err);
                    return;
                }
                //加入开奖
                sqls.push(mysql.format(tmpSql, [bet.id, zjCount, data.data, 'lottery_running']));
            });
            try {
                setPj(sqls, data);
            } catch (err) {
                logger.debug(err);
            }
        }
    });
}

//检查是否和上次开奖号是一样的,如果一样就进行撤单处理
function check_same_codes(type, action_no, open_number, callback) {
    let last_no = parseInt(action_no.substr(action_no.length - 1, 1)) - 1;
    last_no = action_no.substr(0, action_no.length - 1) + '' + last_no;
    mysqlPool.query(`SELECT * FROM ${DB_PREFIX}data WHERE type=? AND number=? AND data=?`, [type, last_no, open_number], function (err, result) {

        if (result.length > 0) {
            //撤单
            console.error("存在连续重复开奖号码，统一撤单");
            mysqlPool.query("call same_cancel_bets(?, ?)", [type, action_no], function (err, res) {
                if (err) {
                    logger.error("撤单失败：", err);
                } else {
                    logger.warn(`撤单成功信息：彩种=${type},期数=${action_no}`);
                    callback();
                }
            });
        } else {
            callback();
        }
    });
}

/**
 * Import
 * 设置派奖
 * @param sqls
 * @param data
 */
function setPj(sqls, data) {
    if (sqls.length === 0) {
        log('彩种[%f]第%s期没有投注'.format(data.type, data.number));
        return false;
    }
    sqls.forEach(function (sql) {
        mysqlPool.query(sql, function (err, result) {
            if (err) {
                logger.error('错误，派奖失败.', err);
                logger.error("失败信息：", sql);
            } else {
                logger.info('恭喜，派奖成功.');
            }
        });
    });
}


// 前台添加数据接口
http.createServer(function (req, res) {
    let data = '';
    let reqArr = req.url.split("?");
    data = querystring.parse(reqArr[1]);
    if (reqArr[0] == '/data/kj') {
        logger.error("后台手动开奖：" + JSON.stringify(data));
        calcJ(data, true)
    }
    res.writeHead(200, {"Content-Type": "text/json"});
    res.write(JSON.stringify({"msg": "success"}));
    res.end();

}).listen(65531);