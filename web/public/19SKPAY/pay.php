<?php
include('function.php');
require_once("19sk.config.php");
$get_order=$dingdanhao;
$get_money=YJSK_PAPI_GetSafeParam("amount","",YJSK_XH_PARAM_TXT);
$get_type=YJSK_PAPI_GetSafeParam("type","",YJSK_XH_PARAM_INT);
if($get_type=='1'){
$bankId='2';
}elseif($get_type=='2'){
$bankId='3';
}elseif($get_type=='3'){
$bankId='20';
}
$amount=$get_money;
$uid=YJSK_PAPI_GetSafeParam("uid","",YJSK_XH_PARAM_TXT);
$get_ip=YJSK_Local_Ip();

//连接数据库
$con = mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
mysql_select_db($DB_NAME, $con);

$result=mysql_query("select * from ".$DB_PRENAME."members where uid='$uid'");
$urow=mysql_fetch_array($result);

$result=mysql_query("select * from ".$DB_PRENAME."bank_list where id='$bankId'");
$bank=mysql_fetch_array($result);

$result=mysql_query("select * from ".$DB_PRENAME."params where name='rechargeMin1'");
$par=mysql_fetch_array($result);
$rechargeMin1=$par['value'];
$result=mysql_query("select * from ".$DB_PRENAME."params where name='rechargeMax1'");
$par=mysql_fetch_array($result);
$rechargeMax1=$par['value'];

/*if($amount<$rechargeMin1 or $amount>$rechargeMax1){
echo "<script language=\"javascript\">alert('最低充值:".$rechargeMin1."元,最高充值:".$rechargeMax1."元');window.close();</script>";
exit();
}*/

$txt='appid='.$APPID.'&order='.$get_order.'&money='.$get_money.'&type='.$get_type.'&key='.$APPKEY;
$md5=md5($txt);
$url=$APPURL.'/pay/pay.php?appid='.$APPID.'&order='.$get_order.'&money='.$get_money.'&type='.$get_type.'&ip='.$get_ip.'&sign='.$md5;

$sql_log=mysql_query("insert into `".$DB_PRENAME."member_recharge` set 
`rechargeId`='$get_order',
`actionTime`='$begtime',
`uid`='$uid',
`username`='$urow[username]',
`actionIP`='$get_ip',
`info`='用户充值',
`mBankId`='$bankId',
`amount`='$amount'
");

mysql_close($con);

if($sql_log){
header('location:'.$url);
}else{
echo "<script language=\"javascript\">alert('未知错误');window.close();</script>";
exit();
}
?>