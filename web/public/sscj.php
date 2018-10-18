<?php
date_default_timezone_set('PRC');        ######设置时间为北京时间
$local_dbhost ="localhost"; //数据库主机名
$local_dbuser = "root";     //数据库用户名
$local_dbpassword ="changliyule0519.-+";      //数据库密码
$local_dbname = "changli"; 

$conn1 = mysql_connect($local_dbhost,$local_dbuser, $local_dbpassword,true);
mysql_select_db($local_dbname, $conn1);
mysql_query("set names GBK"); 
$sx="1440"-1;
$s=60;
$type="69";
for ($x=0; $x<=$sx; $x++){
  $ss=$s*$x;
  $act=$x+1;
  $kssj=1529596860;
  $kssjs=$kssj+$ss;
  $sj=date("G:i:s",$kssjs);
  //mysql_query("INSERT INTO `lottery_data_time` (`type`, `actionNo`, `actionTime`, `stopTime`, `expect`) VALUES ('$type', '$act', '$sj', '$sj', '')",$conn1);
  echo $act.':'.$sj.'</br>';
}