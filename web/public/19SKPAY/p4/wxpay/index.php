<?php
include($_SERVER['DOCUMENT_ROOT'].'/19SKPAY/function.php');
require_once($_SERVER['DOCUMENT_ROOT']."/19SKPAY/19sk.config.php");
require_once("wx_config.php");
$Money=YJSK_PAPI_GetSafeParam("amount","",YJSK_XH_PARAM_TXT);//支付金额
$uid=YJSK_PAPI_GetSafeParam("uid","",YJSK_XH_PARAM_TXT);
$dingdanhao=YJSK_PAPI_GetSafeParam("OrderNo","",YJSK_XH_PARAM_TXT);
$get_ip=YJSK_Local_Ip();
$url='http://www.26sk.cn/payment/apipay/wxpay/example/native.php?';
$md5='orderid='.$dingdanhao.'&userid='.$userid.'&notifyurl='.$returnurl.'&money='.$Money.$key;
$sign=md5($md5);
$url=$url.'userid='.$userid.'&orderid='.$dingdanhao.'&body=给'.$_SERVER['HTTP_HOST'].'的会员'.$uid.'充值&money='.$Money.'&notifyurl='.$returnurl.'&sign='.$sign;
$url = @file_get_contents($url);
$url = iconv('utf-8','gb2312',$url);
//$allArray=(explode("|", $url));
$ewm=$url;
if($ewm!=''){

//连接数据库
$con = mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
mysql_select_db($DB_NAME, $con);
mysql_query("set names 'UTF-8'");
$result=mysql_query("select * from ".$DB_PRENAME."member_recharge where rechargeId='$dingdanhao'");
$sql_log=mysql_fetch_array($result);
mysql_close($con);

if($sql_log['id']!=''){
echo '{"code_img_url":"'.$ewm.'","weixin_order":"'.$dingdanhao.'","weixin_price":"'.$Money.'"}';
}else{
echo '{"status":500,"msg":"Error"}';
}

}else{
echo '{"status":500,"msg":"Error"}';
}
?>