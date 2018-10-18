<?php
/**
 * 本程序用于学习，研究彩票用途请勿商用！！！！！！！
 *
 */
function mysuiji_pass( $mysuiji_length = 8 ) {
    //$mysuiji_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_.';
	$mysuiji_length=4;
	$mysuiji_chars = '0123456789';
    $mysuiji_password = '';
    for ( $i = 0; $i < $mysuiji_length; $i++ ) 
    {
        $mysuiji_password .= $mysuiji_chars[ mt_rand(0, strlen($mysuiji_chars) - 1) ];
    }
    return $mysuiji_password;
}
//定义验证email的正则表达式
function emails($email){
$check="/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";    
$bool=preg_match($check,$email,$counts);
return $bool;
}
// 邮箱发送说明：（发送给谁 ，发送标题，字符编码）
function email_fs($var,$var1,$var2,$var3="") 
{
////////邮箱程序
$var3=strtolower($var3);
if($var3==''){
$var3='UTF-8';
}elseif($var3=='utf-8'){
$var3='utf-8';
}elseif($var3=='gbk'){
$var3='gbk';
}else{
$var3='gb2312';
}
$FromName=iconv("UTF-8",$var3,"昌利娱乐");
ini_set("magic_quotes_runtime",0);
$class_mail=dirname(__FILE__).'/../phpmailer/class.phpmailer.php';
require $class_mail;
try {
	$mail = new PHPMailer(true); 
	$mail->IsSMTP();
	$mail->CharSet=$var3; //设置邮件的字符编码，这很重要，不然中文乱码
	$mail->SMTPAuth   = true;                  //开启认证
	$mail->Port       = 25;                    
	$mail->Host       = 'smtp.163.com'; 
	$mail->Username   = 'w7y1t3@163.com';    
	$mail->Password   = 'zaijia';            
	//$mail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现"Could  not execute: /var/qmail/bin/sendmail "的错误提示
	$mail->AddReplyTo("123456@qq.com","gjjskf.com");//回复地址
	$mail->From       = 'w7y1t3@163.com';
	$mail->FromName   = $FromName;
	$to = $var;
	$mail->AddAddress($to);
	$mail->Subject  = $var1;
	$mail->Body = $var2;
	$mail->AltBody    = $FromName; //当邮件不支持html时备用显示，可以省略
	$mail->WordWrap   = 80; // 设置每行字符串的长度
	//$mail->AddAttachment("f:/test.png");  //可以添加附件
	$mail->IsHTML(true); 
	$mail->Send();
	return true;
} catch (phpmailerException $e) {
	return false;
}
}

if($_SERVER['REMOTE_ADDR']=='218.20.5.20'){
//email_fs('1666160975@qq.com','如果您有其他退网易邮件中','邮件ds0中');
}

function mywappc(){    
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
if(mywappc()){
	define('wappc', "0");
}else{
	define('wappc', "1");
}
if(wappc=="0" and ($_SERVER['SERVER_NAME']=="www.gjjskf.com" or $_SERVER['SERVER_NAME']=="gjjskf.com")){
	if($_GET['pid']!=''){
		header('location:http://m.gjjskf.com/user/reg?pid='.$_GET['pid']);
	}else{
		header('location:http://m.gjjskf.com');
	}
	exit();
}

define('ROOT', dirname(__FILE__));
define('SYSTEM', ROOT . '/../system');
define("VENDOR_PATH", ROOT . '/../vendor/');

define("STATIC_PATH", '/static/');

//检测访问类型，进行动态调整
define("PAY_DRIVER_PATH", ROOT . '/../data/pay/');
define("TEMPLATE_PATH", SYSTEM . "/tpl/web/");
define("RunTime_PATH", SYSTEM . "/data/");

require(ROOT . '/../data/config.php');
require(SYSTEM . '/core/core.core.php');
require(VENDOR_PATH . "autoload.php");
core::init();