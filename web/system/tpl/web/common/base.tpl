<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <title>{$_config.webName}</title>
    <script src="{$smarty.const.STATIC_PATH}/libs/jquery/jquery-2.1.4.min.js"></script>
    <!--bootstrap-->
    <link href="{$smarty.const.STATIC_PATH}libs/bootstrap/css/bootstrap.min.css"
          rel="stylesheet"
          type="text/css"/>
    {*<script src="{$smarty.const.THEME_PATH}js/highcharts.js"></script>*}
    {*<script src="{$smarty.const.THEME_PATH}js/highcharts.js"></script>*}
    {*<script src="{$smarty.const.THEME_PATH}js/jquery.zclip.min.js"></script>*}
    <script src="{$smarty.const.THEME_PATH}js/jquery.cookie.js"></script>
    {*<script src="{$smarty.const.THEME_PATH}js/jquery.datetimepicker.js"></script>*}
    {*<script src="{$smarty.const.THEME_PATH}js/array.ext.js"></script>*}
    {*<script src="{$smarty.const.THEME_PATH}js/rawdeflate.js"></script>*}
    {*<script src="{$smarty.const.THEME_PATH}js/select.js"></script>*}
    <script src="{$smarty.const.THEME_PATH}js/common.js"></script>
    <script src="{$smarty.const.THEME_PATH}js/function.js"></script>
    {*<script src="{$smarty.const.THEME_PATH}js/game.js"></script>*}
    {*<script src="{$smarty.const.THEME_PATH}js/jquery.rotate.js"></script>*}
    {*<script src="{$smarty.const.THEME_PATH}js/skinTab.js"></script>*}

    <!--saejs-->
    <script src="{$smarty.const.STATIC_PATH}libs/seajs/sea.js"></script>
    <script src="{$smarty.const.STATIC_PATH}libs/seajs/sea-plugin.js"></script>
    <script src="{$smarty.const.THEME_PATH}sea-conf.js"></script>
    <style>
        .common.container {
            margin-top: 20px;
        }

        .container {
            min-width: 1080px;
        }
    </style>
    <script>
        seajs.use(["bootstrap", "commonCss", "fontAwesome", "animate"], function () {

        })
    </script>
</head>
<body>

<nav class="navbar-ey">
    <div class="container">
        <div class="col-xs-6">
            <div class="info" id="user-info">
                <ul class="list-inline">
                    <li>
                        <span class="unval icon-user">会员名称：<span id="show-username"></span></span>
                    </li>
                    <li>
                        <span class="balance icon-fire-1">可用余额：<span id="show-user-money"></span></span>
                    </li>
                    <li>
                        <span class="balance icon-cloud">积分：<span id="show-user-score"></span></span>
                    </li>
                    <li>
                        <a href="/user/logout">
                            <span class="text text-danger">注销登录</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-xs-5 col-xs-offset-1">
            <ul class="list-inline">
                <li>
                    <a href="/user/recharge" id="user-recharge">
                        <span class="text">充值</span>
                    </a>
                </li>
                <li>
                    <a href="/user/cash" id="user-cash">
                        <span class="text">提现</span>
                    </a>
                </li>
                <li>
                    <a href="/user/setting">
                        <span class="text">设置</span>
                    </a>
                </li>
                <li>
                    <a href="/user/message_receive" class="pr"
                       id="message-receive">
                        <span class="text">私信</span>
                    </a>
                </li>
                <li>
                    <a href="/public/notice" class="pr"
                       id="message-receive">
                        <span class="text">公告</span>
                    </a>
                </li>
                <li>
                    <a href="/user/sign" target="ajax-link">
                        <span class="text">今日签到</span>
                    </a>
                </li>
                <li><a href="https://beta.bugly.qq.com/kq5e" class="app-download"><i class="fa fa-mobile fa-lg"></i>&nbsp;APP下载</a></li>
                <li>
                    <a href="{$_config.kefuGG}" target="_blank"
                       class="sign bar-item icon-cloud">在线客服</a>
                </li>
                <li>
                    <a class="text" style="color: red;" target="_blank"
                       href="http://wpa.qq.com/msgrd?v=3&uin=2468091888&site=qq&menu=yes">QQ总代</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div id="header">
    <div class="container">
        <div class="row">
            <div class="col-xs-2">
                <a href="javascript:$.reload('/');" class="logo"></a>
            </div>
            <div class="col-xs-10">
                <div id="nav">
                    <div class="nav-container">
                        <a href="/" class="bar-item" id="home">
                            <span>HOME</span>
                            <i> 首页</i>
                        </a>
                        <div class="bar-item slider-bar" id="cp_slider">
                            <span>LOTTERY</span>
                            <i>彩票大厅
                                <d class="fa fa-sort-down"></d>
                            </i>
                            <div class="nav-cp-category-list slider-bar-show">
                                <div class="row" id="cps_list_app">
                                    <div class="col-xs-3" v-for="it in type_list">
                                        <ul class="list-unstyled">
                                            <li class="list-title">
                                                <span v-html="it.title">时时彩</span>
                                            </li>
                                            <li v-for="vit in it.type_list">
                                                <a :href="'/game/index?id='+vit.id"
                                                   class="list-item-a">
                                                    <abbr v-if="it.title=='利鼎全天彩'">{$_config.webName}</abbr>
                                                    <abbr v-html="vit.title"></abbr>
                                                    <i class="cp-hot" v-if="vit.is_hot==1"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <script>
                                    seajs.use(["vue", "apiJs"], function () {
                                        var api = seajs.require("apiJs");
                                        var vm = new Vue({
                                            el: "#cps_list_app",
                                            data: {
                                                type_list: [],
                                                is_loading: true,
                                            },
                                            methods: {
                                                getTypeList: function () {
                                                    var _self = this;
                                                    _self.type_list = [];
                                                    api.getTypeMB(function (ret) {
                                                        setTimeout(function () {
                                                            _self.is_loading = false;
                                                        }, 2000);
                                                        if (ret.code !== 200) {
                                                            layer.msg(ret.msg);
                                                        }
                                                        ret.data.forEach(function (item) {
                                                            _self.type_list.push(item);
                                                        });
                                                    });
                                                }
                                            },
                                            mounted: function () {
                                                this.getTypeList();
                                            }
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                        <a class="bar-item future-open" id="user-setting">
                            <span>LIVE</span>
                            <i>真人娱乐</i>
                        </a>
                        <a class="bar-item future-open" id="bet-log">
                            <span>SPORTS</span>
                            <i>体育博弈</i>
                        </a>
                        <a class="bar-item future-open" id="user-money">
                            <span>POKE</span>
                            <i>棋牌游戏</i>
                        </a>
                        <a class="bar-item future-open" id="system-notice">
                            <span>GAME</span>
                            <i>电子游艺</i>
                        </a>
                        {if $_user.type eq 1}
                            <div class="bar-item slider-bar" id="agent-nav">
                                <span>AGENT</span>
                                <i>代理中心
                                    <d class="fa fa-sort-down"></d>
                                </i>
                                <div class="agent-nav-list slider-bar-show">
                                    <ul class="list-unstyled">
                                        <li><a href="/agent/member" id="agent-member">代理开户</a>
                                        </li>
                                        <li>
                                            <a href="/agent/member" id="agent-member">会员管理</a>
                                        </li>
                                        <li>
                                            <a href="/agent/coin_table">团队报表</a>
                                        </li>
                                        <li>
                                            <a href="/agent/wage" id="user-wage">日结工资</a>
                                        </li>
                                        <li><a href="/agent/log" id="agent-log">团队记录</a></li>
                                        <li><a href="/agent/money" id="agent-money">盈亏统计</a></li>
                                        <li><a href="/agent/coin" id="agent-coin">帐变日志</a></li>
                                        <li><a href="/agent/spread" id="agent-spread">推广链接</a></li>
                                    </ul>
                                </div>
                            </div>
                        {/if}
                        <div class="bar-item slider-bar">
                            <span>DATA</span>
                            <i>数据报表
                                <d class="fa fa-sort-down"></d>
                            </i>
                            <div class="agent-nav-list slider-bar-show">
                                <ul class="list-unstyled">
                                    <li>
                                        <a href="/bet/log">游戏记录</a>
                                    </li>
                                    <li>
                                        <a href="/user/coin_table">个人报表</a>
                                    </li>
                                    <li>
                                        <a href="/user/report_table">契约分红</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <a href="/activity/rotary" class="bar-item" id="activity">
                            <span>ACTIVITY</span>
                            <i>活动中心</i>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{literal}
    <script>
        seajs.use(["layer", "layerCss"], function () {
            $(function () {
                $('#nav a').mouseenter(function () {
                    $(this).animateCss('jello');
                });

                $(".slider-bar").hover(function () {
                    $(this).find('.slider-bar-show').fadeIn(180);
                }, function () {
                    $(this).find('.slider-bar-show').hide();
                });

                lottery.user_fresh();

            });
            $(document).on("click", ".no-build", function (e) {
                e.preventDefault();
                layer.open({content: "暂未开放，请敬请期待！"});
            });

            $("a.future-open").on('click', function (e) {
                e.preventDefault();
                layer.open({content: "暂未开放，请敬请期待！"});
            });

            $(document).on('click', "a[target='ajax-link']", function (e) {
                e.preventDefault();
                var $this = $(this);
                var data = $this.serialize();
                var url = $this.attr("href");
                var ref = $this.attr("ref");
                $.post(url, data, function (ret) {
                    layer.open({content: ret.msg});
                });

            });
        });
    </script>
{/literal}

<!--主要身体-->
<div id="dom_body">
    <div id="container">
        <div id="container_warp">
            <div class="common container">
                {block name="content"}{/block}
            </div>
        </div>
    </div>
</div>
<div class="footer-box">
    <div class="container">
        <div class="user_link">
            <a class="link2" href="javascript:;"></a>
            <a class="link3" href="javascript:;"></a>
            <a class="link4" href="javascript:;"></a>
            <a class="link5" href="javascript:;"></a>
            <a class="link6" href="javascript:;"></a>
            <a class="link7" href="javascript:;"></a>
            <a class="link8" href="javascript:;"></a>
        </div>
        <div class="footer">
            <div class="cnt">
                <p>Copyright&nbsp;&nbsp;&copy;{$_config.webName} All Rights
                    Reserved</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>