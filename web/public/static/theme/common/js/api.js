define(function (require, exports, module) {

    exports.API_ROUTES = {
        get_wp_types: "/api/get_wp_types",
        get_index_types: "/api/get_index_types",
        get_coin_logs: "/user/api_user_coin",
        get_bet_logs: "/bet/api_bet_logs",
        get_agent_money: '/agent/api_get_money',
        get_type_list: '/api/get_cp_types',
        get_user_info: '/api/get_user_info',
        get_notice_list: '/sys/api_notice_list',
        get_agent_member: '/agent/api_get_member',
        api_get_qy: "/api/api_get_qy",
        get_agent_finance_logs: "/api/get_agent_finance_logs",
        get_bet_info: "/bet/info",
        remove_single_bet: "/bet/remove_single"
    };
    exports.getTypeIndex = function (callback) {
        $.get(exports.API_ROUTES.get_index_types, function (ret) {
            callback(ret);
        })
    };
    exports.getTypeMB = function (callback) {
        $.get(exports.API_ROUTES.get_wp_types, function (ret) {
            callback(ret);
        })
    };
    exports.COIN_TYPES = {
        55: '注册奖励',
        0: '其他',
        1: '用户充值',
        9: '系统充值',
        54: '充值奖励',

        106: '提现冻结',
        12: '上级转款',
        8: '提现失败返还',
        107: '提现成功扣除',

        201: '契约分红',
        202: '契约分红',

        167: "日结工资",
        51: '绑定银行奖励',
        101: '投注扣款',
        108: '开奖扣除',
        6: '中奖奖金',
        7: '撤单返款',
        102: '追号投注',
        5: '追号撤单',
        11: '合买收单',
        255: '未开奖返还',
        100: '抢庄冻结',
        10: '撤庄返款',
        103: '抢庄返点',
        104: '抢庄抽水',
        105: '抢庄赔付',
        2: '下级返点',
        3: '代理分红',
        52: '充值佣金',
        53: '消费佣金',
        56: '亏损佣金',
        13: '转款给下级',
        50: '签到赠送',
        120: '幸运大转盘',
        121: '积分兑换',
    }
});