<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<agent_finance_logs class="app-page">
    <div class="finance_logs">
        <header class="header-bar home-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">团队账变</h1>
            </div>
        </header>
        <div id="financeLogsApp" class="content container">
            <nav class="ey-tabs ey-header-tabs" style="top: 50px;">
                <div class="ey-tab-items">
                    <a class="ey-tab-item"
                       @click="changeType(tp.id)"
                       :class="{active:type===tp.id}"
                       v-for="tp in filter_types">
                        <span v-html="tp.title"></span>
                    </a>
                </div>
            </nav>
            <div class="table-box">
                <div class="row table-head">
                    <div class="phone-3 column">时间</div>
                    <div class="phone-2 column">用户</div>
                    <div class="phone-3 column">类型</div>
                    <div class="phone-2 column">金额</div>
                    <div class="phone-2 column">说明</div>
                </div>
                <div class="row table-body" v-for="vo in data_list">
                    <div class="phone-3 column" v-html="parseDate(vo.actionTime)"></div>
                    <div class="phone-2 column" v-html="vo.username"></div>
                    <div class="phone-3 column" v-html="getTypeTitle(vo.liqType)"></div>
                    <div class="phone-2 column">
                        <span v-html="vo.coin" class="money"></span>
                    </div>
                    <div class="phone-2 column">
                        <p style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden" v-html="vo.info"></p>
                    </div>
                </div>
            </div>
            <div class="go-next">
                <div class="next-btn" @click="getNext()">点击加载更多数据</div>
            </div>
        </div>

        <script>
            seajs.use(["vue", "apiJs", "moment"], function () {
                var api = seajs.require("apiJs");
                //var moment = seajs.require("moment");
                Vue.debug = true;
                var vm = new Vue({
                    el: "#financeLogsApp",
                    data: {
                        active: 1,
                        type: '',
                        p: 1,
                        filter_types: [
                            {
                                id: '',
                                title: "账变日志"
                            },
                            {
                                id: [167],
                                title: "日结工资"
                            },
                            {
                                id: [1, 9],
                                title: "充值记录"
                            },
                            {
                                id: [106, 12, 107, 8],
                                title: "提现记录"
                            }
                        ],
                        data_list: [],
                    },
                    filters: {},
                    methods: {
                        parseDate: function (time) {
                            return moment(time * 1000).format("YYYY-MM-DD..");
                        },
                        getTypeTitle: function (type) {
                            return api.COIN_TYPES[type];
                        },
                        getCoinLogs: function () {
                            //get
                            var _self = this;
                            LoadStart();
                            _self.data_list = [];
                            $.post(api.API_ROUTES.get_agent_finance_logs, {
                                type: _self.type,
                                page: _self.p
                            }, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.data_list.forEach(function (item) {
                                        _self.data_list.push(item);
                                    });
                                }
                                LoadStop();
                            });
                        },
                        getNext: function () {
                            //next
                            var _self = this;
                            _self.p = (_self.p + 1);
                            LoadStart();
                            $.post(api.API_ROUTES.get_agent_finance_logs, {
                                type: _self.type,
                                page: _self.p
                            }, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.data_list.forEach(function (item) {
                                        _self.data_list.push(item);
                                    });
                                } else {
                                    layer.msg("没有更多数据了");
                                }
                                LoadStop();
                            });
                        },
                        changeType: function (type) {
                            this.type = type;
                            this.p = 1;//重置分页
                            this.getCoinLogs();
                        }
                    },
                    mounted: function () {
                        this.getCoinLogs();
                    }
                });
            });

        </script>
    </div>
</agent_finance_logs>