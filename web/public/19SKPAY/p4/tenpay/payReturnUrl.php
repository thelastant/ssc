<?php
if($_GET[charset]=='gbk'){
    header("Content-Type: text/html; charset=gb2312");
}
include('../../yx_inc/function.php');
require_once("tenpay_config.php");
if(mywappc()){
$mywappc='0';
}
$out_trade_no=$_GET[OrderNo];
$OrderNo=$out_trade_no;
$Sign=md5($OrderNo."&Money=".$_GET['Money']."&trade_status=".$_GET['trade_status']."&key=".$key);
$Signmd5=md5("MerchantID=".$partner."&OrderNo=".$OrderNo."&key=".$key);
$url = 'http://'.$sup_sq_url.'/payment/apipay/tenpay/PayOnlieQuery.php?MerchantID='.$partner.'&OrderNo='.$OrderNo.'&Sign='.$Signmd5;
$notify_km = @file_get_contents($url);
if($_GET[sign]==$Sign and $notify_km=='2002'){
$total=mysql_num_rows(mysql_query("SELECT * FROM `details_funds` where  orderid='$out_trade_no' ",$conn1));
if ($total=='0'){
$return=mysql_query("select * from pay_record where  title='�Ƹ�ͨ'  and orderno='$out_trade_no' and online='0' ",$conn1);
$row=mysql_fetch_array($return);
if ($row['id']!=''){
$ureturn=mysql_query("select * from members where   number='$row[number]'",$conn1);
$yrow=mysql_fetch_array($ureturn);
$sup_ureturn=mysql_query("select * from sup_members where   number='$sup_number'",$conn2);
$sup_user=mysql_fetch_array($sup_ureturn);
##############�ж�Ѻ���Ƿ������ͱ�׼
if ($yrow['frozen_kuan']<$yrow['di_kuan']){
$kuan=$yrow['frozen_kuan']+$row['price'];
$title='(�Ƹ�ͨ��ֵ�����ڶ���Ѻ�������ͱ�׼����������Զ�ת�붳��Ѻ��)';

mysql_query("insert into `details_funds` (title,orderid,incomes,befores,afters,number,begtime) " .
"values ('$title','$out_trade_no','$row[price]','$yrow[frozen_kuan]','$kuan','$row[number]','$begtime')",$conn1);
//---------------------SUP�ӿ� ��ʼ
$sup_kuan=$sup_user['kuan']+$row['price'];
$sup_total1=mysql_num_rows(mysql_query("SELECT * FROM `sup_details_funds` where  orderid='$out_trade_no' and number='$sup_number'",$conn2));
if ($sup_total1=='0'){
mysql_query("insert into `sup_details_funds` (title,orderid,incomes,befores,afters,number,begtime)"."values ('$title','$out_trade_no','$row[price]','$sup_user[kuan]','$sup_kuan','$sup_number','$begtime')",$conn2);
mysql_query("update `sup_members`    set kuan='$sup_kuan' where number='$sup_number'",$conn2); 
}
//---------------------SUP�ӿ� ����
mysql_query("update `members`    set frozen_kuan='$kuan' where number='$row[number]'",$conn1); 
mysql_query("update `pay_record` set online='1' where id='$row[id]'",$conn1); 
}else{
$kuan=$yrow['kuan']+$row['price'];
$title='(�Ƹ�ͨ��ֵ)';
//---------------------SUP�ӿ� ��ʼ
$sup_kuan=$sup_user['kuan']+$row['price'];
$sup_total2=mysql_num_rows(mysql_query("SELECT * FROM `sup_details_funds` where  orderid='$out_trade_no' and number='$sup_number'",$conn2));
if ($sup_total2=='0'){
mysql_query("insert into `sup_details_funds` (title,orderid,incomes,befores,afters,number,begtime)"."values ('$title','$out_trade_no','$row[price]','$sup_user[kuan]','$sup_kuan','$sup_number','$begtime')",$conn2);
mysql_query("update `sup_members`    set kuan='$sup_kuan' where number='$sup_number'",$conn2); 
}
//---------------------SUP�ӿ� ����

mysql_query("insert into `details_funds` (title,orderid,incomes,befores,afters,number,begtime)"."values ('$title','$out_trade_no','$row[price]','$yrow[kuan]','$kuan','$row[number]','$begtime')",$conn1);

$godo=mysql_query("update `members`    set kuan='$kuan' where number='$row[number]'",$conn1); 
$godos=mysql_query("update `pay_record` set online='1' where id='$row[id]'",$conn1); 
}
}
}

if($godo){

if($mywappc=='0'){
echo "<script language=\"javascript\">alert('֧���ɹ���');window.location.href='/wap/Username/index.php';</script>"; 
}else{
echo "<script language=\"javascript\">alert('֧���ɹ���');window.close();  </script>";
}

}else{

if($mywappc=='0'){
echo "<script language=\"javascript\">alert('֧���ɹ���');window.location.href='/wap/Username/index.php';</script>"; 
}else{
echo "<script language=\"javascript\">alert('֧���ɹ���');window.close();  </script>";
}

}

} else {
//�������ɹ�����

if($mywappc=='0'){
echo "<script language=\"javascript\">alert('��֤ʧ�ܣ�');window.location.href='/wap/Username/index.php';</script>"; 
}else{
echo "<script language=\"javascript\">alert('��֤ʧ�ܣ�');window.close();  </script>";
}

}
?>