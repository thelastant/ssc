<?php 
header("Content-type: text/html; charset=utf-8");
//echo "<script>alert('暂未开通');window.close();</script>";exit;
include_once("config.php");
if(wappc()){
	$val1='2';
}else{
	$val1='1';
}
$username = $uidc['username'];

$uapiresult=mysql_query("select * from api_user where type='ibc' and uid='$uidsc'");
$uapi=mysql_fetch_array($uapiresult);

$Hostname = "http://api.kemairui.cn/Ibc";
if($uidsc==''){
	echo "<script>alert('请登录后再试！');window.close();</script>";exit;
}

/*echo $Hostname.'?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=CreateMember&username='.$username."</br>";
echo $Hostname.'?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=Login&username='.$username.'&gametype='.$val1.'&lang=cs'."</br>";

exit;*/
//echo $Hostname.'?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=Login&username='.$username.'&gametype='.$val1.'&lang=cs'."</br>";

if($uapi['id']==''){
$CreateMember=curl_file_get_contents($Hostname.'?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=CreateMember&username='.$username);//exit($CreateMember);
$CreateMember = json_decode($CreateMember,true);
if($CreateMember['result']=="true"){
mysql_query("INSERT INTO `api_user` (`uid`, `type`, `time`) VALUES ('$uidsc','ibc','$begtime')");
}
}

$url = curl_file_get_contents($Hostname.'?agent_name='.$agent_name.'&agent_pass='.$agent_pass.'&private_key='.$private_key.'&function=Login&username='.$username.'&gametype='.$val1.'&lang=cs');//exit($url);
$result = json_decode($url,true);
//exit($result['msg']);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>    
        <title>IBC 沙巴体育</title>
     <script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
	     </head>
    <frameset rows="*" cols="100%">
        <frame noresize="noresize" src="<?=$result['data']['sports_url']?>" scrolling="auto" name="top">
		<noframes>
        </noframes>
    </frameset>
</html>