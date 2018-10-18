<div class="container" style="margin-top: 20px;">
    <div id="cash-panel" class="money-panel common">
        <div class="main">
            <form action="/user/api_cash_submit" class="form-inline" target="ajax-form" method="post">
                <div class="form-group">
                    <select name="bank_id" id="" class="form-control input-lg">
                        <?php foreach ($banks as $bank) { ?>
                            <option value="<?php echo $bank['id']; ?>">
                                <p><?php echo $bank['bank_name']; ?></p>
                                <p><?php echo $bank['account']; ?></p>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <input class="form-control input-lg" autocomplete="off" type="number" name="money"
                           value=""
                           min="0"
                           placeholder="请输入您的提现金额">
                </div>
                <div class="form-group">
                    <input class="form-control input-lg" type="password"
                           name="coin_pwd"
                           placeholder="请输入资金密码">
                </div>
				<div class="form-group" style="display: none">
                    <input class="form-control input-lg" type="text" name="y_email" placeholder="输入邮箱验证码" style="width: 160px;">
                </div>
                <div class="form-group" style="display: none">
                    <button type="button" id="yzmhq" class="submit btn btn-lg btn-primary icon-ok" style="width: 140px;">获取验证码</button>
                </div>
				<div class="form-group">
                    <button type="submit" class="submit btn btn-lg btn-primary icon-ok">提现</button>
                </div>
            </form>
        </div>
        <div id="cash-intro" class="addon<?php if (!$enable['result']) echo ' nb'; ?>" >
            <?php if (!$enable['result']) { ?>
                <div class="tip icon-attention-alt" style="display: none"><?php echo $enable['reason']; ?><span class="triangle"></span></div>
            <?php } ?>
            <ul class="list">
                <li>
                    新卡绑定6小时才能提款
                </li>
                <li>您是尊贵的<span class="btn btn-red">VIP <?php echo $this->user['grade']; ?></span>用户，每天提现次数上限为<span
                            class="btn btn-green"><?php echo $info['times_limit']; ?></span>次，今天您已经提交<span
                            class="btn btn-blue"><?php echo $info['times']; ?></span>次申请；
                </li>
                <li>每天受理提现请求的时间段为<span class="color blue"><?php echo $this->config['cashFromTime']; ?>
                        ~ <?php echo $this->config['cashToTime']; ?></span>；
                </li>
                <li>提现金额最小为<span class="color red"><?php echo $this->config['cashMin']; ?></span>元，最大为<span
                            class="color red"><?php echo $this->config['cashMax']; ?></span>元；
                </li>
                <li>消费比例公式：今日消费比例=今日投注量/今日充值额，消费比例未达到<?php echo $this->config['cashMinAmount']; ?>%则不能提现；</li>
                <li>如果今日未充值，则消费比例默认为100%，即使未投注也可随时提款（系统是从当天凌晨0点至第二天凌晨0点算一天）；</li>
                <li>今日投注<span class="color green"><?php echo $info['amount_bets']; ?></span>元，今日充值<span
                            class="color blue"><?php echo $info['amount_recharge']; ?></span>元，您今日消费比例已达到<span
                            class="color red"><?php echo $info['proportion']; ?>%</span>；
                </li>
            </ul>
        </div>
    </div>
    <div id="cash-log" class="common">
        <div class="head">
            <div class="name icon-paper-plane">提现记录</div>
        </div>
        <div class="body"><?php require(TPL . '/user/cash_body.tpl.php'); ?></div>
    </div>
</div>
<script type="text/javascript">
    seajs.use(["layer", "layerCss"], function () {
        //
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
</script>
<script type="text/javascript">
$(document).ready(function(){
  $("#yzmhq").click(function(){
      $.get("/user/txyzm",function(data){
        if(data=='-1'){
			alert('为了您的账号安全，请绑定邮箱后操作');
		}else if(data=='0'){
			alert('电子邮箱格式错误');
		}else if(data=='1'){
			$("#yzmhq").hide();
			alert('验证码发送成功');
		}else if(data=='2'){
			alert('验证码发送失败');
		}
      });
  });
});
</script>