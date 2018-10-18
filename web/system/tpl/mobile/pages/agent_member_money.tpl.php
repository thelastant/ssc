<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<agent_member_money class="app-page">
    <div class="finance_logs">
        <header class="header-bar home-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">团队亏盈记录</h1>
            </div>
        </header>
        <div id="agentMoneyApp" class="content container">
            <nav class="ey-tabs ey-header-tabs"
                 style="top: 50px;">
            </nav>
            <div class="table-box">
                <div class="row table-head">
                    <div class="phone-3 column">用户名</div>
                    <div class="phone-3 column">总收入(元)</div>
                    <div class="phone-3 column">总支出(元)</div>
                    <div class="phone-3 column">总结余(元)</div>
                </div>
                <div class="row table-body" v-for="vo in data_list">
                    <div class="phone-3 column" v-html="vo.username"></div>
                    <div class="phone-3 column">
                        <span v-html="vo.income" class="text-danger"></span>
                    </div>
                    <div class="phone-3 column">
                        <span v-html="vo.expenditure" class="text-danger"></span>
                    </div>
                    <div class="phone-3 column">
                        <span v-html="vo.total==''?0:vo.total" class="text-danger"></span>
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
                var vm = new Vue({
                    el: "#agentMoneyApp",
                    data: {
                        active: 1,
                        page: {
                            current: 1,
                            max: 1,
                        },
                        data_list: [],
                        search: {}
                    },
                    filters: {},
                    methods: {
                        parseDate: function (time) {
                            return moment(time * 1000).format("YYYY-MM-DD..");
                        },
                        getTypeTitle: function (type) {
                            return api.COIN_TYPES[type];
                        },
                        getCpTypes: function () {
                            var _self = this;
                            LoadStart();
                            $.get(api.API_ROUTES.get_type_list, function (ret) {
                                if (ret.code === 200) {
                                    _self.cp_types = ret.data.type_list;
                                }
                                LoadStop();
                            });
                        },
                        getCoinLogs: function () {
                            //get
                            var _self = this;
                            _self.data_list = [];
                            LoadStart();
                            $.post(api.API_ROUTES.get_agent_money, {
                                search: _self.search,
                                page: _self.p
                            }, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.data.forEach(function (item) {
                                        if (!item.expenditure) {
                                            item.expenditure = 0;
                                        }
                                        item.total = parseInt(item.income) + parseInt(item.expenditure);
                                        _self.data_list.push(item);
                                    });
                                } else {
                                    layer.open({content: ret.msg});
                                }
                                console.log(_self.data_list);
                                LoadStop();
                            });
                        },
                    },
                    beforeMount: function () {
                        this.getCpTypes();
                    },
                    mounted: function () {
                        this.getCoinLogs();
                    }
                });
            });
        </script>
    </div>
</agent_member_money>
