<?php
function curl_file_get_contents($durl){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $durl);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$r = curl_exec($ch);
	curl_close($ch);
	return $r;
}

$url = curl_file_get_contents("http://pkvip55.com/Shared/GetLotteryResult?gamelist=TXFFC:0");//exit($url);
$result = json_decode($url,true);
if($result['status']){
$dnr='<row expect="'.$result['Data'][0]['PreviousPeriod'].'" opencode="'.$result['Data'][0]['PreviousResult'].'" opentime="'.$result['Data'][0]['CloseTime'].'"/>
';
header("Content-Type: text/xml; charset=utf-8");
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">\n";
echo '<xml>';
echo $dnr;
echo '</xml>';
}