<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <title><?php echo $this->config['webName']; ?>-登陆</title>
    <script>
        window.SYSTEM_SETTINGS = {
            version: "<?php echo $this->version; ?>",
        };
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
                        <h1 class="action-title">用户登陆</h1>
                        <form id="form" method="post" action="" target="ajax-form">
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
                            <div style="padding-left: 10%;" class="visible-xs">
                                <input class="mui-switch mui-switch-animbg pull-left"
                                       value="1"
                                       name="app_mode" type="checkbox"
                                       checked="checked">
                                <label style="line-height: 30px;color: #fff;margin-top: 5px;margin-left: 10px;">移动端模式</label>
                            </div>
                            <div>
                                <button id="submit" type="submit" class="btn btn-block btn-primary btn-lg"
                                        style="width: 80%;margin-left: 10%">
                                    <span>登 &nbsp;&nbsp;&nbsp;录</span>
                                </button>
                            </div>
                        </form>
                    </div>
                    <a class="btn btn-block btn-reg" href="/user/reg?" >
                        <span>立即注册</span>
                    </a>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>