let http = require("http");
const mysqlConf = require("../config").dbinfo;
const urlParse = require("url").parse;
let moment = require("moment");
let seedrandom = require('seedrandom');

//官方自营彩票生成server
let mysqlPool = global.mysqlPool;
const randNu = "01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789";


//链接mysql
let returnControl = {
    request: {},
    response: {},
    init: function (req, res) {
        this.request = req;
        this.response = res;
    },
    jsonReturn: function (json) {
        this.response.writeHead(200, {"Content-Type": "text/plain"});
        this.response.write(JSON.stringify(json));
        this.response.end();
    },
    _404: function (msg) {
        this.response.writeHead(404, {"Content-Type": "text/plain"});
        this.response.write(msg);
        this.response.end();
    },
    end: function () {
        this.response.end();
    }
};
let gameControl = {
    getType: function (type) {

    },
    //生成随机数字
    createRandNum: function (len) {
        let code = [];
        for (let i = 0; i < len; i++) {
            let date = new Date();
            let rng = seedrandom(date.getTime());
            let rand = (rng() * Math.random() * date.getTime()) / 1000000000000;
            rand = randNu[parseInt(rand * (randNu.length - 1))];
            if (rand === undefined) {
                rand = randNu[parseInt(Math.random() * (randNu.length - 1))];
            }
            code.push(rand);
        }
        return code.join(',');
    }
};
exports.start = function () {
    //server
    let server = http.createServer(function (request, response) {
        returnControl.init(request, response);
        let req = urlParse(request.url, true);
        let typeId = req.query.id;
        let action = req.query.action;
        let key = req.query.key;
        if (action === "open" && key === "lottery_kk") {
            let openData = {};
            openData.type = req.query.type;
            openData.number = req.query.number;
            openData.time = req.query.time;
            openData.data = req.query.data;
            mysqlPool.query("INSERT INTO lottery_data set ?", openData, function (err) {
                console.log(err);
            });
            response.write("success");
            returnControl.end();
            return;
        }
        if (typeId === undefined) {
            returnControl._404("err");
            return;
        }
        //去解析
        let date = new Date();
        let hours = date.getHours();
        let mins = date.getMinutes();
        let ss = date.getSeconds();
        let time = `${hours}:${mins}:${ss}`;
        let nowDate = moment().format("YYYYMMDD");

        //获取最新的期数,并且没有开奖得,优化可以用AWAIT,
        mysqlPool.query({
            sql: "SELECT * FROM lottery_data_time  WHERE  `type`=? AND ?>`actionTime` ORDER BY `actionTime` DESC limit 1",
            timeout: 3000,
            values: [typeId, time],
        }, function (error, results, fields) {
            if (error) {
                returnControl._404("系统错误");
                return;
            }
            //获取数据
            if (results.length === 1) {
                //去判断上期开奖结果
                let openAction = results[0];
                //去开奖值
                mysqlPool.query({
                    sql: "SELECT * FROM lottery_self_sale WHERE `type_id`=? AND `open_issue`=? AND `date`=?",
                    timeout: 3000,
                    values: [openAction.type, openAction.actionNo, nowDate]
                }, function (error, results2, fields) {
                    if (error) {
                        returnControl._404("系统错误");
                        return;
                    }
                    let res = {};
                    if (results2.length <= 0) {
                        //生成开奖结果
                        res.type_id = openAction.type;
                        //生成结果类型
                        switch (res.type_id) {
                            case 60:
                                res.open_code = gameControl.createRandNum(3);
                                break;
                            default:
                                res.open_code = gameControl.createRandNum(5);
                                break;
                        }
                        res.date = nowDate;
                        res.open_date = date.getTime() / 1000;
                        res.open_issue = openAction.actionNo;
                        //执行输出
                        mysqlPool.query("INSERT lottery_self_sale SET ?", res, function (err) {
                            if (err) {
                                returnControl._404("系统错误");
                                return;
                            }
                        })
                    } else {
                        res = results2[0];
                    }
                    if (res == null) {
                        returnControl.end();
                        return;
                    }
                    response.writeHead(200, {"Content-Type": "text/plain"});
                    response.write(JSON.stringify(res));
                    response.end();
                    return;
                })
            } else {
                returnControl._404("系统错误");
                return;
            }
        });
    }).listen(8081);
    server.on('clientError', (err, socket) => {
        console.log('HTTP/1.1 400 Bad Request\r\n\r\n');
    });
};
