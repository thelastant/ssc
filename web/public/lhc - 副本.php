<?php
header("Content-type: text/html; charset=utf-8");

$dbhost="127.0.0.1";
$dbname="changli";
$conf['db']['user']="root";
$conf['db']['password']="changliyule0519.-+";
$conf['db']['prename']="lottery_";
$api = 'http://1680660.com/smallSix/findSmallSixInfo.do?lotCode=10048';
$resource = file_get_contents( $api );//exit($resource);
$data=json_decode($resource,true);
$preDrawCode=$data['result']['data']['preDrawCode'];
$hmArray=(explode(',',$preDrawCode));
function bcs($v1,$v2="2"){
	return str_pad($v1,$v2,"0",STR_PAD_LEFT);
}
$preDrawCode=bcs($hmArray[0]).",".bcs($hmArray[1]).",".bcs($hmArray[2]).",".bcs($hmArray[3]).",".bcs($hmArray[4]).",".bcs($hmArray[5]).",".bcs($hmArray[6]);
if(bcs($hmArray[0])=="00" or bcs($hmArray[1])=="00" or bcs($hmArray[2])=="00" or bcs($hmArray[3])=="00" or bcs($hmArray[4])=="00" or bcs($hmArray[5])=="00" or bcs($hmArray[6])=="00"){
	exit;
}
if($data['result']['data']['drawIssue']!=''){
//连接数据库
$con = mysql_connect($dbhost,$conf['db']['user'],$conf['db']['password'])or die('Could not connect');
mysql_select_db($dbname, $con);

$result=mysql_query("select * from ".$conf['db']['prename']."data where type='122' and number='".$data['result']['data']['preDrawIssue']."'  ");
$rows=mysql_fetch_array($result);
if($rows['id']!='' and substr_count($rows['data'],',')!="6"){
mysql_query("UPDATE `".$conf['db']['prename']."data` set `data`='".$preDrawCode."' WHERE type='122'");
}

$result=mysql_query("select * from ".$conf['db']['prename']."data_lhc_time where type='122' and action_date='".$data['result']['data']['drawTime']."'");
$row=mysql_fetch_array($result);


if($row['id']==''){
$sql_log=mysql_query("insert into `".$conf['db']['prename']."data_lhc_time` set 
`type`='122',
`actionNo`='".$data['result']['data']['drawIssue']."',
`action_date`='".$data['result']['data']['drawTime']."'
");
}

mysql_close($con);
}

if(substr_count($preDrawCode,',')=="6"){
header('Content-Type: text/xml;charset=utf8');

echo '<xml>
<row expect="'.$data['result']['data']['preDrawIssue'].'" opencode="'.$preDrawCode.'" opentime="'.$data['result']['data']['serverTime'].'"/>
</xml>';
}
