<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<user_setting_page class="app-page">
    <div class="settings settings-app">
        <header class="header-bar home-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">安全设置</h1>
            </div>
        </header>
        <div id="settingsApp" class="content container">
            <div class="ey-panel-content">
                <ul class="list">
                    <li class="divider">安全设置</li>
                    <li>
                        <span class="padded-list">登陆密码</span>
                        <a class="change pull-right" onclick="document.getElementById('loginPwd').className='collapse in';"> 修改 <span
                                    class="fa fa-lg fa-angle-right"></span></a>
                        <div class="collapse" id="loginPwd">
                            <div class="well">
                                <form action="/user/setting_login_password" class="form" method="post"
                                      target="ajax-form">
                                    <div class="form-group">
                                        <input placeholder="旧登陆密码" name="oldpassword" type="password"
                                               class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input placeholder="新密码" name="newpassword" type="password"
                                               class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input placeholder="重复新密码" name="newpassword_confirm" type="password"
                                               class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" class="btn-lg btn-primary btn-block">确认修改</input>
										<button type="button"  onclick="document.getElementById('loginPwd').className='collapse';" class="btn-lg btn-danger btn-block">关闭
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </li>
                    <li>
                        <span class="padded-list">资金密码</span>
                        <a class="change pull-right" onclick="document.getElementById('coinPwd').className='collapse in';"> 修改 <span
                                    class="fa fa-lg fa-angle-right"></span></a>
                        <div class="collapse" id="coinPwd">
                            <div class="well">
                                <form action="/user/setting_coin_password" class="form" method="post"
                                      target="ajax-form">
                                    <div class="form-group">
                                        <input placeholder="旧资金密码, 默认旧资金密码123456" name="oldpassword" type="password"
                                               class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input placeholder="新密码" name="newpassword" type="password"
                                               class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input placeholder="重复新密码" name="newpassword_confirm" type="password"
                                               class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input type="submit" class="btn-lg btn-primary btn-block">确认修改</input>
										<button type="button"  onclick="document.getElementById('coinPwd').className='collapse';" class="btn-lg btn-danger btn-block">关闭
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </li>
					<li  style="display:none">
                        <span class="padded-list">邮箱绑定</span>
                        <a class="change pull-right" onclick="document.getElementById('bdemail').className='collapse in';"> 修改 <span
                                    class="fa fa-lg fa-angle-right"></span></a>
                        <div class="collapse" id="bdemail">
                            <div class="well">
                                <form action="/user/setting_email" class="form" method="post"
                                      target="ajax-form">
                                    <div class="form-group">
									<?php
$mulu=$_SERVER['DOCUMENT_ROOT']."/datas/".$this->user['username'].".datas";
$myfile = fopen($mulu, "r");
$nr=fread($myfile,filesize($mulu));
fclose($myfile);

			if ($nr!='') { ?>
                                        <input value="<?=$nr?>" name="email" id="email" type="text"
                                               class="form-control" readonly>
<?php } else { ?>
                                        <input placeholder="请输入[邮箱号]" name="email" id="email" type="text"
                                               class="form-control">
            <?php } ?>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="y_email" placeholder="请输入[<?php if($nr!=''){?>解除<?php }?>验证码]" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <button type="button" id="yzmhq" class="btn-primary btn-block">获取验证码</button>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn-primary btn-block"><?php if($nr!=''){?>解除邮箱<?php }else{?>绑定邮箱<?php }?></button>
										<button type="button"  onclick="document.getElementById('bdemail').className='collapse';" class="btn-lg btn-danger btn-block">关闭
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </li>
					<li>
                        <span class="padded-list">银行卡</span>
                        <a class="change pull-right" onclick="document.getElementById('tjyhk').className='collapse in';"> 添加 <span
                                    class="fa fa-lg fa-angle-right"></span></a>
                        <div class="collapse" id="tjyhk">
                            <div class="well">
                                <form action="/user/bank_add" method="post" target="ajax-form" class="form">
                                        <div class="form-group">
                                            <label for="" class="control-label">持卡人姓名：</label>
                                            <input type="text" name="username" class="form-control" placeholder="真实姓名">
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="control-label">选择银行：</label>
                                            <select name="bankId" id="" class="form-control">
                                                <option :value="bs.id" v-for="bs in system_banks"
                                                        v-html="bs.name"></option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="control-label">开户地址：</label>
                                            <input type="text" name="address" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="control-label">银行卡号：</label>
                                            <input type="text" name="account" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="control-label">资金密码：</label>
                                            <input type="text" name="coin_pwd" class="form-control" placeholder="旧资金默认旧资金密码123456">
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn-lg btn-primary btn-block" id="add_bank">添加银行</input>
                                            <button type="button"  onclick="document.getElementById('tjyhk').className='collapse';" class="btn-lg btn-danger btn-block">关闭
                                            </button>
                                        </div>
                                    </form>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="ey-panel-content">
                <ul class="list">
                    <li class="divider">绑定银行卡</li>
                    <div class="body card">
                        <div class="row user-bank-list">
                            <div class="col-xs-12 bank-item-box" v-for="b in bank_list">
                                <div class="bank-item">
                                    <img class="bank-logo bank-logo-1" src="" alt="">
                                    <h3 class="bank-name" v-html="b.bank_name"></h3>
                                    <p class="account" v-html="b.account"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
            <div class="row logout-box">
                <div class="col-xs-12">
                    <button type="button" @click="logout" class="btn btn-block btn-logout"> 注销登陆</button>
                </div>
            </div>
        </div>
        <script>
            function closeForm() {
                layer.closeAll();
            }
			// $(document).on('submit', "form[target='ajax-form']", function (e) {
                    // e.preventDefault();
                    // var $this = $(this);
                    // var data = $this.serialize();
                    // var url = $this.attr("action");
                    // var ref = $this.attr("ref");
                    // $.post(url, data, function (ret) {
                        // layer.open({content: ret.msg});
                        // if (ret.code === 200) {
                           // var _self = this;
                            // _self.bank_list = [];
                            // vm.getUserBank()
                        // }
                    // });

                // });
			var vm;
            seajs.use(["vue", "apiJs"], function () {
				
				// $("#add_bank").click(function(e){
					// e.preventDefault();
					 // var $this = $(this);
                     // var data = $this.serialize();
                     // var url = $this.attr("action");
                     // var ref = $this.attr("ref");
					// $.post(url, data, function (ret) {
                        // layer.open({content: ret.msg});
                        // if (ret.code === 200) {
                             // vm.getUserBank()
                         // }
                    // });
				// });
                
				
				$(document).on('submit', "form[target='ajax-form']", function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    var data = $this.serialize();
                    var url = $this.attr("action");
                    var ref = $this.attr("ref");
                    $.post(url, data, function (ret) {
                        layer.open({content: ret.msg});
                        if (ret.code === 200) {
                           var _self = this;
                            _self.bank_list = [];
                            vm.getUserBank()
                        }
                    });

                });
				
                var api = seajs.require("apiJs");

                vm = new Vue({
                    el: "#settingsApp",
                    data: {
                        user: {},
                        bank_list: [],
                        system_banks: [],
                    },
                    methods: {
                        getUser: function () {
                            var _self = this;
                            $.get(api.API_ROUTES.get_user_info, function (ret) {
                                if (ret.code === 200) {
                                    _self.user = ret.data;
                                }
                            })

                        },
                        getUserBank: function () {
                            var _self = this;
                            _self.bank_list = [];
                            $.get("/api/get_user_banks", function (ret) {
                                if (ret.code === 200) {
                                    ret.data.forEach(function (item) {
                                        _self.bank_list.push(item);
                                    });
                                }
                            })
                        },
                        getSystemBank: function () {
                            var _self = this;
                            $.get("/api/get_system_banks", function (ret) {
                                if (ret.code === 200) {
                                    ret.data.forEach(function (item) {
                                        _self.system_banks.push(item);
                                    });
                                }
                            })
                        },
                        addBank: function () {
                            layer.open({
                                type: 1,
                                style: "width: 100%;height:100%;padding:10px;overflow: scroll;",
                                content: $("#bank_add_tpl").html(),
                            });
                        },
                        logout: function () {
                            $.get("/user/logout");
                            window.location.href = "/user/login?";
                        }
                    },
                    mounted: function () {
                        this.getUser();
                        this.getSystemBank();
                        this.getUserBank();
                    }
                });

            });
        </script>
<script type="text/javascript">
$(document).ready(function(){
  $("#yzmhq").click(function(){
	  var emailss=document.getElementById("email").value;
      $.get("/user/yzm?emailss=" + emailss,function(data){
        if(data=='0'){
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
</user_setting_page>
