{extends file='common/base.tpl'}
{block name="content"}
    <style>
        #agent-member-dom {
            background: #fff;
            min-height: 500px;
        }

        #agent-member-dom .row {
            padding: 30px;
        }

        #agent-member-dom .data-form {
            margin-bottom: 10px;
        }

        .input-label {
            line-height: 30px;
            border: 1px solid #eee;
            background: #eee;
            color: #000;
            padding: 0 10px;
            font-weight: normal;
        }
    </style>
    <div id="agent-member-dom">
        <div class="head">
            <div class="name icon-address-book">契约分红</div>
        </div>
        <div id="self-box" class="hidden">
            <div class="row set_qy">
                <div class="col-xs-12">
                    <form action="" class="form qy-form" method="post">
                        <div class="form-group">
                            <span v-if="qy_mode==1">
                                <input type="radio" checked="checked" value="1" class="disabled" disabled> 累计上月模式
                            </span>
                            <span v-if="qy_mode==0">
                                <input type="radio" value="1" class="disabled" disabled> 累计上月模式
                            </span>
                        </div>
                        <div class="form-group" v-for="(vo,key) in self_qy_list">
                            <label for="">半月累计销量（元）>= </label>
                            <label type="text" class="input-label"
                                   v-html="vo.need_sale"></label>

                            <label for="">分红 </label>
                            <label type="text" class="input-label"
                                   v-html="vo.red_rate+'%'"></label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row" style="background: #fff;margin: 0;padding: 30px">
            <div class="clearfix">
                <div class="col-xs-6">
                    <table class="table table-hover table-bordered">
                        <tbody>
                        <tr class="top">
                            <td colspan="2" align="center"><label for="">上期分红信息</label></td>
                        </tr>
                        <tr>
                            <td style="width:50%">分红开始时间</td>
                            <td><span class="startDay">{$total.last_self.last.start_date}</span></td>
                        </tr>
                        <tr>
                            <td>分红结束时间</td>
                            <td><span class="endDay">{$total.last_self.last.end_date}</span></td>
                        </tr>
                        <tr>
                            <td>分红比例</td>
                            <td><span class="percent hand">
                                    <a href="javascript:void(0);" class="text-primary self-qy-rate"
                                       @click="showSelfRate">{$total.last_self.fh_config.red_rate}%</a>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>团队盈亏金额</td>
                            <td><span class="text-danger">{$total.last_self.fh_msg.team_coin}元</span></td>
                        </tr>
                        <tr>
                            <td>应得分红</td>
                            <td><span class="deservedBonus text-danger">{$total.last_self.fh_msg.need_coin}元</span></td>
                        </tr>
                        <tr>
                            <td>已收到分红</td>
                            <td><span class="receivedBonus text-danger">{$total.last_self.had}元</span></td>
                        </tr>
                        <!--<tr><td>分红次数</td><td></td></tr>-->
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-6">
                    <table class="table table-hover table-bordered">
                        <tbody>
                        <tr class="top">
                            <td colspan="2" align="center"><label for="">派发分红信息</label></td>
                        </tr>
                        <tr>
                            <td style="width:50%">应派发分红</td>
                            <td><span class=" text-danger">{$total.need_child_send}元</span></td>
                        </tr>
                        <tr>
                            <td>已派发分红</td>
                            <td><span class=" text-danger">{$total.had_child_send}元</span></td>
                        </tr>
                        <tr>
                            <td>未派发分红</td>
                            <td>
                                <span class="yetDistributBonus">{$total.not_child_send}</span>&nbsp;&nbsp;
                                {literal}
                                    <a @click="pushQyRed"
                                       class="btn btn-danger"
                                       :class="{'disabled':click_enable<=0}">派发分红</a>
                                {/literal}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <form action="" method="" class="form-inline data-form">
                <div class="form-group">
                    <label for="" class="control-label">开始时间</label>
                    <input type="date" class="form-control" name="start_time" v-model="search.start_time">
                </div>
                <div class="form-group">
                    <label for="" class="control-label">结束时间</label>
                    <input type="date" class="form-control" name="start_time" v-model="search.end_time">
                </div>
                <div class="form-group">
                    <label for="" class="control-label">用户名</label>
                    <select name="uid" class="form-control" v-model="search.uid">
                        <option value="">全部</option>
                        <option :value="chi.uid" v-for="chi in children" v-html="chi.username"></option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="button" @click="getData();" class="btn btn-primary">查询</button>
                </div>
            </form>
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <td>日期</td>
                    <td>用户名</td>
                    <td>分红比例</td>
                    <td>团队盈亏</td>
                    <td>分红金额</td>
                    <td>状态</td>
                </tr>
                </thead>
                <tbody>
                <tr v-if="data_logs.length<=0">
                    <td colspan="20" class="text-center">
                        <span>对不起,您还没有分红记录</span>
                    </td>
                </tr>
                <tr v-for="it in data_logs">
                    <td><span v-html="it.send_date"></span></td>
                    <td><span v-html="it.username"></span></td>
                    <td><span v-html="it.red_rate"></span>%</td>
                    <td><span class="text-danger" v-html="it.team_coin"></span></td>
                    <td><span class="text-danger" v-html="it.send_coin"></span></td>
                    <td>
                        <label class="label label-success" v-if="it.status==1">已派发</label>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        var TEMP = {
            uid:{$_user.uid},
            need_child_send:{$total.need_child_send},
            had_child_send:{$total.had_child_send},
            click_enable:{$total.click_enable},
            qy_mode:{$_user.qy_mode}
        };
        console.log(TEMP);
    </script>
{literal}
    <script type="text/javascript">
        $(function () {
            seajs.use(["vue", "moment"], function () {
                var vm = new Vue({
                    el: "#agent-member-dom",
                    data: {
                        qy_mode: TEMP.qy_mode,
                        children: [],
                        self_qy_list: [],
                        search: {
                            start_time: moment().format("YYYY-MM-01"),
                            end_time: moment().format("YYYY-MM-DD"),
                            uid: '',
                        },
                        data_logs: [],
                        is_last_send: true,
                        click_enable: TEMP.click_enable,
                    },
                    methods: {
                        showSelfRate: function () {
                            var htm = $("#self-box").html();
                            layer.open({
                                content: "" + htm,
                            });
                        },
                        getChildren: function () {
                            var _self = this;
                            $.get("/agent/get_children", function (ret) {
                                if (ret.code === 200) {
                                    ret.data.forEach(function (item) {
                                        _self.children.push(item);
                                    });
                                    _self.getData();
                                }
                            })
                        }
                        ,
                        getData: function () {
                            var _self = this;
                            _self.data_logs = [];
                            $.post("/api/api_get_qy_logs", {search: _self.search}, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.forEach(function (item) {
                                        _self.data_logs.push(item);
                                    });
                                }
                            })
                        },
                        getSelfQy: function () {
                            var _self = this;
                            $.post("/api/api_get_qy", {uid: _self.uid}, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.forEach(function (item) {
                                        _self.self_qy_list.push(item);
                                    });
                                }
                            });
                        },
                        pushQyRed: function () {
                            var _self = this;
                            $.post("/user/api_send_qy_red", {}, function (ret) {
                                layer.open({content: ret.msg});
                                if (ret.code === 200) {
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 1200);
                                }
                            });
                        }
                    },
                    mounted: function () {
                        var _self = this;
                        this.getChildren();
                        this.getSelfQy();
                        //是否发放分红
                        if ((TEMP.need_child_send - TEMP.had_child_send) > 0) {
                            _self.is_last_send = false;
                        } else {
                            _self.is_last_send = true;
                        }
                    }
                });
                //
                $(document).on('click', ".ajax-href-form", function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    var data = $this.serialize();
                    var url = $this.attr("href");
                    var ref = $this.attr("ref");
                    $.post(url, data, function (ret) {
                        layer.open({content: ret.data.body});
                        if (ret.code === 200) {
                            //window.location.reload();
                        }
                    });

                });
                $(document).on('submit', "form[target='ajax-form']", function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    var data = $this.serialize();
                    var url = $this.attr("action");
                    var ref = $this.attr("ref");
                    $.post(url, data, function (ret) {
                        layer.open({content: ret.msg});
                        if (ret.code === 200) {
                            window.location.reload();
                        }
                    });

                });
            });
        });
    </script>
{/literal}
{/block}