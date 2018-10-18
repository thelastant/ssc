var settingHelper = {
    setTabIndex: function (index,yx) {
		if(index=="0"){
			//alert("0");
			$("nav#bottom-bar").show();
		}else if(index=="1"){
			$("nav#bottom-bar").hide();//隐藏
		}else if(index=="2"){
			$("nav#bottom-bar").hide();//隐藏
		}else if(index=="3"){
			$("nav#bottom-bar").show();
		}
		
		if(yx=="y"){
			$("nav#bottom-bar").hide();//隐藏
		}else if(yx=="x"){
			$("nav#bottom-bar").show();
		}
        $(".mui-bar .mui-tab-item").removeClass("mui-active");
        $(".mui-bar .mui-tab-item").eq(index).addClass("mui-active");
    }
};
//#region  设置HJs
phonon.options({
    navigator: {
        defaultPage: 'home',
        animatePages: true,
        enableBrowserBackButton: true,
    },
    i18n: null // for this example, we do not use internationalization
});
var app = phonon.navigator();
app.on({page: 'home', preventClose: false, content: null, readyDelay: 1}, function (activity) {
    activity.onCreate(function () {
        $("#bottom-bar").hide();
    });
    activity.onReady(function () {
        $("#bottom-bar").show();
        settingHelper.setTabIndex(0);
    });
});

app.on({page: 'cp_game', preventClose: false}, function (activity) {
    activity.onReady(function () {
        settingHelper.setTabIndex(1);
    });
    activity.onHashChanged(function (router) {
        console.log(router);
        var url = "/game/" + router + "&client_type=mobile";
        $("cp_game").load(url, "POST");
        $("#bottom-bar").hide();//隐藏
    });
});

//Cp类型
app.on({page: 'cp_type_list', content: "/pages/show_page?page=type_list"}, function (activity) {
    activity.onReady(function () {
        settingHelper.setTabIndex(1);
        $("#bottom-bar").show();
    });
});

//通知
app.on({page: 'notice_page', content: "/pages/show_page?page=notice_page"}, function (activity) {
    activity.onReady(function () {
        settingHelper.setTabIndex(1);
        $("#bottom-bar").hide();
    });
});

//账户变动
app.on({page: 'user_finance_logs', content: "/pages/show_page?page=user_finance_logs"}, function (activity) {
    activity.onReady(function () {
        settingHelper.setTabIndex(3);
        $("#bottom-bar").hide();//隐藏
    });
});

//投注记录
app.on({page: 'user_bets_logs', content: "/pages/show_page?page=show_bets_logs"}, function (activity) {
    activity.onReady(function () {
		settingHelper.setTabIndex(1);
        $("#bottom-bar").hide();//隐藏
    });
});

//用户控制面板
app.on({page: 'user_dashboard', content: "/pages/show_page?page=dashboard"}, function (activity) {
    activity.onReady(function () {
        settingHelper.setTabIndex(3);
        $("#bottom-bar").show();//隐藏
    });
});

//用户设置
app.on({page: 'user_setting_page', content: "/pages/show_page?page=settings"}, function (activity) {
    activity.onReady(function () {
        settingHelper.setTabIndex(3);
    });
});

//游戏记录
app.on({page: 'game_play_logs', content: "/pages/show_page?page=game_play_logs"}, function (activity) {
    activity.onReady(function () {
        settingHelper.setTabIndex(2);
        $("#bottom-bar").hide();//隐藏
    });
});

//开奖中心
app.on({page: 'cp_data_open', content: "/pages/show_page?page=cp_data_open"}, function (activity) {
    activity.onReady(function () {
		settingHelper.setTabIndex(2);
        $("#bottom-bar").hide();
    });
});

//单个开奖日志
app.on({page: 'cp_open_logs',}, function (activity) {
    activity.onHashChanged(function (router) {
        var url = "/game/" + router + "&client_type=mobile";
        $("cp_open_logs").load(url);
    });
    activity.onReady(function () {
        settingHelper.setTabIndex(2);
    });
});

//#endregion

//充值
app.on({page: 'user_pay_in', content: "/pages/show_page?page=user_pay_in"}, function (activity) {
    activity.onReady(function () {
        $("#bottom-bar").hide();
    });
});

//提现
app.on({page: 'user_pay_out', content: "/pages/show_page?page=user_pay_out"}, function (activity) {
    activity.onReady(function () {
        settingHelper.setTabIndex(2);
    });
});

//额度管理
app.on({page: 'user_ed_gl', content: "/pages/show_page?page=user_ed_gl"}, function (activity) {
    activity.onReady(function () {
        settingHelper.setTabIndex(2);
    });
});

//团队管理
app.on({page: 'agent_member_list', content: "/pages/show_page?page=agent_member_list"}, function (activity) {
    activity.onReady(function () {
        $("#bottom-bar").hide();
    });
});

app.on({page: "agent_account", content: "/pages/show_page?page=agent_account"}, function (activity) {
    activity.onReady(function () {
        $("#bottom-bar").hide();
    });
});

app.on({page: "agent_member_money", content: "/pages/show_page?page=agent_member_money"}, function (activity) {
    activity.onReady(function () {
        $("#bottom-bar").hide();
    });
});

app.on({page: "agent_member_bets", content: "/pages/show_page?page=agent_member_bets"}, function (activity) {
    activity.onReady(function () {
        $("#bottom-bar").hide();
    });
});

//客服服务
app.on({page: "kf_service_page", content: "/pages/show_page?page=kf_service_page"}, function (activity) {
    activity.onReady(function () {
        $("#bottom-bar").hide();
    });
});

//账户变动
app.on({page: 'agent_finance_logs', content: "/pages/show_page?page=agent_finance_logs"}, function (activity) {
    activity.onReady(function () {
        settingHelper.setTabIndex(3);
        $("#bottom-bar").hide();//隐藏
    });
});
app.start();
