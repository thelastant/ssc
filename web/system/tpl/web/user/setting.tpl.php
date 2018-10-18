<div id="user-setting-dom" class="common container">
    <div class="head">
        <div class="name icon-user">个人基本信息</div>
    </div>
    <div class="body">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="key">账户名称</td>
                <td class="val"><?php echo htmlspecialchars($this->user['username']); ?></td>
                <td class="key">账户等级</td>
                <td class="val">VIP<?php echo $this->user['grade']; ?></td>

            </tr>
            <tr>
                <td class="key">账户类型</td>
                <td class="val"><?php echo $this->user['type'] ? '代理' : '会员'; ?></td>
                <td class="key">上级代理</td>
                <td class="val"><?php
                    $parent_id = $this->user['parentId'];
                    $parent = $parent_id ? $this->db->query("SELECT `username` FROM `{$this->db_prefix}members` WHERE `uid`=$parent_id LIMIT 1", 2) : null;
                    echo $parent ? htmlspecialchars($parent['username']) : '无';
                    ?></td>
            </tr>
            <tr>
                <td class="key">可用积分</td>
                <td class="val"><?php echo $this->user['score']; ?></td>
                <td class="key">可用资金</td>
                <td class="val"><?php echo $this->user['coin']; ?> 元</td>
            </tr>
            <tr>
                <td class="key">返点比例</td>
                <td class="val"><?php echo $this->user['fanDian']; ?> %</td>
                <td class="key">日工资比</td>
                <td class="val"><?php echo $this->user['day_rate']; ?> %</td>
            </tr>
            <tr>
                <td class="key">注册时间</td>
                <td class="val"><?php echo date('Y-m-d H:i:s', $this->user['regTime']); ?></td>
                <td class="key">最后登录</td>
                <td class="val"><?php echo $this->user['updateTime']; ?></td>
            </tr>
        </table>
    </div>
    <div class="head">
        <div class="name icon-key">密码管理</div>
        <div class="desc">如果不修改密码，请忽略此项</div>
    </div>
    <div class="body password">
        <form method="POST" action="/user/setting_login_password" class="mb ajax-form" target="ajax-form">
            <div class="pwd_name">登录密码：</div>
            <input type="password" name="oldpassword" placeholder="请输入[当前登录密码]">
            <input type="password" name="newpassword" placeholder="请输入[新登录密码]">
            <input type="password" name="newpassword_confirm" placeholder="请重复输入[新登录密码]">
            <button type="submit" class="btn btn-blue">修改登录密码</button>
        </form>
        <form method="POST" action="/user/setting_coin_password" class="mb ajax-form" target="ajax-form">
            <div class="pwd_name">资金密码：</div>
            <?php if (empty($this->user['coinPassword'])) { ?>
                <input type="password" name="oldpassword" placeholder="[资金密码]未设置，此项不需填写" readonly>
            <?php } else { ?>
                <input type="password" name="oldpassword" placeholder="请输入[当前资金密码] 默认旧资金密码123456">
            <?php } ?>
            <input type="password" name="newpassword" placeholder="请输入[新资金密码]">
            <input type="password" name="newpassword_confirm" placeholder="请重复输入[新资金密码]">
            <button type="submit" class="btn btn-green">修改资金密码</button>
        </form>
		<form method="POST" action="/user/setting_email" class="ajax-form" target="ajax-form" style="display:none">
            <div class="pwd_name"><?php if($nr!=''){?>解除邮箱<?php }else{?>绑定邮箱<?php }?>：</div>
            <?php
$mulu=$_SERVER['DOCUMENT_ROOT']."/datas/".$this->user['username'].".datas";
$myfile = fopen($mulu, "r");
$nr=fread($myfile,filesize($mulu));
fclose($myfile);

			if ($nr!='') { ?>
                <input type="text" name="email" id="email" value="<?=$nr?>" readonly>
            <?php } else { ?>
                <input type="text" name="email" id="email" placeholder="请输入[邮箱号]">
            <?php } ?>
            <input type="text" name="y_email" placeholder="请输入[<?php if($nr!=''){?>解除<?php }?>验证码]">
			<button type="button" id="yzmhq" class="btn btn-green" style="float: left;margin-left: 10px;">获取验证码</button>
            <button type="submit" class="btn btn-green"><?php if($nr!=''){?>解除邮箱<?php }else{?>绑定邮箱<?php }?></button>
        </form>
    </div>
    <div class="head">
        <div class="name icon-credit-card">银行账户</div>
        <div class="desc">为了您的账户安全，确认银行账户后只能通过联系客服修改</div>
    </div>
    <style>
        .layer-window {
            height: 520px;
            width: 400px;

        }

        .layer-window-content {
            padding: 15px;
        }

        .layer-window-header {
            color: #fff;
            height: 60px;
            background: rgba(0, 0, 0, 0.75);
            border-bottom: 3px solid #337ab7;
        }

        .layer-window-header h3 {
            line-height: 60px;
            margin: 0;
            padding-left: 20px;
            border-bottom: none;
        }

        .bank-item {
            margin-bottom: 15px;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .add-bank {
            cursor: pointer;
        }
    </style>
    <div class="body card" id="bankApp">
        <div class="row user-bank-list">
            <div class="col-xs-3" v-for="b in bank_list">
                <div class="bank-item">
                    <img class="bank-logo bank-logo-1" src="" alt="">
                    <h3 class="bank-name" v-html="b.bank_name"></h3>
                    <p class="account" v-html="b.account"></p>
                </div>
            </div>
            <div class="col-xs-3">
                <div class="bank-item add-bank">
                    <a @click="addBank();">添加银行卡</a>
                </div>
            </div>
        </div>
        <div class="hidden" id="bank_add_tpl">
            <div class="layer-window">
                <div class="layer-window-header">
                    <h3>添加银行卡
                        <small class="text-danger">您最多添加10张卡</small>
                    </h3>
                </div>
                <div class="layer-window-content">
                    <form action="/user/bank_add" method="post" target="ajax-form" class="form">
                        <div class="form-group">
                            <label for="" class="control-label">持卡人姓名：</label>
                            <input type="text" name="username" class="form-control" placeholder="真实姓名">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">选择银行：</label>
                            <select name="bankId" id="" class="form-control">
                                <option :value="bs.id" v-for="bs in system_banks" v-html="bs.name"></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">开户地址(微信名或支付宝名)：</label>
                            <input type="text" name="address" placeholder="" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">银行卡号(微信账号或支付宝账号)：</label>
                            <input type="text" name="account" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">资金密码：</label>
                            <input type="text" name="coin_pwd" class="form-control">
                        </div>

                        <div class="form-group">
                            <button class="btn btn-lg btn-primary btn-block">添加银行</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use(["vue", "layer", "layerCss", "publicJs"], function () {
        var publicFunc = seajs.require("publicJs");

        var bankApp = new Vue({
            el: "#bankApp",
            data: {
                bank_list: [],
                system_banks: [],
            },
            methods: {
                getUserBank: function () {
                    var _self = this;
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
                        content: $("#bank_add_tpl").html(),
                    });
                }
            },
            mounted: function () {
                this.getSystemBank();
                this.getUserBank();
            }
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
                    publicFunc.WindowReload(1200);
                }
            });

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