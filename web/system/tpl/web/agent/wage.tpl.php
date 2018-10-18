<div id="agent-member-dom" class="common container">
    <div class="head">
        <div class="name icon-address-book">日结工资</div>
    </div>
    <div class="row" style="background: #fff;margin: 0;padding: 30px">
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
                <td>日工资比例</td>
                <td>团队流水</td>
                <td>日工资发放金额</td>
                <td>状态</td>
            </tr>
            </thead>
            <tbody>
            <tr v-if="day_logs.length<=0">
                <td colspan="20" class="text-center">
                    <span>对不起,您还没有日结工资记录</span>
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
                        start_time: "<?php echo date('Y-m-d', strtotime('-10day')); ?>",
                        end_time: "<?php echo date('Y-m-d'); ?>",
                        uid: "",
                    },
                    day_logs: [],
                },
                methods: {
                    getChildren: function () {
                        var _self = this;
                        var indexD = layer.open({type: 2});
                        $.get("/agent/get_children", function (ret) {
                            layer.close(indexD);
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
                        var indexD = layer.open({type: 2});
                        $.post("/agent/get_day_coin", {search: _self.search}, function (ret) {
                            layer.close(indexD);
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