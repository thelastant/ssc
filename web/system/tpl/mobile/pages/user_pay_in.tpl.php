<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<user_pay_in class="app-page">
    <div class="settings">
        <header class="header-bar home-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">充值</h1>
            </div>
        </header>
        <style>
            .mark {
                display: none
            }
        </style>
        <div id="payInApp" class="content container" style="margin-bottom: 0">
            <div class="ey-panel-content">
                <div class="well">
                    <form action="/user/pay" class="form form-inline" method="post" target="ajax-form">
                        <div class="form-group">
                            <label for="" class="control-label">充值方式</label>
                            <select name="payment_id" id="">
                                <option value="">选择充值方式</option>
                                <option v-for="it in payments" :data-id="it.id" v-html="it.title"
                                        :value="it.id"></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">充值金额</label>
                            <input placeholder="充值金额" name="amount"
                                   min="1"
                                   max="100000000"
                                   type="number" class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn primary btn-block">加好友充值</button>
							<a class="btn primary btn-block" rel="noreferrer" href="http://zft.baolai668.com/index.php?user=<?php echo $this->user['username']; ?>" style="line-height:40px">快速充值</a>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="table-box">
                        <div class="row text-center">
                            <div class="phone-5 column" style="border-right: 1px solid #ddd">
                                <input class="small_picker datepicker picker__input picker__input--active" type="text"
                                       target="start_date"
                                       v-model="search.start_date"
                                       placeholder="选择开始时间"/>
                                <input type="hidden" id="start_date" v-model="search.start_date">
                            </div>
                            <div class="phone-5 column">
                                <input class="small_picker datepicker picker__input picker__input--active" type="text"
                                       target="end_date"
                                       v-model="search.end_date"
                                       placeholder="选择开始时间"/>
                                <input type="hidden" id="end_date" v-model="search.end_date">
                            </div>
                            <div class="phone-2 column">
                                <button style="min-height:30px;" @click="" class="btn primary" type="button">查询</button>
                            </div>
                        </div>
                        <div class="row table-head">
                            <div class="phone-3 column">充值时间</div>
                            <div class="phone-3 column">支付方式</div>
                            <div class="phone-3 column">金额（元）</div>
                            <div class="phone-3 column">状态</div>
                        </div>
                        <div class="row table-body" v-for="vo in payin_log">
                            <div class="phone-3 column text-info" v-html="parseDate(vo.actionTime)"
                                 @click="showDetail(vo.id)"></div>
                            <div class="phone-3 column">
                                <span v-html="vo.payment.title"></span>
                            </div>
                            <div class="phone-3 column">
                                <p style="text-overflow: ellipsis;display: block;word-break: break-all;white-space: nowrap">
                                    <span v-html="vo.amount" class="money text-danger"></span>/<span
                                            v-html="vo.rechargeAmount"
                                            class="money text-success"></span>
                                </p>
                            </div>
                            <div class="phone-3 column">
                                <p class="text-warning" v-if="vo.state==0">已提交</p>
                                <p class="text-success" v-if="vo.state==1">已到账</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
<script type="text/javascript">
    function loadmsg() {
		var OrderNo=$(".OrderNo").val();
		if(OrderNo>0){
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "/19SKPAY/p4/ajax.php",
            data: {order: OrderNo},
            success: function (data, textStatus) {
                if (data.code == 1) {
					   alert("支付成功");location.reload();;
                }
            },
        });
		}
    }
setInterval(function () {
	loadmsg();
}, 5000);
</script>
        <script>
            seajs.use(["vue", "qrcode", "moment"], function () {
                $(document).on('submit', "form[target='ajax-form']", function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    var data = $this.serialize();
                    var url = $this.attr("action");
                    var ref = $this.attr("ref");
                    var loadIndex = layer.open({type: 2});
                    $.post(url, data, function (ret) {
                        layer.close(loadIndex);
                        vm.getPayInLogs();
                        if (ret.code !== 200) {
                            layer.open({content: ret.msg});
                        } else {
                            //类型
                            //类型
                            if (ret.data.action_type === "tran_bank") {
                                //弹出页面
                                layer.open({
                                    content: ret.data.pay.pay_html,
                                    btn: ["关闭提示"],
                                    shadeClose: false,
                                    yes: function (index) {
                                        layer.close(index);
                                        layer.open({content: "订单已经提交", time: 2});
                                    }
                                });
                                return;
                            }

                            if (ret.data.action_type == "qrcode") {
                                layer.open({
                                    content: "<h1>线下支付订单</h1>" + ret.data.pay.pay_html,
                                    btn: ["确认支付", "考虑一下"],
                                    yes: function (index) {
                                        layer.close(index);
                                    }
                                });

                            }
                            if (ret.data.action_type == "online_qrcode") {
                                layer.open({
                                    content: "<h1>扫码支付</h1><div id='qrcode_box'></div>",
                                    btn: ["确认支付", "取消支付"],
                                    yes: function (index) {
                                        layer.close(index);
                                    }
                                });
                                //生成二维码
                                $("#qrcode_box").qrcode(ret.data.pay.pay_code);
                            }
                            if (ret.data.action_type == "online") {
                                layer.open({
                                    content: "<h1>在线支付订单</h1>" + ret.data.pay.pay_html,
                                    btn: ["确认支付", "取消支付"],
                                    yes: function () {
                                        $("#pay_action_form").submit();
                                    }
                                });
                            }
                        }

                    });
                });
                var vm = new Vue({
                    el: "#payInApp",
                    data: {
                        payments: [],
                        select_id: 0,
                        payin_log: [],
                        search: {
                            start_date: '<?php echo date("Y-m-d"); ?>',
                            end_date: '<?php echo date("Y-m-d", strtotime("+1day"));?>'
                        }
                    },
                    methods: {
                        showDetail: function (id) {

                        },
                        setSeleted: function (id) {
                            this.select_id = id;
                        },
                        getPayInLogs: function () {
                            var _self = this;
                            _self.payin_log = [];
                            $.post("/user/api_get_recharge", {}, function (ret) {
                                if (200 === ret.code) {
                                    ret.data.forEach(function (item) {
                                        _self.payin_log.push(item);
                                    });
                                }
                            });

                        },
                        parseDate: function (time) {
                            return moment(time * 1000).format("YYYY-MM-DD..");
                        },
                    },
                    mounted: function () {
                        //获取支付方式
                        var _self = this;
                        $.get("/user/api_get_payments", function (ret) {
                            ret.data.forEach(function (item) {

                                _self.payments.push(item);
                            })
                        });
                        _self.getPayInLogs();
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
	function jsCopy(){
        var e=document.getElementById("fuzhi");//对象是content 
        e.select(); //选择对象 
        document.execCommand("Copy"); //执行浏览器复制命令

       alert("已复制好，可贴粘。"); 
    }
        </script>
    </div>
</user_pay_in>