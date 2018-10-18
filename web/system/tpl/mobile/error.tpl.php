<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit"/>
    <title>Request Error</title>
    <style type="text/css">
        body {
            font: 12px 'Century Gothic', 'Microsoft YaHei', \5FAE\8F6F\96C5\9ED1, Tahoma, Verdana, Arial, Arial, helvetica, sans-serif;
            background-color: #F5F5F5;
            margin: 0
        }

        #error {
            border: 1px solid #DDD;
            box-shadow: 0 0 5px 0 #DDD;
            width: 80%;
            margin-left: 10%;
            margin-top: 20%;
            background-color: #FFF;
            border-top: 2px solid #5990C4;
        }

        #error .head {
            line-height: 34px;
            padding: 10px 15px;
            border-bottom: 1px solid #DDD;
        }

        #error .head .name {
            color: #5990C4;
            font-size: 15px;
            float: left
        }

        .btn-danger {
            margin-top: 10px;
        }

        a {
            text-decoration: none;
            -webkit-transition: all .218s linear;
            -moz-transition: all .218s linear;
            transition: all .218s linear
        }

        #error .head .back:hover {
            color: #777;
            border-color: #C5C5C5;
            background-color: #F2F2F2
        }

        #error .head .back:active {
            background-color: #EDEDED;
            box-shadow: inset 0 0 5px #D4D4D4
        }

        #error .body {
            border-top: 1px solid #EDEDED
        }

        #error .body .container {
            line-height: 24px;
            padding: 30px 15px 20px;
            border-bottom: 1px solid #DDD
        }

        #error .body .error {
            background-color: #FFECEC
        }

        #error .body .success {
            background-color: #EEFFEC
        }

        #error .body .container .errtit {
            font-size: 23px;
            color: #666
        }

        #error .body .container .errmsg {
            font-size: 13px;
            margin-top: 10px;
            color: #999
        }

        #error .footer {
            background-color: #F9F9F9;
            padding: 15px;
            color: #AAA;
            text-align: right
        }
    </style>
    <link href="/static/libs/bootstrap/css/bootstrap.min.css"
          rel="stylesheet"
          type="text/css"/>
</head>
<body>
<div id="error" class="container">
    <div class="head row">
        <div class="name">系统错误提示</div>
    </div>
    <div class="body row">
        <div class="container error">
            <div class="errtit">200 . Request Error</div>
            <div class="errmsg"><?php echo $msg; ?></div>
            <a href="javascript:history.back(-1)" class="btn btn-danger btn-block">返回 <span id="timer"></span></a>
        </div>
        <?php
        if ($url != '') {
            ?>
            <script>
                setTimeout(function () {
                    window.location.href = "<?php echo $url;?>";
                }, 1600);
            </script>
        <?php }
        ?>
    </div>
    <div class="footer">&copy; <?php echo date('Y'); ?>版权所有</div>
</div>
<body>
</html>