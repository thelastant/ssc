{extends file='common/base.tpl'}
{block name="content"}
    <style>
        #coin-table-dom {
            background: #fff;
            min-height: 500px;
        }

        #coin-table-dom .row {
            padding: 30px;
        }

        #coin-table-dom .data-form {
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
    <div id="coin-table-dom">
        <div class="head">
            <div class="name icon-address-book">团队报表</div>
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
                    <button type="button" @click="getData();" class="btn btn-primary">查询</button>
                </div>
            </form>
            <table class="table table-bordered table-hover">
                <thead>
                <tr class="text-center">
                    <td>用户名</td>
                    <td>充值</td>
                    <td>提现</td>
                    <td>消费</td>
                    <td>派奖</td>
                    <td>返点</td>
					<td>团队盈亏</td>
                    {*<td>活动/工资</td>*}
                    <td>盈利</td>
                    {*<td>其他</td>*}
                </tr>
                </thead>
                <tbody>
                <tr v-if="data_logs.length<=0">
                    <td colspan="20" class="text-center">
                        <span>对不起,您还没有记录</span>
                    </td>
                </tr>
                <tr v-for="it in data_logs" class="text-center">
                    <td><span v-html="it.username"></span></td>
                    <td><span v-html="it.pay_in"></span></td>
                    <td><span v-html="it.pay_out"></span></td>
                    <td><span v-html="it.used"></span></td>
                    <td><span v-html="it.send_coin"></span></td>
                    <td><span v-html="it.fan_dian"></span></td>
					<td><span v-html="it.teamwin"></span></td>
                    {*<td><span v-html="it.activity"></span></td>*}
                    <td><span v-html="it.win"></span></td>
                    {*<td><span v-html="it.other"></span></td>*}
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        var TEMP = {
            uid:{$_user.uid},
        };
        console.log(TEMP);
    </script>
{literal}
    <script type="text/javascript">
        $(function () {
            seajs.use(["vue", "moment"], function () {
                var vm = new Vue({
                    el: "#coin-table-dom",
                    data: {
                        search: {
                            start_time: moment().format("YYYY-MM-01"),
                            end_time: moment().format("YYYY-MM-DD"),
                        },
                        data_logs: [],
                        children: [],
                        is_last_send: true,
                    },
                    methods: {
                        showSelfRate: function () {
                            var htm = $("#self-box").html();
                            layer.open({
                                content: "" + htm,
                            });
                        },
                        getData: function () {
                            var _self = this;
                            _self.data_logs = [];
                            $.post("/agent/coin_table", {search: _self.search}, function (ret) {
                                if (ret.code === 200) {
                                    var total = {
                                        username: "合计",
                                        pay_in: 0,
                                        pay_out: 0,
                                        used: 0,
                                        send_coin: 0,
                                        fan_dian: 0,
                                        activity: 0,
										teamwin: 0,
                                        win: 0
                                    };

                                    for (x in ret.data) {
                                        ret.data[x]['date'] = x;

                                        //total
                                        total.pay_in += toDecimal(ret.data[x].pay_in);
                                        total.pay_out += toDecimal(ret.data[x].pay_out);
                                        total.used += toDecimal2(ret.data[x].used);
                                        total.send_coin += toDecimal(ret.data[x].send_coin);
                                        total.fan_dian += toDecimal(ret.data[x].fan_dian);
										total.teamwin += toDecimal(ret.data[x].teamwin);
                                        total.activity += toDecimal(ret.data[x].activity);
                                        total.win += toDecimal2(ret.data[x].win);
                                        _self.data_logs.push(ret.data[x]);
                                    }
                                    _self.data_logs.push(total);
                                }
                            })
                        }

                    },
                    mounted: function () {
                        var _self = this;
                        this.getData();
                    }
                });
            });
        });
    </script>
	<script type="text/javascript"> 
//保留两位小数 
//功能：将浮点数四舍五入，取小数点后2位 
function toDecimal(x) { 
var f = parseFloat(x); 
if (isNaN(f)) { 
return; 
} 
f = Math.round(x*100)/100; 
return f; 
}

function toDecimal2(v,e){
var t=1;
for(;e>0;t*=10,e--);
for(;e<0;t/=10,e++);
return Math.round(v*t)/t;
}
</script>
{/literal}
{/block}