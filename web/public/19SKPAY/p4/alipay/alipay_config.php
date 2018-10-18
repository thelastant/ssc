<?php
$partner = $amh_user_hmpay_id;   //财付通商户号
$key = $amh_user_hmpay_key;		
$return_url = "http://".$_SERVER['HTTP_HOST']."/19SKPAY/p4/alipay/payReturnUrl.php";			//显示支付结果页面,*替换成payReturnUrl.php所在路径
$notify_url = "http://".$_SERVER['HTTP_HOST']."/19SKPAY/p4/alipay/payNotifyUrl.php";			//支付完成后的回调处理页面,*替换成payNotifyUrl.php所在路径
?>