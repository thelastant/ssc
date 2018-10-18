<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<user_bets_logs class="app-page">
    <style>
        .layermend::after, .layermend::before {
            background-color: #fff;
        }
    </style>
    <div class="finance_logs">
        <header class="header-bar home-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">投注记录</h1>
            </div>
        </header>
        <div id="betLogsApp" class="content container">
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
                <div class="row text-center">
                    <div class="phone-6 column" style="border-right: 1px solid #ddd">
                        <input class="small_picker datepicker picker__input picker__input--active" type="text"
                               target="start_date"
                               v-model="search.start_date"
                               placeholder="选择开始时间"/>
                        <input type="hidden" id="start_date" v-model="search.start_date">
                    </div>
                    <div class="phone-6 column">
                        <input class="small_picker datepicker picker__input picker__input--active" type="text"
                               target="end_date"
                               v-model="search.end_date"
                               placeholder="选择开始时间"/>
                        <input type="hidden" id="end_date" v-model="search.end_date">
                    </div>
                </div>
                <div class="row table-head">
                    <div class="phone-3 column">游戏</div>
                    <div class="phone-3 column">开奖</div>
                    <div class="phone-3 column">投注金额(元)</div>
                    <div class="phone-3 column">奖金(元)</div>
                </div>
                <div class="row table-body" v-for="vo in data_list">
                    <div class="phone-3 column text-info" v-html="vo.title" @click="showDetail(vo.id)"></div>
                    <div class="phone-3 column">
                        <span v-html="vo.lotteryNo"></span>
                        <p v-if="vo.lotteryNo<=0">-*-*-*-</p>
                    </div>
                    <div class="phone-3 column">
                        <span v-html="(vo.mode * vo.beiShu* vo.actionNum).toFixed(2)" class="money"></span>
                    </div>
                    <div class="phone-3 column">
                        <p class="inline-p-tag ey-c-success" v-if="(vo.lotteryNo.length>0&&vo.bonus==0.0000)" v-html="vo.bonus"></p>
						<p class="inline-p-tag ey-c-success" style="color: #D80000;" v-if="(vo.lotteryNo.length>0&&vo.bonus!=0.0000)" v-html="vo.bonus"></p>
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
                //var moment = seajs.require("moment");
                var vm = new Vue({
                    el: "#betLogsApp",
                    data: {
                        active: 1,
                        type: '',
                        p: 1,
                        filter_types: [
                            {
                                id: '',
                                title: "所有"
                            },
                            {
                                id: 1,
                                title: "已派奖"
                            },
                            {
                                id: 2,
                                title: "未中奖"
                            },
                            {
                                id: 3,
                                title: "未开奖"
                            },
                            {
                                id: 4,
                                title: "追号"
                            }
                            //{
                              //  id: 6,
                                //title: '撤单'
                            //}

                        ],
                        data_list: [],
                        cp_types: {},
                        search: {
                            start_date: '<?php echo date("Y-m-d"); ?>',
                            end_date: '<?php echo date("Y-m-d", strtotime("+1day"));?>'
                        }
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
                                    //btn: ["撤销投注", "关闭"],
									btn: [ "关闭"],
                                    skin: 'footer',
                                    style: 'position:fixed; bottom:0; left:0; width: 100%;max-width:100%; border:none;',
                                    yes: function (index) {
                                        layer.close(index);
                                        //$.post(api.API_ROUTES.remove_single_bet, {id: id}, function (ret) {
                                        //    layer.open({
                                         //       content: ret.msg,
                                         //       time: 2,
                                          //  });
                                          //  _self.getCoinLogs();
                                        //});
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
                            $.post(api.API_ROUTES.get_bet_logs, {
                                state: _self.type,
                                page: _self.p,
                                search: _self.search
                            }, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.data.forEach(function (item) {
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
                            $.post(api.API_ROUTES.get_bet_logs, {
                                state: _self.type,
                                page: _self.p,
                                search: _self.search
                            }, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.data.forEach(function (item) {
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
                    created: function () {

                    },
                    mounted: function () {
                        this.getCoinLogs();
                        var _self = this;
                        $('.datepicker').pickadate({
                            format: 'yyyy-mm-dd'
                        });
                        $('.datepicker').change(function (e) {
                            $().val();
                            _self.search[$(this).attr("target")] = $(this).val();
                        })
                    }
                });


            });

        </script>
    </div>
</user_bets_logs>
