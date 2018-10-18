<?php
include($_SERVER['DOCUMENT_ROOT'].'/19SKPAY/function.php');
require_once($_SERVER['DOCUMENT_ROOT']."/19SKPAY/19sk.config.php");
require_once("wx_config.php");

$get_order=YJSK_PAPI_GetSafeParam("orderid","",YJSK_XH_PARAM_INT);

$apiurl = "http://www.26sk.cn/payment/apipay/wxpay/wxqueryorder.php?";
$md5 = "orderid=".$get_order."&userid=".$userid.$key;
$sign = md5($md5);
$url=$apiurl."userid=".$userid."&orderid=".$get_order."&sign=".$sign;
$url = @file_get_contents($url);
$url = iconv('utf-8','gb2312',$url);
$allArray=(explode("|", $url));
$orderid=$allArray[0];
$out_trade_no=$orderid;
$out_userid=$allArray[1];
$out_state=$allArray[2];
$out_price=$allArray[3];
if($out_state=='1'){//订单状态：1：成功，-1：失败

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