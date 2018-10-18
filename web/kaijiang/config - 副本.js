let mysql = require('mysql');
let parse = require('./kj-data/parse-calc-count.js');
let moment = require("moment");

//qq24549162 qq群611960155
//傻啊账号密码 
// const APIPLUS_ACCOUNT = {
//     token: 'e3b74992c08c60ae',
//     format: 'json',
// };


//apiplus 账号和密码,小马哥的账号和密码
const APIPLUS_ACCOUNT = {
    token: '1ee039e942af86ec',
    format: 'json',
};


//http://face.opencai.net/?token=8ebac319e1e6be2e&verify=90baf40db048
//开发正式账号密码
// const APIPLUS_ACCOUNT = {
//     token: '8ebac319e1e6be2e',
//     format: 'json',
// };


//开启彩种
const enable_types = [
    1, 5, 6, 7, 9, 10, 14, 16, 20, 23, 25, 26, 29, 35, 39, 50, 51, 53, 60, 66, 67, 68, 69, 120, 121, 122, 123, 124, 125, 126, 127, 128
];


//自营类型彩种
const self_sale = [
    5, 14, 26, 60, 67, 68, 120, 126
];

const mysql_conf = {
    host: '127.0.0.1',
    user: 'root',
    password: 'root123..',
    database: '2018sql'
};

let cp_types = [
    //#region 时时彩
	{
        title:'重庆时时彩',
        source:'AOB采集',
        name:'cqssc',
        enable:true,
        timer:'cqssc',
        type_id: 1,
        option:{

        host:"e.apiplus.net",
		path: '/newly.do?token=te4faba9cc864e96bk&code=cqssc&format=xml',
        timeout:3000,
        headers:{
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },

        parse:function(str){
            try{                                                                                              	//
                str=str.substr(0,200);	                                                                      	//
                var reg=/<row expect="([\d\-]+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/;                   	//
                var m;
                if(m=str.match(reg)){                                                                         	//
                    return {                                                                                  	//
                        type:1,                                                                              	//
                        time:m[3],                                                                            	//
                        number:m[1],                                                                          	//
                        data:m[2]                                                                             	//
                    };                                                                                        	//
                }					                                                                          	//
            }catch(err){                                                                                      	//
                throw('重庆时时彩AOB解析数据不正确');                                                            	//
            }
        }
    },
	{
        title:'天津时时彩',
        source:'AOB采集',
        name:'tjssc',
        enable:true,
        timer:'tjssc',
        type_id: 35,
        option:{

        host:"e.apiplus.net",
		path: '/newly.do?token=te4faba9cc864e96bk&code=tjssc&format=xml',
        timeout:3000,
        headers:{
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },

        parse:function(str){
            try{                                                                                              	//
                str=str.substr(0,200);	                                                                      	//
                var reg=/<row expect="([\d\-]+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/;                   	//
                var m;
                if(m=str.match(reg)){                                                                         	//
                    return {                                                                                  	//
                        type:35,                                                                              	//
                        time:m[3],                                                                            	//
                        number:m[1],                                                                          	//
                        data:m[2]                                                                             	//
                    };                                                                                        	//
                }					                                                                          	//
            }catch(err){                                                                                      	//
                throw('天津时时彩AOB解析数据不正确');                                                            	//
            }
        }
    },

    //天津时时彩
    


    //江西时时彩已经停止了
    {
        title: '江西时时彩',
        source: '百度乐彩',
        name: 'jxssc_baidu',
        enable: false,
        type_id: 3,
        timer: 'jxssc_baidu',
        option: {
            host: 'baidu.lecai.com',
            timeout: 30000,
            path: '/lottery/ajax_latestdrawn.php?lottery_type=202',
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/view/202?phase=20150821084&agentId=5621',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                var data = JSON.parse(str);
                if (typeof data.data[0].result.result[0].data === 'object') {
                    var time = data.data[0].time_endticket;
                    var number = data.data[0].phase;
                    var data = data.data[0].result.result[0].data.join(',');
                    return {
                        type: 3,
                        time: time,
                        number: number,
                        data: data,
                    };
                }
            } catch (err) {
                throw('江西时时彩解析数据不正确');
            }
        },
    },

    //北京28
    {
        title: '北京28',
        source: '360彩票',
        name: 'bj28_360',
        enable: false,
        timer: 'bj28_360',
        type_id: 105,
        option: {
            host: 'cp.360.cn',
            timeout: 30000,
            path: '/ssccq/',
            headers: {'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)'},
        },
        parse: function (str) {
            try {
                return getFrom360CP(str, 1);
            } catch (err) {
                throw(this.title, err);
            }
        },
    },

    {
        title: '北京28',
        source: '百度乐彩',
        name: 'bj28_360',
        enable: false,
        timer: 'bj28_360',
        type_id: 105,
        option: {
            host: 'baidu.lecai.com',
            timeout: 30000,
            path: '/lottery/draw/view/557/608319?agentId=5563',
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/sorts/cqssc.php?phase=20150821112&agentId=5591',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                console.log(str);
            } catch (err) {
                throw(this.title, err);
            }
        },
    },

    //北京快乐8
    {
        title: '北京快乐8',
        source: '500万彩票网',
        name: 'bjkl8_500',
        enable: false,
        timer: 'bjkl8_500',
        type_id: 24,
        option: {
            host: "kaijiang.500.com",
            timeout: 30000,
            path: '/static/info/kaijiang/xml/kl8/' + moment().format("YYYYMMDD") + '.xml',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },
        parse: function (str) {
            try {
                str = str.substr(0, 300);
                let reg = /<row expect="(\d+?)" opencode="([\d\,]+?)" specail="(\d+?)" opentime="([\d\:\- ]+?)"/;
                let m = str.match(reg);
                let tmp = {
                    type: this.type_id,
                    time: m[4],
                    number: m[1],
                    data: m[2],
                    expect: m[1],
                };
                console.log(tmp);
                return tmp;
            } catch (err) {
                throw(this.title + "解析不正确");
            }
        }
    },


    /*http://face.apius.cn/***************START*/
    //#韩国1.5
    {
        title: '韩国1.5分彩',
        source: 'apius',
        name: 'apius_hg15fc',
        enable: true,
        type_id: 66,
        timer: 'apius_hg15fc',
        option: {
            host: 'a.apiplus.net',
            timeout: 3000,
            path: `/newly.do?token=${APIPLUS_ACCOUNT.token}&code=krkeno&format=${APIPLUS_ACCOUNT.format}`,
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/view/202?phase=20150821084&agentId=5621',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                let tmp = JSON.parse(str);
                let parseNew = tmp.data[0];
                let openTime = tmp.data[0].opentime;
                let parseResult = {};

                //#老
                // //解析期数
                // let count = 880;
                // //1.5分钟一期
                // let up = 90;
                // //可能有一些延迟
                // let nu_time = moment().format("YYYY-MM-DD") + " 00:00:20";//第一波,第一期,04:59:00结束
                // let do_time = moment().format("YYYY-MM-DD") + " 07:00:20";//后一波,第一期
                // let date = new Date();
                // //看是第几波
                // if (parseInt(Date.parse(openTime) / 1000) <= parseInt(Date.parse(do_time) / 1000) && parseInt(Date.parse(openTime) / 1000) >= parseInt(Date.parse(nu_time) / 1000)) {
                //     //第一波
                //     let nowTime = parseInt(Date.parse(openTime) / 1000) - parseInt(Date.parse(nu_time) / 1000);
                //     let nowCount = parseInt(nowTime / up) + 1;
                //     if (nowCount > 200 || nowCount <= 0) {
                //         throw("期数错误");
                //     }
                //     parseResult.number = moment().format("YYYYMMDD0") + nowCount;
                // } else {
                //     //第二波
                //     let nowTime = parseInt(Date.parse(openTime) / 1000) - parseInt(Date.parse(do_time) / 1000);
                //     let nowCount = parseInt(nowTime / up) + 201;
                //     if (nowCount <= 201 || nowCount > 880) {
                //         throw("期数错误");
                //     }
                //     parseResult.number = moment().format("YYYYMMDD0") + nowCount;
                // }
                //#

                parseResult.time = parseNew.opentime;
                parseResult.number = parseNew.expect;
                parseResult.data = parseApiPlusNumber(tmp.data[0].opencode, 5);
                parseResult.type = this.type_id;
                parseResult.expect = parseNew.expect;
                return parseResult;
            } catch (err) {
                throw(this.title, err);
            }
        },
    },
    //#东京快乐8【东京1.5分彩-OK】
	{
        title: '东京1.5分彩',
        source: 'Node System API',
        name: 'apius_djybkl8',
        enable: false,
        timer: 'apius_djybkl8',
        type_id: 67,
        option: {
            host: "127.0.0.1",
            port: 8081,
            path: '/open?id=67',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) "
            }
        },
        parse: function (str) {
            try {
                return parseSystemOpenCp(str, this.type_id);
            } catch (err) {
                throw(this.title, err);
            }
        }
    },

    //#新加坡快乐彩【新加坡2分彩-OK】
	{
        title: '新加坡2分彩',
        source: 'Node System API',
        name: 'apius_xjpklc',
        enable: false,
        timer: 'apius_xjpklc',
        type_id: 68,
        option: {
            host: "127.0.0.1",
            port: 8081,
            path: '/open?id=68',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) "
            }
        },
        parse: function (str) {
            try {
                return parseSystemOpenCp(str, this.type_id);
            } catch (err) {
                throw(this.title, err);
            }
        }
    },

    //新加坡新版快乐8
    {
        title: '新加坡新版快乐8',
        source: 'apius',
        name: 'apius_xjpkl8',
        enable: true,
        type_id: 128,
        timer: 'apius_xjpkl8',
        option: {
            host: 'a.apiplus.net',
            timeout: 3000,
            path: `/newly.do?token=${APIPLUS_ACCOUNT.token}&code=sgnkl8&format=${APIPLUS_ACCOUNT.format}`,
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/view/202?phase=20150821084&agentId=5621',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                let tmp = JSON.parse(str);
                let parseNew = tmp.data[0];
                let openTime = tmp.data[0].opentime;
                let parseResult = {};
                parseResult.number = parseNew.expect;
                parseResult.time = parseNew.opentime;
                parseResult.expect = parseNew.expect;
                parseResult.data = parseApiPlusNumber(tmp.data[0].opencode, 5);
                parseResult.type = this.type_id;
                return parseResult;
            } catch (err) {
                throw(this.title, err);
            }
        },
    },

    //#加拿大卑斯快乐8
    {
        title: '加拿大卑斯快乐8',
        source: 'apius',
        name: 'apius_jndkl8',
        enable: true,
        type_id: 121,
        timer: 'apius_jndkl8',
        option: {
            host: 'a.apiplus.net',
            timeout: 3000,
            path: `/newly.do?token=${APIPLUS_ACCOUNT.token}&code=cakeno&format=${APIPLUS_ACCOUNT.format}`,
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/view/202?phase=20150821084&agentId=5621',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                let tmp = JSON.parse(str);
                let parseNew = tmp.data[0];
                let openDate = tmp.data[0].opentime;
                let parseResult = {};
                //解析冰果的期数
                let start_time = moment().format("YYYY-MM-DD") + " 00:03:00";//第一期开奖+30秒延迟
                let do_time = moment().format("YYYY-MM-DD") + " 09:32:00";//后一波,第一期

                let mCount = 325;
                let nCount = 43;

                let date = new Date();
                let up = 210;
                //7:00pm到8:00pm
                let nowTime = parseInt(Date.parse(openDate) / 1000) - parseInt(Date.parse(start_time) / 1000);
                if (nowTime >= 0) {
                    let nowCount = Math.floor(nowTime / up);
                    nowCount += 1;
                    if (nowCount <= count) {
                        parseResult.number = moment().format("YYYYMMDD") + nowCount;
                    }

                }
                parseResult.time = parseNew.opentime;
                parseResult.expect = parseNew.expect;
                parseResult.data = parseApiPlusNumber(tmp.data[0].opencode, 5);
                parseResult.type = this.type_id;
                return parseResult;
            }
            catch (err) {
                throw(this.title, err);
            }
        },
    },
    //#台湾bingo
    
    //6合彩
	{
        title:'香港6合彩',
        source:'AOB采集',
        name:'apius_xg6hc',
        enable:true,
        timer:'apius_xg6hc',
        type_id: 122,
        option:{

        host:"www.gjjskf.com",
		path: '/lhc.php',
        timeout:5000,
        headers:{
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },

        parse:function(str){
            try{                                                                                              	//
                str=str.substr(0,200);	                                                                      	//
                var reg=/<row expect="([\d\-]+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/;                   	//
                var m;
                if(m=str.match(reg)){                                                                         	//
                    return {                                                                                  	//
                        type:122,                                                                              	//
                        time:m[3],                                                                            	//
                        number:m[1],                                                                          	//
                        data:m[2]                                                                             	//
                    };                                                                                        	//
                }					                                                                          	//
            }catch(err){                                                                                      	//
                throw('香港6合彩AOB解析数据不正确');                                                            	//
            }
        }
    },
    
	{
		title:'广东11选5',
		source:'AOB采集2',
		name:'gd11x5',
		enable:true,
		timer:'gd11x5', 
		type_id: 6,
		option:{                               
			host:"e.apiplus.net",                                                                        
			timeout:3000,                                                                                   
			path: '/newly.do?token=te4faba9cc864e96bk&code=gd11x5&format=xml',                                                                      
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) " 
			}
		},
		parse:function(str){
			try{
				str=str.substr(0,200);
				var reg=/<row expect="([\d\-]+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/;
				var m;
				if(m=str.match(reg)){
					return {
						type:6,
						time:m[3],
						number:m[1].substring(4),
						data:m[2]
					};
				}
			}catch(err){
				throw('11x5解析数据不正确');
			}
		}
	},
    //#安徽快三
    {
		title:'安徽快3',
		source:'AOB采集',
		name:'ahk3',
		enable:true,
		timer:'ahk3', 
		type_id: 39,
		option:{                               
			host:"jiekou3.68yk.cn",                                                                        
			timeout:3000,                                                                                   
			path: '/dbapi/index.php?id=1000003&key=fa74d191d0ef888963f5f3a33ac013f2&gz=&type=14',                                                                    
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) " 
			}
		},
		parse:function(str){
			try{
				str=str.substr(0,200);
				var reg=/<row expect="([\d\-]+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/;
				var m;
				if(m=str.match(reg)){
					return {
						type:39,
						time:m[3],
						number:m[1],
						data:m[2]
					};
				}
			}catch(err){
				throw('安徽快3解析数据不正确');
			}
		}
	},
    //#内蒙古快三
    {
        title: '内蒙古快三',
        source: 'apius',
        name: 'apius_nmgk3',
        enable: true,
        type_id: 51,
        timer: 'apius_nmgk3',
        option: {
            host: 'a.apiplus.net',
            timeout: 3000,
            path: `/newly.do?token=${APIPLUS_ACCOUNT.token}&code=nmgk3&format=${APIPLUS_ACCOUNT.format}`,
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/view/202?phase=20150821084&agentId=5621',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                var tmp = JSON.parse(str);
                return {
                    time: tmp.data[0].opentime,
                    type: this.type_id,
                    data: tmp.data[0].opencode,
                    number: tmp.data[0].expect,
                    expect: tmp.data[0].expect,
                };
            } catch (err) {
                throw(this.title);
            }
        },
    },
    /*http://face.apius.cn/***************END*/

    //#region SPIDER
    //腾讯时时彩
    {
        title:'腾讯时时彩+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++',
        source:'AOB采集',
        name:'txssc',
        enable:true,
        timer:'txssc',
        type_id: 69,
        option:{

        host:"jiekou3.68yk.cn",
		path: '/dbapi/index.php?gz=0&type=23',
        timeout:3000,
        headers:{
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },

        parse:function(str){
            try{                                                                                              	//
                str=str.substr(0,200);	                                                                      	//
                var reg=/<row expect="([\d\-]+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/;                   	//
                var m;
                if(m=str.match(reg)){                                                                         	//
                    return {                                                                                  	//
                        type:69,                                                                              	//
                        time:m[3],                                                                            	//
                        number:m[1],                                                                          	//
                        data:m[2]                                                                             	//
                    };                                                                                        	//
                }					                                                                          	//
            }catch(err){                                                                                      	//
                throw('腾讯时时彩AOB解析数据不正确**************************************************************************************************************');                                                            	//
            }
        }
    },

    //新西兰45秒彩  OK
    {
        title: '新西兰45秒彩',
        source: 'Spider',
        name: 'spider_xxl45mc',
        enable: false,
        type_id: 123,
        timer: 'spider_xxl45mc',
        option: {
            host: "nz-lotto.nz",
            timeout: 5000,
            path: '/keno/search_result_list',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)",
                "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
                "Host": "nz-lotto.nz",
            }
        },
        parse: function (str) {
            try {
                let data = JSON.parse(str);
                let current = data.data[0];
                let openCodes = parseApiPlusNumber(current.num, 5);
                let time = current.time;
                time = moment(time, "YYYY-MM-DDHH:mm:ss").unix();
                time -= (60 * 60 * 4);
                //时间差4个小时
                //中国时间
                return {
                    type: this.type_id,
                    time: time,
                    number: current.issue,
                    data: openCodes,
                    expect: current.issue,
                };
            } catch (err) {
                throw(this.title, err);
            }
        }
    },

    //韩国1.5 960期官方采集
    {
        title: '韩国1.5分彩',
        source: 'Spider',
        name: 'Spider_hg15fc',
        enable: true,
        type_id: 127,
        timer: 'Spider_hg15fc',
        option: {
            host: 'krlotto.com',
            timeout: 3000,
            path: `/results/` + moment().format("YYYY-MM-DD") + ".json",
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/view/202?phase=20150821084&agentId=5621',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                let tmp = JSON.parse(str);
                let parseNew = tmp[0];
                let parseResult = {};
                parseResult.number = moment().format("YYYYMMDD0") + parseNew.index;
                parseResult.time = parseNew.time;
                parseResult.data = parseApiPlusNumber(parseNew.code, 5);
                parseResult.type = this.type_id;
                parseResult.expect = parseNew.issue;
                return parseResult;
            } catch (err) {
                throw(this.title, err);
            }
        },
    },
	{
        title: '台湾时时彩',
        source: 'Node System API',
        name: 'Spider_twbingo',
        enable: false,
        timer: 'Spider_twbingo',
        type_id: 120,
        option: {
            host: "127.0.0.1",
            port: 8081,
            path: '/open?id=120',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) "
            }
        },
        parse: function (str) {
            try {
                return parseSystemOpenCp(str, this.type_id);
            } catch (err) {
                throw(this.title, err);
            }
        }
    },
    //#endregion


    //#region **外彩采集**/
    {
        title: '吉林快3',
        source: '百度乐彩',
        name: 'jlk3',
        enable: false,
        timer: 'jlk3',
        option: {
            host: "baidu.lecai.com",
            timeout: 30000,
            path: '/lottery/draw/view/560?phase=150729051&agentId=5563',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },

        parse: function (str) {
            try {
                var exp_data = /var latest_draw_result = {"red":\[([0-9\[\]\,\s"]+)\]/;
                var exp_phase = /var latest_draw_phase = '(\d+)';/;
                var exp_time = /var latest_draw_time = '([0-9\-\:\s]+)';/;
                var m_data = str.match(exp_data);
                var m_phase = str.match(exp_phase);
                var m_time = str.match(exp_time);
                if (m_data && m_phase && m_time) {
                    return {
                        type: 30,
                        time: m_time[1],
                        number: '20' + m_phase[1],
                        data: m_data[1].replace(/"/g, '')
                    };
                }
            } catch (err) {
                throw('吉林快3解析数据不正确');
            }
        }
    },

    //#endregion **外彩采集**/


    {
        title: '新疆时时彩',
        source: '新疆福利彩票网',
        name: 'xjssc',
        enable: false,
        timer: 'xjssc',
        option: {
            host: "www.xjflcp.com",
            timeout: 30000,
            path: '/ssc/',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },
        parse: function (str) {
            try {
                return getFromXJFLCPWeb(str, 12);
            } catch (err) {
                throw('新疆时时彩解析数据不正确');
            }
        }
    },


    //#region 排列彩种  START

    //
    //OK
    {
        title: '排列3',
        source: '百度乐彩',
        name: 'pl3_baidu',
        enable: false,
        timer: 'pl3_baidu',
        type_id: 10,
        option: {
            host: 'baidu.lecai.com',
            timeout: 30000,
            path: '/lottery/ajax_latestdrawn.php?lottery_type=3',
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/sorts/cqssc.php?phase=20150821112&agentId=5591',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                var data = JSON.parse(str);
                if (typeof data.data[0].result.result[0].data === 'object') {
                    var time = data.data[0].time_endticket;
                    var number = '20' + data.data[0].phase;
                    var data = data.data[0].result.result[0].data.join(',');
                    return {
                        type: 10,
                        time: time,
                        number: number,
                        data: data,
                    };
                }
            } catch (err) {
                throw(this.title, err);
            }
        },
    },
    {
        title: '排列3',
        source: '500万彩票网',
        name: 'pl3_360',
        enable: false,
        timer: 'pl3_360',
        type_id: 10,
        option: {
            host: "www.500wan.com",
            timeout: 30000,
            path: '/static/info/kaijiang/xml/pls/list10.xml',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },

        parse: function (str) {
            try {
                str = str.substr(0, 300);
                var m;
                var reg = /<row expect="(\d+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/;
                if (m = str.match(reg)) {
                    return {
                        type: this.type_id,
                        time: m[3],
                        number: 20 + m[1],
                        data: m[2]
                    };
                }
            } catch (err) {
                throw(this.title + "解析不正确");
            }
        }
    },


    {
        title: '排列5_百度',
        source: '百度乐彩',
        name: 'pl5_baidu',
        enable: false,
        timer: 'pl5_baidu',
        type_id: 53,
        option: {
            host: 'baidu.lecai.com',
            timeout: 30000,
            path: '/lottery/ajax_latestdrawn.php?lottery_type=4',
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/sorts/cqssc.php?phase=20150821112&agentId=5591',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                var data = JSON.parse(str);
                if (typeof data.data[0].result.result[0].data === 'object') {
                    var time = data.data[0].time_endticket;
                    var number = '20' + data.data[0].phase;
                    var data = data.data[0].result.result[0].data.join(',');
                    return {
                        type: 53,
                        time: time,
                        number: number,
                        data: data,
                    };
                }
            } catch (err) {
                throw(this.title, err);
            }
        },
    },
    {
        title: '排列5',
        source: '500万彩票网',
        name: 'plw_500wan',
        enable: false,
        timer: 'plw_500wan',
        type_id: 53,
        option: {
            host: "www.500wan.com",
            timeout: 30000,
            path: '/static/info/kaijiang/xml/plw/list10.xml',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },
        parse: function (str) {
            try {
                str = str.substr(0, 300);
                var m;
                var reg = /<row expect="(\d+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/;
                if (m = str.match(reg)) {
                    return {
                        type: this.type_id,
                        time: m[3],
                        number: 20 + m[1],
                        data: m[2]
                    };
                }
            } catch (err) {
                throw('排3解析数据不正确');
            }
        }
    },


    //OK
    {
        title: '福彩3D_百度',
        source: '百度乐彩',
        name: 'fc3d_baidu',
        enable: false,
        timer: 'fc3d_baidu',
        type_id: 9,
        option: {
            host: 'baidu.lecai.com',
            timeout: 30000,
            path: '/lottery/ajax_latestdrawn.php?lottery_type=52',
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/sorts/cqssc.php?phase=20150821112&agentId=5591',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                var data = JSON.parse(str);
                if (typeof data.data[0].result.result[0].data === 'object') {
                    var time = data.data[0].time_endticket;
                    var number = data.data[0].phase;
                    var data = data.data[0].result.result[0].data.join(',');
                    return {
                        type: 9,
                        time: time,
                        number: number,
                        data: data,
                    };
                }
            } catch (err) {
                throw(this.title, err);
            }
        },
    },
    {
        title: '福彩3D',
        source: '500万彩票网',
        name: 'fc3d_500wan',
        enable: false,
        timer: 'fc3d',
        type_id: 9,
        option: {
            host: "www.500wan.com",
            timeout: 30000,
            path: '/static/info/kaijiang/xml/sd/list10.xml',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },
        parse: function (str) {
            try {
                str = str.substr(0, 300);
                var m;
                var reg = /<row expect="(\d+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)" trycode="[\d\,]*?" tryinfo="" \/>/;
                if (m = str.match(reg)) {
                    return {
                        type: 9,
                        time: m[3],
                        number: m[1],
                        data: m[2]
                    };
                }
            } catch (err) {
                throw('福彩3D解析数据不正确');
            }
        }
    },

    //#endregion其他彩种  END

    //OK
    {
        title: '江苏快3',
        source: '360彩票',
        name: 'jsk3_360',
        enable: false,
        timer: 'jsk3_360',
        type_id: 25,
        option: {
            host: "cp.360.cn",
            timeout: 30000,
            path: '/k3js/',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },
        parse: function (str) {
            try {
                let data = getFrom360CPK3(str, 25);
                return data;
            } catch (err) {
                throw('江苏快3解析数据不正确');
            }
        }
    },
    //OK
    {
        title: '湖北快3',
        source: '360彩票',
        name: 'k3hb_360',
        enable: false,
        timer: 'k3hb_360',
        type_id: 50,
        option: {
            host: "cp.360.cn",
            timeout: 30000,
            path: '/k3hb/',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },
        parse: function (str) {
            try {
                let data = getFrom360CPK3(str, 50);
                return data;
            } catch (err) {
                throw('湖北快3解析数据不正确');
            }
        }
    },

    //辽宁11x5  OK
    {
        title: '辽宁11选5',
        source: '360彩票',
        name: 'ln11x5_360',
        enable: false,
        timer: 'ln11x5_360',
        type_id: 23,
        option: {
            host: "cp.360.cn",
            timeout: 30000,
            path: '/ln11/',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },
        parse: function (str) {
            try {
                return getFrom360CP(str, 23);
            } catch (err) {
                throw('辽宁11选5解析数据不正确');
            }
        }
    },


    

    //OK
    {
        title: '江西11选5',
        source: '百度乐彩',
        name: 'jx11x5_baidu',
        enable: false,
        timer: 'jx11x5_baidu',
        type_id: 16,
        option: {
            host: "baidu.lecai.com",
            timeout: 30000,
            path: '/lottery/draw/view/22?phase=2015082464&agentId=5563',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },
        parse: function (str) {
            try {
                var exp_data = /var latest_draw_result = {"red":\[([0-9\[\]\,\s"]+)\]/;
                var exp_phase = /var latest_draw_phase = '(\d+)';/;
                var exp_time = /var latest_draw_time = '([0-9\-\:\s]+)';/;
                var m_data = str.match(exp_data);
                var m_phase = str.match(exp_phase);
                var m_time = str.match(exp_time);
                if (m_data && m_phase && m_time) {
                    return {
                        type: 16,
                        time: m_time[1],
                        number: m_phase[1],
                        data: m_data[1].replace(/"/g, '')
                    };
                }
            } catch (err) {
                throw('江西11选5解析数据不正确');
            }
        }
    },
    {
        title: '江西11选5',
        source: '360彩票',
        name: 'jx11x5_360',
        enable: false,
        timer: 'jx11x5_360',
        type_id: 16,
        option: {
            host: "cp.360.cn",
            timeout: 30000,
            path: '/dlcjx/',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
            }
        },
        parse: function (str) {
            try {
                return getFrom360CP(str, 16);
            } catch (err) {
                throw('江西11选5解析数据不正确');
            }
        }
    },

    //OK
    {
        title: '山东11选5',
        source: '360彩票网',
        name: 'sd11x5_360',
        enable: false,
        timer: 'sd11x5_360',
        type_id: 7,
        option: {
            host: "cp.360.cn",
            timeout: 30000,
            path: '/yun11/',
            headers: {
                "User-Agent": "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; Sleipnir/2.9.8) "
            }
        },
        parse: function (str) {
            try {
                return getFrom360sd11x5(str, 7);
            } catch (err) {
                throw('山东11选5解析数据不正确');
            }
        }
    },
    {
        title: '山东11选5',
        source: '百度乐彩',
        name: 'sd11x5_baidu',
        enable: false,
        timer: 'sd11x5_baidu',
        type_id: 7,
        option: {
            host: "baidu.lecai.com",
            timeout: 30000,
            path: '/lottery/ajax_latestdrawn.php?lottery_type=20',
            headers: {
                'Accept': 'application/json, text/javascript, */*; q=0.01',
                'Referer': 'http://baidu.lecai.com/lottery/draw/view/20?phase=15082465&agentId=5622',
                'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)',
                'X-Requested-With': 'XMLHttpRequest',
            },
        },
        parse: function (str) {
            try {
                var data = JSON.parse(str);
                if (typeof data.data[0].result.result[0].data === 'object') {
                    var time = data.data[0].time_endticket;
                    var number = data.data[0].phase;
                    var data = data.data[0].result.result[0].data.join(',');
                    return {
                        type: 7,
                        time: time,
                        number: number.substr(0, 2) !== '20' ? '20' + number : number,
                        data: data,
                    };
                }
            } catch (err) {
                throw('山东11选5解析数据不正确');
            }
        }
    },

    //OK,时间有问题
    {
		title:'北京PK10',
		source:'AOB采集',
		name:'bjpk10',
		enable:true,
		timer:'bjpk10',
		type_id: 20,
		option:{                               
			host:"e.apiplus.net",                                                                     
			timeout:3000,                                                                                   
			path: '/newly.do?token=te4faba9cc864e96bk&code=bjpk10&format=xml',                                                                      
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) " 
			}
		},
		parse:function(str){
			
			try{
				str=str.substr(0,200);
				var reg=/<row expect="([\d\-]+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/;
				var m;
				if(m=str.match(reg)){
					return {
						type:20,
						time:m[3],
						number:m[1],
						data:m[2]
					};
				}
			}catch(err){
				throw('北京PK10AOB解析数据不正确');
			}
		}
	},

    //#region系统彩票，自动产生数据
    {
        title: '11选5',
        source: 'Node System API',
        name: 'system_11x5',
        enable: false,
        timer: 'system_11x5',
        type_id: 29,
        option: {
            host: "127.0.0.1",
            port: 8081,
            path: '/open?id=29',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) "
            }
        },
        parse: function (str) {
            try {
                return parseSystemOpenCp(str, this.type_id);
            } catch (err) {
                throw('五分彩解析数据不正确');
            }
        }
    },
    {
        title: '全天快三',
        source: 'Node System API',
        name: 'system_qtks',
        enable: false,
        timer: 'system_qtks',
        type_id: 60,
        option: {
            host: "127.0.0.1",
            port: 8081,
            path: '/open?id=60',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) "
            }
        },
        parse: function (str) {
            try {
                return parseSystemOpenCp(str, 60);
            } catch (err) {
                throw('全天快三解析数据不正确');
            }
        }
    },
    /*{
        title: '假韩国1.5',
        source: 'Node System API',
        name: 'system_jhg1d5fc',
        enable: false,
        timer: 'system_jhg1d5fc',
        type_id: 126,
        option: {
            host: "127.0.0.1",
            port: 8081,
            path: '/open?id=126',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) "
            }
        },
        parse: function (str) {
            try {
                return parseSystemOpenCp(str, this.type_id);
            } catch (err) {
                throw(this.title, err);
            }
        }
    },*/
	{
        title: '假韩国1.5',
        source: 'Node System API',
        name: 'system_jhg1d5fc',
        enable: false,
        timer: 'system_jhg1d5fc',
        type_id: 126,
        option: {
            host: "127.0.0.1",
            port: 8081,
            path: '/open?id=126',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) "
            }
        },
        parse: function (str) {
            try {
                return parseSystemOpenCp(str, this.type_id);
            } catch (err) {
                throw(this.title, err);
            }
        }
    },
    {
        title: '五分彩',
        source: 'Node System API',
        name: 'system_wfc',
        enable: false,
        timer: 'system_wfc',
        type_id: 14,
        option: {
            host: "127.0.0.1",
            port: 8081,
            path: '/open?id=14',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) "
            }
        },
        parse: function (str) {
            try {
                return parseSystemOpenCp(str, 14);
            } catch (err) {
                throw('五分彩解析数据不正确');
            }
        }
    },
    {
        title: '二分彩',
        source: 'Node System API',
        name: 'system_efc',
        enable: false,
        timer: 'system_efc',
        type_id: 26,
        option: {
            host: "127.0.0.1",
            port: 8081,
            path: '/open?id=26',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) "
            }
        },
        parse: function (str) {
            try {
                return parseSystemOpenCp(str, 26);
            } catch (err) {
                throw('二分彩解析数据不正确');
            }
        }
    },
    {
        title: '分分彩',
        source: 'Node System API',
        name: 'system_ffc',
        enable: false,
        timer: 'system_ffc',
        type_id: 5,
        option: {
            host: "127.0.0.2",
            port: 8081,
            path: '/open?id=5',
            headers: {
                "User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0) "
            }
        },
        parse: function (str) {
            try {
                //这个地方我其实可以自己生成CP数据做一个随机数字就可以了
                return parseSystemOpenCp(str, 5);
            } catch (err) {
                throw('分分彩解析数据不正确');
            }
        }
    }
    //#endregion

];

//配置是否开启
cp_types.forEach(function (item) {
    let tmp = enable_types.includes(item.type_id);
    if (tmp) {
        item.enable = true;
    } else {
        item.enable = false;
    }
});

exports.cp = cp_types;
exports.dbinfo = mysql_conf;
// 出错时等待 15
exports.errorSleepTime = 3000;

global.log = function (log) {
    let date = new Date();
    console.log(log);
};


//////////////////////////////////////////////////////////////////////parse functions

function parseApiPlusXGLHCNumber(code) {
    let codes = code.split(",");
    if (codes[5].indexOf("+") >= 0) {
        let spiarr = codes[5].split('+');
        codes[5] = spiarr[0];
        codes[6] = spiarr[1];
    }
    return codes.join(",");
}
//解析apiplus数据
function parseApiPlusNumber(code, count) {
    let codes = code.split(",");
    let result = [];
    switch (codes.length) {
        case 20:
            if (codes[19].indexOf("+") >= 0) {
                codes[19] = codes[19].split('+')[0];
            }
            //排序，小到大
            codes.sort(function (a, b) {
                return parseInt(a) - parseInt(b);
            });
            for (let i = 0; i < codes.length; i += 4) {
                //数据相加
                let res = (parseInt(codes[i]) + parseInt(codes[i + 1]) + parseInt(codes[i + 2]) + parseInt(codes[i + 3])).toString();
                res = res.substr(res.length - 1, 1);
                result.push(res);
            }
            break;
        default:
            return null;
            break;
    }
    return result.join(",");
}

//解析nodejs系统菜
function parseSystemOpenCp(str, type) {
    let data = JSON.parse(str);
    if (data.type_id !== type) {
        throw new Exception("parse err");
    }
    let date = new Date();
    let openIssue = (parseInt(data.open_issue) + 10000).toString();
    openIssue = openIssue.substr(1, openIssue.length - 1);
    let openNo = moment().format("YYYYMMDD") + "" + openIssue;
    //replace(/-/g, '') + openIssue;
    //自己组装日期
    return {
        type: type,
        time: data.open_date,
        number: openNo,
        data: data.open_code,
    };
}

function getFromXJFLCPWeb(str, type) {
    str = str.substr(str.indexOf('<td><a href="javascript:detatilssc'), 300).replace(/[\r\n]+/g, '');

    var reg = /(\d{10}).+(\d{2}\:\d{2}).+<p>([\d ]{9})<\/p>/,
        match = str.match(reg);

    if (!match) throw new Error('数据不正确');
    //console.log('期号：%s，开奖时间：%s，开奖数据：%s', match[1], match[2], match[3]);

    try {
        var data = {
            type: type,
            time: match[1].replace(/^(\d{4})(\d{2})(\d{2})\d{2}/, '$1-$2-$3 ') + match[2],
            number: match[1].replace(/^(\d{8})(\d{2})$/, '$10$2'),
            data: match[3].split(' ').join(',')
        };
        //console.log(data);
        return data;
    } catch (err) {
        throw('解析数据失败');
    }
}


function getFromCaileleWeb(str, type, slen) {
    if (!slen) slen = 380;
    str = str.substr(str.indexOf('<tr bgcolor="#FFFAF3">'), slen);
    //console.log(str);
    var reg = /<td.*?>(\d+)<\/td>[\s\S]*?<td.*?>([\d\- \:]+)<\/td>[\s\S]*?<td.*?>((?:[\s\S]*?<div class="ball_yellow">\d+<\/div>){3,5})\s*<\/td>/,
        match = str.match(reg);
    if (match.length > 1) {

        if (match[1].length == 7) match[1] = '2016' + match[1].replace(/(\d{4})(\d{3})/, '$1-$2');
        if (match[1].length == 8) {
            if (parseInt(type) != 11) {
                match[1] = '20' + match[1].replace(/(\d{6})(\d{2})/, '$1-0$2');
            } else {
                match[1] = '20' + match[1].replace(/(\d{6})(\d{2})/, '$1-$2');
            }
        }
        if (match[1].length == 9) match[1] = '20' + match[1].replace(/(\d{6})(\d{2})/, '$1-$2');
        if (match[1].length == 10) match[1] = match[1].replace(/(\d{8})(\d{2})/, '$1-0$2');
        var mynumber = match[1].replace(/(\d{8})(\d{3})/, '$1$2');
        try {
            var data = {
                type: type,
                time: match[2],
                number: mynumber
            }

            reg = /<div.*>(\d+)<\/div>/g;
            data.data = match[3].match(reg).map(function (v) {
                var reg = /<div.*>(\d+)<\/div>/;
                return v.match(reg)[1];
            }).join(',');

            //console.log(data);
            return data;
        } catch (err) {
            throw('解析数据失败');
        }
    }
}

function getFrom360CP(str, type) {
    str = str.substr(str.indexOf('<em class="red" id="open_issue">'), 380);
    //console.log(str);
    var reg = /[\s\S]*?(\d+)<\/em>[\s\S].*?<ul id="open_code_list">((?:[\s\S]*?<li class=".*?">\d+<\/li>){3,5})[\s\S]*?<\/ul>/,
        match = str.match(reg);
    var myDate = new Date();
    var year = myDate.getFullYear();       //年
    var month = myDate.getMonth() + 1;     //月
    var day = myDate.getDate();            //日
    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;
    var mytime = year + "-" + month + "-" + day + " " + myDate.toLocaleTimeString();
    //console.log(match);
    if (match.length > 1) {
        if (match[1].length == 7) match[1] = year + match[1].replace(/(\d{8})(\d{3})/, '$1$2');
        if (match[1].length == 6) match[1] = year + match[1].replace(/(\d{4})(\d{2})/, '$1$2');
        if (match[1].length == 9) match[1] = '20' + match[1].replace(/(\d{6})(\d{2})/, '$1$2');
        if (match[1].length == 10) match[1] = match[1].replace(/(\d{8})(\d{2})/, '$1$2');
        var mynumber = match[1].replace(/(\d{8})(\d{3})/, '$1$2');

        try {
            var data = {
                type: type,
                time: mytime,
                number: mynumber
            }

            reg = /<li class=".*?">(\d+)<\/li>/g;
            data.data = match[2].match(reg).map(function (v) {
                var reg = /<li class=".*?">(\d+)<\/li>/;
                return v.match(reg)[1];
            }).join(',');

            //console.log(data);
            return data;
        } catch (err) {
            throw('解析数据失败');
        }
    }
}

function getFrom360CPK3(str, type) {

    str = str.substr(str.indexOf('<em class="red" id="open_issue">'), 380);
    //console.log(str);
    var reg = /[\s\S]*?(\d+)<\/em>[\s\S].*?<ul id="open_code_list">((?:[\s\S]*?<li class=".*?">\d+<\/li>){3,5})[\s\S]*?<\/ul>/,
        match = str.match(reg);
    var myDate = new Date();
    var year = myDate.getFullYear();       //年
    var month = myDate.getMonth() + 1;     //月
    var day = myDate.getDate();            //日
    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;
    var mytime = year + "-" + month + "-" + day + " " + myDate.toLocaleTimeString();
    //console.log(match);
    match[1] = match[1].replace(/(\d{4})(\d{2})/, '$10$2');

    try {
        var data = {
            type: type,
            time: mytime,
            number: year + match[1]
        };
        reg = /<li class=".*?">(\d+)<\/li>/g;
        data.data = match[2].match(reg).map(function (v) {
            var reg = /<li class=".*?">(\d+)<\/li>/;
            return v.match(reg)[1];
        }).join(',');
        return data;
    } catch (err) {
        throw('解析数据失败');
    }
}

function getFromPK10(str, type) {
    str = str.substr(str.indexOf('<td class="winnumLeft">'), 350).replace(/[\r\n]+/g, '');
    var reg = /<td class=".*?">(\d+)<\/td>[\s\S]*?<td>(.*)<\/td>[\s\S]*?<td class=".*?">([\d\:\- ]+?)<\/td>[\s\S]*?<\/tr>/,
        match = str.match(reg);
    if (!match) throw new Error('数据不正确');
    var myDate = new Date();
    var year = myDate.getFullYear();
    var mytime = year + "-" + match[3];
    try {
        var data = {
            type: type,
            time: mytime,
            number: match[1],
            data: match[2]
        };
        return data;
    } catch (err) {
        throw('解析数据失败');
    }

}

function getFromK8(str, type) {

    str = str.substr(str.indexOf('<div class="lott_cont">'), 450).replace(/[\r\n]+/g, '');
    //console.log(str);
    var reg = /<tr class=".*?">[\s\S]*?<td>(\d+)<\/td>[\s\S]*?<td>(.*)<\/td>[\s\S]*?<td>(.*)<\/td>[\s\S]*?<td>([\d\:\- ]+?)<\/td>[\s\S]*?<\/tr>/,
        match = str.match(reg);
    if (!match) throw new Error('数据不正确');
    //console.log(match);
    try {
        var data = {
            type: type,
            time: match[4],
            number: match[1],
            data: match[2] + '|' + match[3]
        };
        //console.log(data);
        return data;
    } catch (err) {
        throw('解析数据失败');
    }

}


function getFromCJCPWeb(str, type) {

    //console.log(str);
    str = str.substr(str.indexOf('<table class="qgkj_table">'), 1200);

    //console.log(str);

    var reg = /<tr>[\s\S]*?<td class=".*">(\d+).*?<\/td>[\s\S]*?<td class=".*">([\d\- \:]+)<\/td>[\s\S]*?<td class=".*">((?:[\s\S]*?<input type="button" value="\d+" class=".*?" \/>){3,5})[\s\S]*?<\/td>/,
        match = str.match(reg);

    //console.log(match);

    if (!match) throw new Error('数据不正确');
    try {
        var data = {
            type: type,
            time: match[2],
            number: match[1].replace(/(\d{8})(\d{2})/, '$10$2')
        }

        reg = /<input type="button" value="(\d+)" class=".*?" \/>/g;
        data.data = match[3].match(reg).map(function (v) {
            var reg = /<input type="button" value="(\d+)" class=".*?" \/>/;
            return v.match(reg)[1];
        }).join(',');

        //console.log(data);
        return data;
    } catch (err) {
        throw('解析数据失败');
    }

}

function getFromCaileleWeb_1(str, type) {
    str = str.substr(str.indexOf('<tbody id="openPanel">'), 120).replace(/[\r\n]+/g, '');

    var reg = /<tr.*?>[\s\S]*?<td.*?>(\d+)<\/td>[\s\S]*?<td.*?>([\d\:\- ]+?)<\/td>[\s\S]*?<td.*?>([\d\,]+?)<\/td>[\s\S]*?<\/tr>/,
        match = str.match(reg);
    if (!match) throw new Error('数据不正确');
    //console.log(match);
    var number, _number, number2;
    var d = new Date();
    var y = d.getFullYear();
    if (match[1].length == 9 || match[1].length == 8) {
        number = '20' + match[1];
    } else if (match[1].length == 7) {
        number = '2016' + match[1];
    } else {
        number = match[1];
    }
    _number = number;
    if (number.length == 11) {
        number2 = number.replace(/^(\d{8})(\d{3})$/, '$1$2');
    } else {
        number2 = number.replace(/^(\d{8})(\d{2})$/, '$1-0$2');
        _number = number.replace(/^(\d{8})(\d{2})$/, '$10$2');
    }
    try {
        var data = {
            type: type,
            time: _number.replace(/^(\d{4})(\d{2})(\d{2})\d{3}/, '$1-$2-$3 ') + match[2],
            number: number2,
            data: match[3]
        };
        //console.log(data);
        return data;
    } catch (err) {
        throw('解析数据失败');
    }
}

function getFrom360sd11x5(str, type) {

    str = str.substr(str.indexOf('<em class="red" id="open_issue">'), 380);
    //console.log(str);
    var reg = /[\s\S]*?(\d+)<\/em>[\s\S].*?<ul id="open_code_list">((?:[\s\S]*?<li class=".*?">\d+<\/li>){3,5})[\s\S]*?<\/ul>/,
        match = str.match(reg);
    var myDate = new Date();
    var year = myDate.getFullYear();       //年
    var month = myDate.getMonth() + 1;     //月
    var day = myDate.getDate();            //日
    if (month < 10) month = "0" + month;
    if (day < 10) day = "0" + day;
    var mytime = year + "-" + month + "-" + day + " " + myDate.toLocaleTimeString();
    //console.log(mytime);
    //console.log(match);

    if (!match) throw new Error('数据不正确');
    try {
        var data = {
            type: type,
            time: mytime,
            number: year + match[1].replace(/(\d{4})(\d{2})/, '$1$2')
        }

        reg = /<li class=".*?">(\d+)<\/li>/g;
        data.data = match[2].match(reg).map(function (v) {
            var reg = /<li class=".*?">(\d+)<\/li>/;
            return v.match(reg)[1];
        }).join(',');

        //console.log(data);
        return data;
    } catch (err) {
        throw('解析数据失败');
    }
}

function getFromCaileleWeb_2(str, type) {

    str = str.substr(str.indexOf('<tbody id="openPanel">'), 500).replace(/[\r\n]+/g, '');
    //console.log(str);
    var reg = /<tr>[\s\S]*?<td>(\d+)<\/td>[\s\S]*?<td>([\d\:\- ]+?)<\/td>[\s\S]*?<td>([\d\,]+?)<\/td>[\s\S]*?<\/tr>/,
        match = str.match(reg);
    if (!match) throw new Error('数据不正确');
    //console.log(match);
    var number, _number, number2;
    var d = new Date();
    var y = d.getFullYear();
    if (match[1].length == 9 || match[1].length == 8) {
        number = '20' + match[1];
    } else if (match[1].length == 7) {
        number = '2016' + match[1];
    } else {
        number = match[1];
    }
    _number = number;
    if (number.length == 11) {
        number2 = number.replace(/^(\d{8})(\d{3})$/, '$1$2');
    } else {
        number2 = number.replace(/^(\d{8})(\d{2})$/, '$10$2');
        _number = number.replace(/^(\d{8})(\d{2})$/, '$10$2');
    }
    try {
        var data = {
            type: type,
            time: _number.replace(/^(\d{4})(\d{2})(\d{2})\d{3}/, '$1-$2-$3 ') + match[2],
            number: number2,
            data: match[3]
        };
        //console.log(data);
        return data;
    } catch (err) {
        throw('解析数据失败');
    }
}