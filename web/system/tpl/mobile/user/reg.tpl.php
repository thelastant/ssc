<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <title><?php echo $this->config['webName']; ?>-开放注册</title>
    <script>
        window.SYSTEM_SETTINGS = {
            version: "<?php echo $this->version; ?>",
        }
    </script>
    <link href="<?php echo STATIC_PATH; ?>libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <script src="<?php echo STATIC_PATH . 'libs/seajs/sea.js?v=' ?><?php echo $this->version; ?>"></script>
    <script src="<?php echo STATIC_PATH . 'libs/seajs/sea-plugin.js?v=' ?><?php echo $this->version; ?>"></script>
    <script src="<?php echo THEME_PATH . 'sea-conf.js?v=' ?>"><?php echo $this->version; ?></script>
    <script>
        seajs.use(["signCss", "signJs", "fontAwesome"]);
    </script>
    <style>

    </style>
</head>
<body>

<div id="sign-box">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-7">
                <div class="sign-body">
                    <div class="content">
                        <h1 class="logo-box">
                            <div class="logo"></div>
                        </h1>
                        <h1 class="action-title">开放注册</h1>
                        <form id="form" method="post" action="/user/reg" target="ajax-form">
                            <?php
                            if (array_key_exists("pid", $_GET)) {
                                ?>
                                <input type="hidden" value="<?php echo intval($_GET["pid"]); ?>" name="pid">
                                <?php
                            }
                            ?>
                            <div class="form-group">
                                <div class="icon">
                                    <i class="fa fa-user"></i>
                                </div>
                                <input type="text"
                                       autocomplete="off"
                                       name="username"
                                       placeholder="请输入账户名"
                                       class="form-control">
                            </div>
                            <div class="form-group">
                                <div class="icon">
                                    <i class="fa fa-lock"></i>
                                </div>
                                <input type="password"
                                       autocomplete="off"
                                       class="form-control"
                                       name="pwd" placeholder="请输入登录密码" title="登录密码">
                            </div>
                            <div class="form-group">
                                <div class="icon">
                                    <i class="fa fa-lock"></i>
                                </div>
                                <input type="password"
                                       autocomplete="off"
                                       class="form-control"
                                       name="re_pwd" placeholder="重复登陆密码" title="登录密码">
                            </div>
							<div class="form-group">
                                <div class="icon">
                                    <i class="fa fa-lock"></i>
                                </div>
                                 <input type="text"
                                       autocomplete="off"
                                       name="qq"			
                                       placeholder="请输入真实QQ或微信"
                                       class="form-control">
                            </div>
                            <div class="form-group verify-box">
                                <div class="icon">
                                    <i class="fa fa-image"></i>
                                </div>
                                <input type="text"
                                       autocomplete="off"
                                       name="verify_code"
                                       id="verify_code"
                                       class="form-control" placeholder="请输入验证码">

                                <img class="verify-img" id="verify-img" src="/user/get_verify?rand=100" alt=""
                                     onclick="changeVerify(this);">
                            </div>
                            <div class=" text-center">
                                <label for="" class="xy">
                                    <input type="checkbox" checked="checked">
                                    <span>我已阅读并同意<?php echo $this->config["webName"]; ?>平台游戏协议</span>
                                </label>
                            </div>
                            <div>
                                <button id="submit" type="submit" class="btn btn-block btn-primary btn-lg"
                                        style="width: 80%;margin-left: 10%">
                                    <span>确认注册</span>
                                </button>
                            </div>
                        </form>
                    </div>
                    <a class="btn btn-block btn-login" href="/user/login?">
                        <span>立即登陆</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>