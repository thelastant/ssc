<?php
include('../../yx_inc/fx_conn.php');
$partner = $amh_user_hmpay_id;   //�Ƹ�ͨ�̻���
$key = $amh_user_hmpay_key;		

$return_url = "http://".$_SERVER[HTTP_HOST]."/payment/tenpay/payReturnUrl.php";
$notify_url = "http://".$_SERVER[HTTP_HOST]."/payment/tenpay/payNotifyUrl.php";
?>