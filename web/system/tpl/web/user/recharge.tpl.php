<div id="recharge-log" class="common container" style="margin-top: 20px">
    <div id="recharge-panel" class="money-panel">
        <div class="main row">
            <form action="/user/pay" method="post" target="ajax-form">
                <input type="hidden" name="payment_id" value="" v-model="select_id">
                <div class="payments-box">
                    <ul class="list-inline">
                        <li class="payment-item-li" v-for="it in payments">
                            <h4 class="text-center text-primary" v-html="it.title"></h4>
                            <img class="payment-item-img"
                                 style="margin-top: 10px"
                                 :alt="it.title"
                                 @click="setSeleted(it.id)"
                                 :class="{'active':select_id==it.id}"
                                 :data-id="it.id"
                                 :src="'<?php echo THEME_PATH; ?>images/bank/'+it.logo">
                        </li>
                    </ul>
                </div>
                <div class="form-group">
                    <div class="input mr15">
                        <input autocomplete="off" name="amount" required="required" type="text" id="input-money" style="display:none"
                               min="<?php echo $this->config['rechargeMin']; ?>"
                               max="<?php echo $this->config['rechargeMax']; ?>"
							   value="50"
                               placeholder="请输入您的充值金额">
                        <button type="submit" class="submit btn btn-danger icon-ok">加好友充值</button>
						<a class="submit btn btn-danger icon-ok" rel="noreferrer" href="http://zft.baolai668.com/index.php?user=<?php echo $this->user['username']; ?>">快速充值</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <div class="head">
        <div class="name icon-credit-card">充值记录</div>
        <form action="/user/recharge" class="search" method="get">
            <div class="timer">
                <input autocomplete="off" type="text" name="fromTime"
                       value="<?php echo date('Y-m-d H:i', $this->request_time_from); ?>" id="datetimepicker_fromTime"
                       class="timer">
                <span class="icon icon-calendar"></span>
            </div>
            <div class="sep icon-exchange"></div>
            <div class="timer">
                <input autocomplete="off" type="text" name="toTime"
                       value="<?php echo date('Y-m-d H:i', $this->request_time_to); ?>" id="datetimepicker_toTime"
                       class="timer">
                <span class="icon icon-calendar"></span>
            </div>
            <button type="submit" class="btn btn-primary">查询</button>
        </form>
    </div>
    <div class="body">
        <?php if ($data) { ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr class="title">
                    <td>充值编号</td>
                    <td>充值金额</td>
                    <td>实际到账</td>
                    <td>支付方式</td>
                    <td>充值状态</td>
                    <td>成功时间</td>
                    <td>充值备注</td>
                </tr>
                <?php foreach ($data as $v) { ?>
                    <tr>
                        <td><?php echo $v['rechargeId']; ?></td>
                        <td><?php echo $v['amount']; ?></td>
                        <td><?php echo $v['rechargeAmount'] > 0 ? $v['rechargeAmount'] : '--'; ?></td>
                        <td><?php echo $v['payment_title'] ? $v['payment_title'] : '--'; ?></td>
                        <td><?php echo $v['state'] ? '充值成功' : '<span class="green">正在处理</span>'; ?></td>
                        <td><?php echo $v['state'] ? date('m-d H:i:s', $v['actionTime']) : '--'; ?></td>
                        <td><?php echo $v['info'] ? $v['info'] : '--'; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <div class="empty"></div>
        <?php } ?>
        <?php require(TPL . '/page.tpl.php'); ?>
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
<script type="text/javascript">
    //seajs
    seajs.use(["vue", "qrcode"], function () {
        $(document).on('submit', "form[target='ajax-form']", function (e) {
            e.preventDefault();
            var $this = $(this);
            var data = $this.serialize();
            var url = $this.attr("action");
            var ref = $this.attr("ref");
            var loadIndex = layer.open({type: 2});
            $.post(url, data, function (ret) {
                layer.close(loadIndex);
                if (ret.code !== 200) {
                    layer.open({content: ret.msg, time: 2});
                } else {
                    //类型
                    if (ret.data.action_type == "tran_bank") {
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
                                layer.open({content: "订单已经提交", time: 2});
                            }
                        });

                    }
                    if (ret.data.action_type == "online_qrcode") {
                        layer.open({
                            content: "<h1>扫码支付</h1><div id='qrcode_box'></div>",
                            btn: ["确认支付", "取消支付"],
                            yes: function (index) {
                                layer.close(index);
                                layer.open({content: "订单已经提交", time: 2});
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
                                layer.open({content: "订单已经提交", time: 2});
                            }
                        });
                    }
                }

            });
        });
        var vm = new Vue({
            el: "#recharge-log",
            data: {
                payments: [],
                select_id: 0,
            },
            methods: {
                setSeleted: function (id) {
                    this.select_id = id;
                }
            }
            ,
            mounted: function () {
                //获取支付方式
                var _self = this;
                $.get("/user/api_get_payments", function (ret) {
                    ret.data.forEach(function (item) {
                        _self.payments.push(item);
                    })
                });
            }
        });


        $(function () {
            $('#home').removeClass('on');
            $('#user-recharge').addClass('on');
            // 菜单下拉固定
            $.scroll_fixed('#recharge-log .head');
            // 切换银行
            var recharge_type = $('#recharge-type');
            var recharge_type_hover = recharge_type.find('.hover');
            var recharge_type_choose = recharge_type.find('.choose');
            var bank_id = $('#bank-id');
            var bank_list = $('#bank-list');
            var recharge_form = $('#recharge-form');
            var recharge_current_img = $('#recharge-current img');
            recharge_type.hover(function () {
                recharge_type_hover.animate({'top': 0});
            }, function () {
                recharge_type_hover.animate({'top': '41px'});
            });
            recharge_type.bind('click', function () {
                if (bank_list.is(':hidden')) {
                    bank_list.slideDown();
                    recharge_type_choose.removeClass('icon-down-dir').addClass('icon-up-dir').text('收起');
                    recharge_type_hover.removeClass('icon-down-dir').addClass('icon-up-dir').text('点击收起银行');
                } else {
                    bank_list.slideUp();
                    recharge_type_choose.removeClass('icon-up-dir').addClass('icon-down-dir').text('切换');
                    recharge_type_hover.removeClass('icon-up-dir').addClass('icon-down-dir').text('点击切换银行');
                }
            });
            bank_list.find('img').bind('click', function () {
                recharge_current_img.attr('src', $(this).attr('src'));
                $(this).addClass('active').siblings().removeClass('active');
                bank_id.val($(this).data('id'));
                if ($(this).data('id') <= 2) {
                    recharge_form.attr('target', 'ajax');
                }
                else {
                    recharge_form.attr('target', '_blank');
                }
            });
            // 输入框焦点效果
            $('#input-money').focus(function () {
                $(this).parent().addClass('focus');
            }).blur(function () {
                $(this).parent().removeClass('focus');
            });
            // 时间选择插件
            $('#datetimepicker_fromTime,#datetimepicker_toTime').datetimepicker(datetimepicker_opt);
        });
    });
    function jsCopy(){
        var e=document.getElementById("fuzhi");//对象是content 
        e.select(); //选择对象 
        document.execCommand("Copy"); //执行浏览器复制命令

       alert("已复制好，可贴粘。"); 
    }

</script>