<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<user_dashboard class="app-page">
    <div class="dashboard">
        <header class="header-bar home-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">会员中心</h1>
            </div>
            <a class="btn pull-right icon icon-settings" href="#!user_setting_page"></a>
        </header>
        <div id="dashboardApp" class="content container">
            <div class="row user-profile-row">
                <div class="row">
                    <h4>欢迎您，<span v-html="user.username"></span></h4>
                    <i></i>
                </div>
                <div class="row">
                    <a data-navigation="user_setting_page">
                        <div class="phone-4 column">
                            <p class="line-1"><span v-html="user.coin"></span>元</p>
                            <p>余额</p>
                        </div>
                        <div class="phone-4 column" style="display:none">
                            <p class="line-1"><span v-html="user.score"></span></p>
                            <p>积分</p>
                        </div>
                        <div class="phone-4 column">
                            <p class="line-1"><span v-html="user.fanDian"></span>%</p>
                            <p>彩票返点</p>
                        </div>
                    </a>
                </div>
                <div class="row money-action-row">
                    <div class="phone-6 column">
                        <a href="#!user_pay_in">充值</a>
                    </div>
                    <div class="phone-6 column">
                        <a href="#!user_pay_out">提现</a>
                    </div>
                </div>
            </div>

            <div class="row dash-row">
                <div class="phone-4 column">
                    <a data-navigation="user_bets_logs">
                        <p class="ey-ui-icon-box ey-bg-blue">
                            <i class="fa fa-bookmark-o fa-lg"></i>
                        </p>
                        <p class="title">投注记录</p>
                    </a>
                </div>
                <div class="phone-4 column">
                    <a data-navigation="user_finance_logs">
                        <p class="ey-ui-icon-box ey-bg-yellow">
                            <i class="fa fa-location-arrow fa-lg"></i>
                        </p>
                        <p class="title">账变记录</p>
                    </a>
                </div>
                <div class="phone-4 column">
                    <a data-navigation="agent_member_list">
                        <p class="ey-ui-icon-box ey-bg-danger">
                            <i class="fa fa-lastfm-square fa-lg"></i>
                        </p>
                        <p class="title">团队管理</p>
                    </a>
                </div>
            </div>
            <div class="row dash-row">
                <div class="phone-4 column">
                    <a data-navigation="agent_account">
                        <p class="ey-ui-icon-box ey-bg-blue">
                            <i class="fa fa-anchor fa-lg"></i>
                        </p>
                        <p class="title">精准开户</p>
                    </a>
                </div>
                <div class="phone-4 column">
                    <a data-navigation="agent_member_money">
                        <p class="ey-ui-icon-box ey-bg-yellow">
                            <i class="fa fa-whatsapp fa-lg"></i>
                        </p>
                        <p class="title">亏盈报表</p>
                    </a>
                </div>
                <div class="phone-4 column">
                    <a data-navigation="agent_finance_logs">
                        <p class="ey-ui-icon-box ey-bg-danger">
                            <i class="fa fa-magic fa-lg"></i>
                        </p>
                        <p class="title">团队账变</p>
                    </a>
                </div>
            </div>
			<div class="row dash-row"  style="display:none">
                <div class="phone-4 column">
                    <a target="_blank" href="/live/ibc.php">
                        <p class="ey-ui-icon-box ey-bg-blue">
                            <i class="fa fa-anchor fa-lg"></i>
                        </p>
                        <p class="title">沙巴体育</p>
                    </a>
                </div>
                <div class="phone-4 column"  style="display:none">
                    <a data-navigation="user_ed_gl">
                        <p class="ey-ui-icon-box ey-bg-yellow">
                            <i class="fa fa-whatsapp fa-lg"></i>
                        </p>
                        <p class="title">额度转换</p>
                    </a>
                </div>
                <div class="phone-4 column" style="display:none">
                    <a>
                        <p class="ey-ui-icon-box ey-bg-danger">
                            <i class="fa fa-magic fa-lg"></i>
                        </p>
                        <p class="title">暂未开放</p>
                    </a>
                </div>
            </div>
            <div class="row activity" style="background: #fff">
                <div class="row-head">
                    <span>优惠活动</span>
                </div>
                <!-- Swiper -->
                <div class="swiper-container" style="border-bottom: 1px solid #ddd;">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            暂无活动
                        </div>
                        <div class="swiper-slide">
                            暂无活动
                        </div>
                    </div>
                    <!-- Add Pagination -->
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <div class="row action-footer">

            </div>
        </div>
        <script>
            seajs.use(["vue", "apiJs", "swiper", 'swiperCss'], function () {
                var swiper = new Swiper('.swiper-container', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    autoplay: 2000,
                });

                var api = seajs.require("apiJs");
                var vm = new Vue({
                    el: "#dashboardApp",
                    data: {
                        user: {},
                    },
                    methods: {
                        getUser: function () {
                            var _self = this;
                            $.get(api.API_ROUTES.get_user_info, function (ret) {
                                if (ret.code === 200) {
                                    _self.user = ret.data;
                                }
                            })
                        }
                    },
                    mounted: function () {
                        var _self = this;
                        setInterval(function () {
                            _self.getUser();
                        }, 2000);
                    }
                })
            });
        </script>
    </div>
</user_dashboard>