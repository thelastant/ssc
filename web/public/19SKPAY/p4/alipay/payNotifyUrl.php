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


//连接数据库
$con = mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
mysql_select_db($DB_NAME, $con);
mysql_query("set names 'UTF-8'");

$result = mysql_query("SELECT * FROM ".$DB_PRENAME."member_recharge where rechargeId='$get_order'");
$paylog = mysql_fetch_array($result);

$result = mysql_query("SELECT * FROM ".$DB_PRENAME."members where uid='$paylog[uid]'");
$urow = mysql_fetch_array($result);


if($paylog['state']=='0'){

mysql_query("UPDATE ".$DB_PRENAME."member_recharge SET state='9',amount='$paylog[amount]',rechargeTime='$begtime',coin='$urow[coin]',fcoin='$urow[fcoin]' WHERE rechargeId='$get_order' AND state=0");

mysql_query("UPDATE ".$DB_PRENAME."members SET coin=coin+$paylog[amount] WHERE uid='$paylog[uid]'");

}

mysql_close($con);

echo "success";

} else {

//当做不成功处理
echo "fail";

}
?>