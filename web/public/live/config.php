<?php 
session_start();
date_default_timezone_set('PRC');        ######设置时间为北京时间
$mytime=date("Y-m-d G:i:s");
$begtime=strtotime(date("Y-m-d G:i:s"));
$agent_name = "z41";
$agent_pass = "55e5467c34";
$private_key = "e01e82c29fc0945512ff2f35d3e6aa30";

$prename="lottery_";
$config = mysql_connect("localhost","root","changliyule0519.-+")or die("Mysql Connect Error");
mysql_select_db("changli");
mysql_query("SET NAMES UTF8");


$uidresult=mysql_query("select * from {$prename}members where username='{$_SESSION['username']}'");
$uidc=mysql_fetch_array($uidresult);
$uidsc = $uidc['uid'];

$Host = "http://api.kemairui.cn/";

function curl_file_get_contents($durl){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $durl);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$r = curl_exec($ch);
	curl_close($ch);
	return $r;
}

function wappc(){
    $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';    
    $useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';      
    function CheckSubstrs($substrs,$text){    
        foreach($substrs as $substr)    
            if(false!==strpos($text,$substr)){    
                return true;    
            }    
            return false;    
    }  
    $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');  
    $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');    
                
    $found_mobile=CheckSubstrs($mobile_os_list,$useragent_commentsblock) ||    
              CheckSubstrs($mobile_token_list,$useragent);    
                
    if ($found_mobile){    
        return true;    
    }else{    
        return false;    
    }    
}
?>