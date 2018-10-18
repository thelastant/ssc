<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<game_play_logs class="app-page">
    <style>
        .layermend::after, .layermend::before {
            background-color: #fff;
        }
    </style>
    <div class="finance_logs">
        <header class="header-bar home-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">游戏记录</h1>
            </div>
        </header>
        <div id="game_play_logsApp" class="content container">
            <nav class="ey-tabs ey-header-tabs"
                 style="top: 50px;">
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
                    <div class="phone-3 column">游戏</div>
                    <div class="phone-2 column">倍数</div>
                    <div class="phone-3 column">开奖</div>
                    <div class="phone-2 column">消费(元)</div>
                    <div class="phone-2 column">奖金(元)</div>

                </div>
                <div class="row table-body" v-for="vo in data_list">
                    <div class="phone-3 column text-info" v-html="vo.title" @click="showDetail(vo.id)"></div>
                    <div class="phone-2 column" v-html="vo.beiShu+'倍'"></div>
                    <div class="phone-3 column">
                        <span v-html="vo.lotteryNo"></span>
                        <p v-if="vo.lotteryNo<=0">-*-*-*-</p>
                    </div>
                    <div class="phone-2 column">
                        <span v-html="(vo.mode * vo.beiShu* vo.actionNum).toFixed(2)" class="money"></span>
                    </div>
                    <div class="phone-2 column">
                        <p class="inline-p-tag ey-c-success" v-if="(vo.lotteryNo.length>0)" v-html="vo.bonus"></p>
                        <p class="inline-p-tag money text-danger" v-if="vo.lotteryNo<=0&&vo.isDelete==0">未开奖</p>
                        <p class="text-warning" v-if="vo.isDelete==1">已撤单</p>
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
                var vm = new Vue({
                    el: "#game_play_logsApp",
                    data: {
                        active: 1,
                        type: '',
                        p: 1,
                        filter_types: [
                            {
                                id: '',
                                title: "投注记录"
                            },
                            {
                                id: 4,
                                title: "追号记录"
                            },
                            {
                                id: 1,
                                title: "中奖记录"
                            }
                        ],
                        data_list: [],
                    },
                    filters: {},
                    methods: {
                        showDetail: function (id) {
                            var _self = this;
                            $.post(api.API_ROUTES.get_bet_info, {id: id}, function (ret) {
                                console.log(ret);
                                layer.open({
                                    title: ["投注明细", 'background-color: #FF4351; color:#fff;'],
                                    shadeClose: false,
                                    content: ret.msg,
                                    btn: ["撤销投注", "关闭"],
                                    skin: 'footer',
                                    style: 'position:fixed; bottom:0; left:0; width: 100%;max-width:100%; border:none;',
                                    yes: function (index) {
                                        layer.close(index);
                                        $.post(api.API_ROUTES.remove_single_bet, {id: id}, function (ret) {
                                            layer.open({
                                                content: ret.msg,
                                                time: 2,
                                            });
                                            _self.getCoinLogs();
                                        });
                                    },
                                    no: function () {
                                        //console.log("no");

                                    }

                                });
                            });
                        },
                        parseDate: function (time) {
                            return moment(time * 1000).format("YYYY-MM-DD..");
                        },
                        getTypeTitle: function (type) {
                            return api.COIN_TYPES[type];
                        },
                        getCoinLogs: function () {
                            //get
                            var _self = this;
                            _self.data_list = [];
                            LoadStart();
                            $.post(api.API_ROUTES.get_bet_logs, {state: _self.type, page: _self.p}, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.data.forEach(function (item) {
                                        item.id = parseInt(item.id);
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
                            $.post(api.API_ROUTES.get_bet_logs, {state: _self.type, page: _self.p}, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.data.forEach(function (item) {
                                        item.id = parseInt(item.id);
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
</game_play_logs>