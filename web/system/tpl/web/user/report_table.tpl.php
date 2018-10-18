<div id="agent-member-dom" class="common container">
    <div class="head">
        <div class="name icon-address-book">契约分红</div>
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
                        <td><span class="startDay">#</span></td>
                    </tr>
                    <tr>
                        <td>分红结束时间</td>
                        <td><span class="endDay">#</span></td>
                    </tr>
                    <tr>
                        <td>分红比例</td>
                        <td><span class="percent hand"><?php echo $this->user['qy_red_rate']; ?>%</span></td>
                    </tr>
                    <tr>
                        <td>团队盈亏金额</td>
                        <td><span class="text-danger">#</span></td>
                    </tr>
                    <tr>
                        <td>应得分红</td>
                        <td><span class="deservedBonus">0.0000</span></td>
                    </tr>
                    <tr>
                        <td>已收到分红</td>
                        <td><span class="receivedBonus">0.0000</span></td>
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
                        <td><span class="shouleDistributBonus">#</span></td>
                    </tr>
                    <tr>
                        <td>已派发分红</td>
                        <td><span class="alreadyDistributBonus">#</span></td>
                    </tr>
                    <tr>
                        <td>未派发分红</td>
                        <td>
                            <span class="yetDistributBonus">0.0000</span>&nbsp;&nbsp;
                            <a id="sendBonus" style=""
                               class="btn btn-danger disabled">派发分红</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div>
            <form action="" method="" class="form-inline" style="padding: 10px">
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
        </div>


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
            <tr v-if="day_logs.length<=0">
                <td colspan="20" class="text-center">
                    <span>对不起,您还没有分红记录</span>
                </td>
            </tr>
            <tr v-for="it in day_logs">
                <td><span v-html="it.date"></span></td>
                <td><span v-html="it.username"></span></td>
                <td><span v-html="it.day_rate"></span>%</td>
                <td><span v-html="it.team_coins"></span></td>
                <td><span v-html="it.coin"></span></td>
                <td>
                    <label class="label label-success" v-if="it.status==1">已派发</label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        seajs.use(["vue"], function () {

            var vm = new Vue({
                el: "#agent-member-dom",
                data: {
                    children: [],
                    search: {
                        start_time: "<?php echo date('Y-m-d'); ?>",
                        end_time: "<?php echo date('Y-m-d'); ?>",
                        uid: '',
                    },
                    day_logs: [],
                },
                methods: {
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
                        _self.day_logs = [];
                        $.post("/agent/get_day_coin", {search: _self.search}, function (ret) {
                            if (ret.code === 200) {
                                ret.data.forEach(function (item) {
                                    _self.day_logs.push(item);
                                });
                            }
                        })
                    }
                },
                mounted: function () {
                    this.getChildren();
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

        $('#home').removeClass('on');
        $('#agent-member').addClass('on');
        $('#agent').addClass('on');
        // 添加会员
        $('#agent-member-dom .member_add').bind('click', function () {
            $('#agent-member-dom .member_add_box').toggleClass("hide");
        });
        // 其他选择
        $('#agent-member-dom select.cs-select').each(function () {
            new SelectFx(this);
        });
        // 菜单下拉固定
        $.scroll_fixed('#agent-member-dom .head');
    });
</script>