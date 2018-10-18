<?php
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('PRC');        ######设置时间为北京时间
$mytime=date("Y-m-d G:i:s");
$begtime=strtotime(date("Y-m-d G:i:s"));
$time=$begtime;
$actionNo=1440;
if($actionNo==1440 and date("H")==00 and date("i")==00){
	$actionNo=date('Ymd1440', $time - 24*3600);
}else{
	$actionNo=date('Ymd', $time).substr(10000+$actionNo,1);
}
echo date("i")."</br>";
echo $actionNo;