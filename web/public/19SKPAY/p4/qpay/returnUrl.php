<?php
header("Content-Type: text/html; charset=gb2312");
include_once('../../yx_inc/function.php');
require_once("q_config.php");
$apiurl = "http://".$sup_sq_url."/payment/apipay/qpay/qqueryorder.php?";
$md5 = "orderid=".$_GET[orderid]."&userid=".$userid.$key;
$sign = md5($md5);
$url=$apiurl."userid=".$userid."&orderid=".$_GET[orderid]."&sign=".$sign;
$url = @file_get_contents($url);

$allArray=(explode("|", $url));
$orderid=$allArray[0];
$out_trade_no=$orderid;
$out_userid=$allArray[1];
$out_state=$allArray[2];
$out_price=$allArray[3];
if($out_state=='1'){//����״̬��1��ʾ�ɹ�������δ����
$total=mysql_num_rows(mysql_query("SELECT * FROM `details_funds` where  orderid='$out_trade_no' ",$conn1));
if ($total=='0'){
$return=mysql_query("select * from pay_record where  title='��Q֧��'  and orderno='$out_trade_no' and online='0' ",$conn1);
$row=mysql_fetch_array($return);
if ($row['id']!=''){
$ureturn=mysql_query("select * from members where   number='$row[number]'",$conn1);
$yrow=mysql_fetch_array($ureturn);
$sup_ureturn=mysql_query("select * from sup_members where   number='$sup_number'",$conn2);
$sup_user=mysql_fetch_array($sup_ureturn);
##############�ж�Ѻ���Ƿ������ͱ�׼
if ($yrow['frozen_kuan']<$yrow['di_kuan']){
$kuan=$yrow['frozen_kuan']+$row['price'];
$title='(��Q֧�������ڶ���Ѻ�������ͱ�׼����������Զ�ת�붳��Ѻ��)';

mysql_query("insert into `details_funds` (title,orderid,incomes,befores,afters,number,begtime) " .
"values ('$title','$out_trade_no','$row[price]','$yrow[frozen_kuan]','$kuan','$row[number]','$begtime')",$conn1);
//---------------------SUP�ӿ� ��ʼ
$sup_kuan=$sup_user['kuan']+$row['price'];
$total=mysql_num_rows(mysql_query("SELECT * FROM `sup_details_funds` where  orderid='$out_trade_no' and number='$sup_number'",$conn2));
if ($total=='0'){
mysql_query("insert into `sup_details_funds` (title,orderid,incomes,befores,afters,number,begtime)"."values ('$title','$out_trade_no','$row[price]','$sup_user[kuan]','$sup_kuan','$sup_number','$begtime')",$conn2);
mysql_query("update `sup_members`    set kuan='$sup_kuan' where number='$sup_number'",$conn2); 
}
//---------------------SUP�ӿ� ����
mysql_query("update `members`    set frozen_kuan='$kuan' where number='$row[number]'",$conn1); 
mysql_query("update `pay_record` set online='1',begtimes='$begtime',content='֧���ɹ�' where id='$row[id]'",$conn1); 
}else{
$kuan=$yrow['kuan']+$row['price'];
$title='(��Q֧��)';
//---------------------SUP�ӿ� ��ʼ
$sup_kuan=$sup_user['kuan']+$row['price'];
$total=mysql_num_rows(mysql_query("SELECT * FROM `sup_details_funds` where  orderid='$out_trade_no' and number='$sup_number'",$conn2));
if ($total=='0'){
mysql_query("insert into `sup_details_funds` (title,orderid,incomes,befores,afters,number,begtime)"."values ('$title','$out_trade_no','$row[price]','$sup_user[kuan]','$sup_kuan','$sup_number','$begtime')",$conn2);
mysql_query("update `sup_members`    set kuan='$sup_kuan' where number='$sup_number'",$conn2); 
}
//---------------------SUP�ӿ� ����

mysql_query("insert into `details_funds` (title,orderid,incomes,befores,afters,number,begtime)"."values ('$title','$out_trade_no','$row[price]','$yrow[kuan]','$kuan','$row[number]','$begtime')",$conn1);

$godo=mysql_query("update `members`    set kuan='$kuan' where number='$row[number]'",$conn1); 
$godos=mysql_query("update `pay_record` set online='1',begtimes='$begtime',content='֧���ɹ�' where id='$row[id]'",$conn1); 
}
}
}

if($godo){
echo 'success';
}else{
echo 'success';
}
}else{
echo 'fail';
mysql_query("update `pay_record` set begtimes='$begtime',content='֧��ʧ��' where orderno='$orderid'",$conn1); //��¼����ʧ��ʱ��
}
?>