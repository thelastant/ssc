<?php
include('../../yx_inc/fx_conn.php');
$userid = $amh_user_hmpay_id; //商户会员号
$key = $amh_user_hmpay_key;//密钥
$returnurl = "http://".$_SERVER[HTTP_HOST]."/payment/qpay/returnUrl.php";//异步通知地址
?>