<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<user_pay_out class="app-page">
    <div class="settings">
        <header class="header-bar home-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">提现</h1>
            </div>
        </header>
        <style>
            .mark {
                display: none
            }
        </style>
        <div id="payOutApp" class="content container" style="background: #fff;">
            <div class="ey-panel-content">
                <div class="well">
                    <form action="/user/api_cash_submit" class="form form-inline" method="post" target="ajax-form1">
                        <div class="form-group">
                            <label for="" class="control-label">选择提现账户</label>
                            <select name="bank_id" id="chose_account">
                                <option value="" >选择提现账户</option>
                                <option v-for="it in user_banks" :data-id="it.id"
                                        v-html="it.bank_name+'-'+it.username"
                                        :value="it.id"></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">资金密码</label>
                            <input type="password" class="form-control" name="coin_pwd" value="" placeholder="请输入资金密码 默认为123456">
                        </div>
						<div class="form-group" style="display: none">>
                            <label for="" class="control-label">验证码</label>
                            <input type="text" class="form-control" name="y_email" value="" placeholder="输入邮箱验证码">
							<button type="button" id="yzmhq" class="btn primary btn-block" style="margin-top: 15px;">获取验证码</button>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">提现金额</label>
                            <input placeholder="提现金额" name="money"
                                   min="<?php echo $this->config['rechargeMin']; ?>"
                                   max="<?php echo $this->config['rechargeMax']; ?>"
                                   type="number" class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn primary btn-block" >确认提现</button>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="table-box">
                        <div class="row table-head">
                            <div class="phone-3 column">提现时间</div>
                            <div class="phone-3 column">提现账户</div>
                            <div class="phone-3 column">金额（元）</div>
                            <div class="phone-3 column">状态</div>
                        </div>
                        <div class="row table-body" v-for="vo in pay_logs">
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
        <script>

	
            seajs.use(["vue", "moment"], function () {
                $(document).on('submit', "form[target='ajax-form1']", function (e) {
					
                    e.preventDefault();
					
                    var $this = $(this);
                    var data = $this.serialize();
                    var url = $this.attr("action");
                    var ref = $this.attr("ref");
                    $.post(url, data, function (ret) {
                        if (ret.code !== 200) {
                            layer.open({content: ret.msg, time: 2});
                        } else {
                            layer.open({content: ret.msg, time: 2});
                        }

                    });

                });
				


				
                var vm = new Vue({
                    el: "#payOutApp",
                    data: {
                        user_banks: [],
                        select_id: 0,
                        pay_logs: [],
                    },
				
                    methods: {
						
                        setSeleted: function (id) {
                            this.select_id = id;
							console.log("bb"+this.select_id);
                        },
						
						timer : function(){
							
							//var sel_id = this.select_id; 
							console.log($("#chose_account").val());
							if("" != $("#chose_account").val()) return;
							 var _self = this;
							  _self.user_banks = []
								$.get("/api/get_user_banks", function (ret) {
									ret.data.forEach(function (item) {
										_self.user_banks.push(item);
									})
								});
							
							_self.getPayInLogs();
						},
                        getPayInLogs: function () {
                            var _self = this;
                            _self.pay_logs = [];
                            $.post("/user/api_get_recharge", {}, function (ret) {
                                if (200 === ret.code) {
                                    ret.data.forEach(function (item) {
                                        _self.pay_logs.push(item);
                                    });
                                }
                            });

                        },
                        parseDate: function (time) {
                            return moment(time * 1000).format("YYYY-MM-DD..");
                        },
                    },
                    mounted: function () {
						 

                        var _self = this;
                        $.get("/api/get_user_banks", function (ret) {
                            ret.data.forEach(function (item) {
                                _self.user_banks.push(item);
                            })
                        });
                        _self.getPayInLogs();
						setInterval(this.timer, 3000);
                    }
					
					
					
                });
				
		$("chose_account").click(function(){
			 console.log("aa");
			//vm.mounted()
		});
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
    </div>
</user_pay_out>
