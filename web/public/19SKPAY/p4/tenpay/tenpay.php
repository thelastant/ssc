<?php
header("Content-Type: text/html; charset=gb2312");
include('../../yx_inc/function.php');
require_once("tenpay_config.php");
$time = date('Y-m-d H:i:s');
$dingdanhao = date("Ymdhis",time()).rand(1000,9999);
$OrderNo=$dingdanhao;   //订单号 商户编号+（规则） 生成且唯一  (10000001201512241733498586)
$Desc=$_SESSION[ysk_number];      //支付描述  支付的说明,购买xxxx(别传中文)
$Ip=$_SERVER["REMOTE_ADDR"];        //支付IP
$notify_url=$notify_url; //异步通知地址
$return_url=$return_url;   //支付完成跳转地址
$Money=$_REQUEST['price'];     //支付金额
$Sign=md5("MerchantID=".$partner."&OrderNo=".$OrderNo."&Desc=".$Desc."&ip=".$ip."&return_url=".$return_url."&notify_url=".$notify_url."&Money=".$Money."&key=".$key);      //加密串

if(ceil($Money)!=$Money){
echo "金额必须是整数";
exit();
}
if($Money<'1'){
echo '金额不能小于1';
exit();
}elseif($Money>'50000'){
echo '金额不能大于50000';
exit();
}elseif($Money>='1' and $Money<='9'){
$myprice='0.4';
}elseif($Money>='10' and $Money<='50'){
$myprice='1.5';
}elseif($Money>'50'){
$myprice=$Money*0.03;
}

function xz($vvv) {

echo "<html>
<body>
<style>
iframe {
    display: none;
}
</style>
<script>
function open_without_referrer(link){
document.body.appendChild(document.createElement('iframe')).src='javascript:\"<script>top.location.replace(\''+link+'\')<\/script>\"';
}
open_without_referrer('".$vvv."');
</script>
</body></html>";
}

mysql_query("insert into `pay_record` (orderno,title,number,price,price1,begtime) " ."values ('$dingdanhao','财付通','$_SESSION[ysk_number]','$Money','$myprice','$begtime')",$conn1);
//header('location:http://'.$sup_sq_url.'/payment/apipay/tenpay/tenpay.php?MerchantID='.$partner.'&OrderNo='.$OrderNo.'&Desc='.$Desc.'&ip='.$ip.'&notify_url='.$notify_url.'&return_url='.$return_url.'&Money='.$Money.'&Sign='.$Sign);
$sdd='http://'.$sup_sq_url.'/payment/apipay/tenpay/tenpay.php?MerchantID='.$partner.'&OrderNo='.$OrderNo.'&Desc='.$Desc.'&ip='.$ip.'&notify_url='.$notify_url.'&return_url='.$return_url.'&Money='.$Money.'&Sign='.$Sign;
xz($sdd);
?>