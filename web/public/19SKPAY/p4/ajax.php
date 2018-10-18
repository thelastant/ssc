<?php
include($_SERVER['DOCUMENT_ROOT'].'/19SKPAY/function.php');
require_once($_SERVER['DOCUMENT_ROOT']."/19SKPAY/19sk.config.php");

$Action=YJSK_PAPI_GetSafeParam("Action","",YJSK_XH_PARAM_TXT);
$Cardorder=YJSK_PAPI_GetSafeParam("Cardorder","",YJSK_XH_PARAM_TXT);
$order=YJSK_PAPI_GetSafeParam("order","",YJSK_XH_PARAM_TXT);
$qorder=YJSK_PAPI_GetSafeParam("qorder","",YJSK_XH_PARAM_TXT);

//连接数据库
$con = mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
mysql_select_db($DB_NAME, $con);
mysql_query("set names 'UTF-8'");

//微信充值订单号查询
if($order!=''){
$return=mysql_query("select * from ".$DB_PRENAME."member_recharge where  payment_id='12'  and rechargeId='$order' ");
$row=mysql_fetch_array($return);
if($row['state']!='0'){
echo '{"code":"1","msg":"success"}';
}else{
echo '{"code":"0","msg":"fail"}';
}

}

mysql_close($con);

?>