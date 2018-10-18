<?php
include('function.php');
require_once("19sk.config.php");
$get_order=YJSK_PAPI_GetSafeParam("order","",YJSK_XH_PARAM_INT);
$get_online=YJSK_PAPI_GetSafeParam("online","",YJSK_XH_PARAM_TXT);
$get_money=YJSK_PAPI_GetSafeParam("money","",YJSK_XH_PARAM_TXT);
$get_type=YJSK_PAPI_GetSafeParam("type","",YJSK_XH_PARAM_TXT);
$get_sign=YJSK_PAPI_GetSafeParam("sign","",YJSK_XH_PARAM_TXT);

$cx_txt_sign="appid=".$APPID."&order=".$get_order."&sign=".$APPKEY;
$cx_md5_sign=md5($cx_txt_sign);
$cx_url=$APPURL."/pay/queryorder.php?appid=".$APPID."&order=".$get_order."&sign=".$cx_md5_sign;
$notify_cx = @file_get_contents($cx_url);
$cx = json_decode($notify_cx,true);

$sign=md5("order=".$get_order."&online=".$get_online."&type=".$get_type."&money=".$get_money."&sign=".$APPKEY);
if($get_sign==$sign){

if($cx['online']=='1'){
	//支付成功
	echo "<script language=\"javascript\">alert('支付成功');window.close();</script>";
}else{
	//支付失败 或 尚未支付
	echo "<script language=\"javascript\">alert('支付失败');window.close();</script>";
}

}else{
	//签名验证失败
	echo "<script language=\"javascript\">alert('验证失败');window.close();  </script>";
}

?>