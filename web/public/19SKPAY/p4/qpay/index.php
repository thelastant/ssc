<?php
include_once('../../yx_inc/function.php');
include_once('../../yx_inc/user_check.php');
require_once("q_config.php");
$price=$_REQUEST[total_fee];
if($price<='10'){
$myprice='0.3';
}elseif($price>='11'){
$myprice=$price*0.03;
}
$prices=$price+$myprice;
$url='http://'.$sup_sq_url.'/payment/apipay/qpay/index.php?';
$md5='orderid='.$dingdanhao.'&userid='.$userid.'&notifyurl='.$returnurl.'&money='.$price.$key;
$sign=md5($md5);
$url=$url.'userid='.$userid.'&orderid='.$dingdanhao.'&body=���γ�ֵ�����Ǹ�'.$_SERVER[HTTP_HOST].'��վ�Ļ�Ա'.$_SESSION[ysk_number].'���г�ֵ&money='.$price.'&notifyurl='.$returnurl.'&sign='.$sign;
$url = @file_get_contents($url);
/*$url = iconv('utf-8','gb2312',$url);
$allArray=(explode("|", $url));*/
$ewm=$url;
$sql=mysql_query("insert into `pay_record` (orderno,title,number,price,price1,begtime) " ."values ('$dingdanhao','��Q֧��','$_SESSION[ysk_number]','$price','$myprice','$begtime')",$conn1);
if($sql){
echo '{"code_img_url":"'.$ewm.'","weixin_order":"'.$dingdanhao.'","weixin_price":"'.$prices.'"}';
}else{
echo '{"status":500,"msg":"Error"}';
}
?>