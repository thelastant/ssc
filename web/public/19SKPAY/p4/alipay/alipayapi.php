<?php
include($_SERVER['DOCUMENT_ROOT'].'/19SKPAY/function.php');
require_once($_SERVER['DOCUMENT_ROOT']."/19SKPAY/19sk.config.php");
require_once("alipay_config.php");
$uid=YJSK_PAPI_GetSafeParam("uid","",YJSK_XH_PARAM_TXT);
$get_ip=YJSK_Local_Ip();
$OrderNo=YJSK_PAPI_GetSafeParam("OrderNo","",YJSK_XH_PARAM_TXT);
$Desc=$uid;      //֧������  ֧����˵��,����xxxx(������)
$ip=$get_ip;        //֧��IP
$notify_url=$notify_url; //�첽֪ͨ��ַ
$return_url=$return_url;   //֧�������ת��ַ
$Money=YJSK_PAPI_GetSafeParam("amount","",YJSK_XH_PARAM_TXT);//֧�����
$Sign=md5("MerchantID=".$partner."&OrderNo=".$OrderNo."&Desc=".$Desc."&ip=".$ip."&return_url=".$return_url."&notify_url=".$notify_url."&Money=".$Money."&key=".$key);      //���ܴ�

//�������ݿ�
$con = mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
mysql_select_db($DB_NAME, $con);
mysql_query("set names 'UTF-8'");
$result=mysql_query("select * from ".$DB_PRENAME."member_recharge where rechargeId='$OrderNo'");
$sql_log=mysql_fetch_array($result);
mysql_close($con);

if($sql_log['id']!=''){
header('location:http://www.26sk.cn/payment/apipay/alipay/alipayapi.php?MerchantID='.$partner.'&OrderNo='.$OrderNo.'&Desc='.$Desc.'&ip='.$ip.'&notify_url='.$notify_url.'&return_url='.$return_url.'&Money='.$Money.'&Sign='.$Sign);
}else{
echo "<script language=\"javascript\">alert('δ֪����');window.close();</script>";
exit();
}

?>