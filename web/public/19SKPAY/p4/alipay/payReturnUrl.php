<?php
include($_SERVER['DOCUMENT_ROOT'].'/19SKPAY/function.php');
require_once($_SERVER['DOCUMENT_ROOT']."/19SKPAY/19sk.config.php");
require_once("alipay_config.php");

$get_order=YJSK_PAPI_GetSafeParam("OrderNo","",YJSK_XH_PARAM_INT);
$get_trade_status=YJSK_PAPI_GetSafeParam("trade_status","",YJSK_XH_PARAM_TXT);
$get_money=YJSK_PAPI_GetSafeParam("Money","",YJSK_XH_PARAM_TXT);
$get_sign=YJSK_PAPI_GetSafeParam("sign","",YJSK_XH_PARAM_TXT);

$Sign=md5($get_order."&Money=".$get_money."&trade_status=".$get_trade_status."&key=".$key);
$Signmd5=md5("MerchantID=".$partner."&OrderNo=".$get_order."&key=".$key);
$url = 'http://www.26sk.cn/payment/apipay/alipay/PayOnlieQuery.php?MerchantID='.$partner.'&OrderNo='.$get_order.'&Sign='.$Signmd5;
$notify_km = @file_get_contents($url);
if($get_sign==$Sign and $notify_km=='2002'){


echo "<script language=\"javascript\">alert('支付成功');window.close();</script>";

} else {

//当做不成功处理
echo "<script language=\"javascript\">alert('验证失败');window.close();  </script>";

}
?>