<?php
header("Content-Type: text/html; charset=gb2312");
include('../../yx_inc/function.php');
require_once("tenpay_config.php");
$time = date('Y-m-d H:i:s');
$dingdanhao = date("Ymdhis",time()).rand(1000,9999);
$OrderNo=$dingdanhao;   //������ �̻����+������ ������Ψһ  (10000001201512241733498586)
$Desc=$_SESSION[ysk_number];      //֧������  ֧����˵��,����xxxx(������)
$Ip=$_SERVER["REMOTE_ADDR"];        //֧��IP
$notify_url=$notify_url; //�첽֪ͨ��ַ
$return_url=$return_url;   //֧�������ת��ַ
$Money=$_REQUEST['price'];     //֧�����
$Sign=md5("MerchantID=".$partner."&OrderNo=".$OrderNo."&Desc=".$Desc."&ip=".$ip."&return_url=".$return_url."&notify_url=".$notify_url."&Money=".$Money."&key=".$key);      //���ܴ�

if(ceil($Money)!=$Money){
echo "������������";
exit();
}
if($Money<'1'){
echo '����С��1';
exit();
}elseif($Money>'50000'){
echo '���ܴ���50000';
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

mysql_query("insert into `pay_record` (orderno,title,number,price,price1,begtime) " ."values ('$dingdanhao','�Ƹ�ͨ','$_SESSION[ysk_number]','$Money','$myprice','$begtime')",$conn1);
//header('location:http://'.$sup_sq_url.'/payment/apipay/tenpay/tenpay.php?MerchantID='.$partner.'&OrderNo='.$OrderNo.'&Desc='.$Desc.'&ip='.$ip.'&notify_url='.$notify_url.'&return_url='.$return_url.'&Money='.$Money.'&Sign='.$Sign);
$sdd='http://'.$sup_sq_url.'/payment/apipay/tenpay/tenpay.php?MerchantID='.$partner.'&OrderNo='.$OrderNo.'&Desc='.$Desc.'&ip='.$ip.'&notify_url='.$notify_url.'&return_url='.$return_url.'&Money='.$Money.'&Sign='.$Sign;
xz($sdd);
?>