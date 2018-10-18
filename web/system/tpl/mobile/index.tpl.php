<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

    <meta name="renderer" content="webkit">

    <title><?php echo $this->config['webName']; ?></title>
    <script>
        window.SYSTEM_SETTINGS = {
            version: "<?php echo $this->version; ?>",
            client_type: "<?php echo $this->client_type; ?>",
        };
        window.GAME_TOTAL = {
            stop: <?php echo $this->config['switchBuy'] == 0 ? 'true' : 'false';?>,
            ban: <?php echo ($this->config['switchDLBuy'] == 0 && $this->user['type']) ? 'true' : 'false';?>,
        };
    </script>

    <!--bootstrap-->
    <link href="<?php echo STATIC_PATH; ?>libs/bootstrap/css/bootstrap.min.css?v=<?php echo $this->version; ?>"
          rel="stylesheet"
          type="text/css"/>

    <link href="<?php echo STATIC_PATH; ?>libs/phonon/css/phonon.min.css?v=<?php echo $this->version; ?>"
          rel="stylesheet"
          type="text/css"/>

    <script src="<?php echo STATIC_PATH . 'libs/seajs/sea.js?v=' ?><?php echo $this->version; ?>"></script>
    <script src="<?php echo STATIC_PATH . 'libs/seajs/sea-plugin.js?v=' ?><?php echo $this->version; ?>"></script>
    <script src="<?php echo THEME_PATH . 'sea-conf.js?v=' ?>"><?php echo $this->version; ?></script>
    <script src="<?php echo STATIC_PATH; ?>libs/phonon/js/phonon.min.js"></script>
    <!--    phononJs-->
    <script>
        seajs.use(["commonJs", 'jqueryPlugins', 'datepickerCore', 'datepickerCssDate', 'datepickerCssCore', "fontAwesome", "functions", "gameJs", "bootstrap", "vue", "fontAwesome", "functions", "jqueryPlugins"], function () {
            seajs.use(["appJs", "commonCss"]);
        });

    </script>
    <!--加载 Start-->
    <div id="loadingBox" class=loading-box>
        <style>
            .loading-box .ph1, .ph2 {
                text-align: center;
                color: #0aa6e3;
                font: 16px/1.5 Helvetica;
                font-family: "Helvetica,微软雅黑,宋体";
                position: relative;
                top: -76px;
            }

            .loading-box .ph2 {
                font-weight: normal;
                font-size: 14px;
                margin-top: 8px;
                color: #95999d;
            }

            .loading-box {
                display: none;
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background: #F7F7F7;
                opacity: 1;
                padding-top: 40%;
                z-index: 10000000;
            }

            .loading-box .spinner {
                position: relative;
                height: 100px;
                width: 100px;
                margin: -50px auto 0;
            }

            .loading-box .spinner i {
                background: #0aa6e3;
                border-radius: 100%;
                height: 10px;
                left: 30px;
                position: absolute;
                top: 45px;
                width: 40px;
            }
        </style>
        <div id="spinner" class=spinner>
            <i class="p1"></i><i class="p2"></i><i class="p3"></i>
            <i class="p4"></i><i class="p5"></i><i class="p6"></i>
            <i class="p7"></i><i class="p8"></i><i class="p9"></i>
            <i class="p10"></i><i class="p11"></i><i class="p12"></i>
        </div>
        <div class="ph1">宝来娱乐</div>
        <div class="ph2">Loading...</div>
        <script type="text/javascript">
            // {* 默认 #preloader 需要隐藏，init()最后再显示出来 *}
            !function () {
                function getById(id) {
                    return document.getElementById(id)
                }

                var deg = 0, tm = null;
                // windows 可以区域高
                var wh = document[document.compatMode == "CSS1Compat" ? "documentElement" : "body"].clientHeight;
                var loadingBox = getById("loadingBox");
                loadingBox.style.paddingTop = ~~(wh * 0.38) + "px";
                function rotate(angle) {
                    var pl = getById('spinner'), s = pl.style, t = 'scale(0.5) rotate(' + deg + 'deg)';
                    s['-ms-transform'] = s['-o-transform'] = s.MozTransform = s.WebkitTransform = s.transform = t;
                    deg += angle;
                    deg = (deg + 360) % 360;
                    tm = window.setTimeout(function () {
                        rotate(angle)
                    }, 120);
                }

                function init() {
                    var spinner = getById("spinner")
                        , balls = spinner.getElementsByTagName("i")
                        , len = balls.length
                        , angle = 360 / len
                        , diffOpacity = 0.07
                        , minO = 1 - (diffOpacity * len)
                        , deg = 0;
                    for (var i = 0, t, s; i < len; i++) {
                        t = ["rotate(", i * angle, "deg) translate(0, -120px)"].join('');
                        s = balls[i].style;
                        s['-ms-transform'] = s['-o-transform'] = s.MozTransform = s.WebkitTransform = s.transform = t;
                        s.opacity = (minO + diffOpacity * i);
                    }
                    // 显示 preloader
                    loadingBox.style.display = "block";
                    rotate(angle);
                }

                window.removeLoadBox = function () {
                    $("#loadingBox").fadeOut(600, function () {
                        clearTimeout(tm);
                        $(this).remove();
                    })
                };
                init();
            }();
        </script>
    </div>

    <script>
        document.on('pagecreated', function (evt) {
            window.removeLoadBox();

        });
    </script>

    <style>
        .header-bar {
            background-color: rgba(0, 0, 0, 0.69);
        }
    </style>
</head>
<body>
<home data-page="true" class="app-page">
    <header class="header-bar home-bar">
        <a data-navigation="notice_page" class="btn pull-left fa fa-envelope-o fa-lg">公告</a>
        <div class="center">
            <h1 class="title"><?php echo $this->config['webName']; ?></h1>
        </div>
        <a data-navigation="kf_service_page" class="btn pull-right fa fa-gg-circle fa-lg">客服</a>
    </header>
    <?php
    require(TPL . '/home.tpl.php');
    ?>
</home>

<!--通知页面-->
<notice_page data-page="true" class="app-page"></notice_page>
<!--彩票大厅-->
<cp_type_list data-page="true" class="app-page"></cp_type_list>
<!--cp游戏-->
<cp_game data-page="true" class="app-page"></cp_game>

<!--用户首页-->
<user_dashboard data-page="true" class="app-page"></user_dashboard>
<!--财务日志-->
<user_finance_logs data-page="true" class="app-page"></user_finance_logs>
<!--投注记录-->
<user_bets_logs data-page="true" class="app-page"></user_bets_logs>

<!--用户个人设置-->
<user_setting_page data-page="true" class="app-page"></user_setting_page>

<!--游戏记录-->
<game_play_logs data-page="true" class="app-page"></game_play_logs>

<!--开奖中心-->
<cp_data_open data-page="true" class="app-page"></cp_data_open>

<!--单个开奖日志-->
<cp_open_logs data-page="true" class="app-page"></cp_open_logs>


<!--充值-->
<user_pay_in data-page="true" class="app-page"></user_pay_in>

<!--提现-->
<user_pay_out data-page="true" class="app-page"></user_pay_out>

<!--额度管理-->
<user_ed_gl data-page="true" class="app-page"></user_ed_gl>


<!--团队管理-->
<agent_member_list data-page="true" class="app-page"></agent_member_list>
<!--开户-->
<agent_account data-page="true" class="app-page"></agent_account>

<!--代理亏盈-->
<agent_member_money data-page="true" class="app-page"></agent_member_money>

<!--代理投注-->
<agent_member_bets data-page="true" class="app-page"></agent_member_bets>


<!--客服页面-->
<kf_service_page data-page="true" class="app-page"></kf_service_page>

<agent_finance_logs data-page="true" class="app-page"></agent_finance_logs>

<nav id="bottom-bar" class="mui-bar mui-bar-tab">
    <a class="mui-tab-item ajax-page mui-active" href="#!home">
        <span class="mui-icon icon icon-home"></span>
        <span class="mui-tab-label">首页</span>
    </a>
    <a class="mui-tab-item ajax-page" href="#!user_bets_logs">
        <span class="mui-icon icon icon-info-outline"></span>
        <span class="mui-tab-label">投注记录</span>
    </a>
    <a class="mui-tab-item ajax-page" href="#!cp_data_open">
        <span class="mui-icon icon icon-star-outline"></span>
        <span class="mui-tab-label">开奖中心</span>
    </a>
    <a class="mui-tab-item ajax-page" data-navigation="user_dashboard">
        <span class="mui-icon icon icon-settings"></span>
        <span class="mui-tab-label">会员中心</span>
    </a>
</nav>
</body>
</html>
