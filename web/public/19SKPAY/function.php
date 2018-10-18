<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
date_default_timezone_set('PRC');        ######设置时间为北京时间
$mytime=date("Y-m-d G:i:s");
$begtime=strtotime(date("Y-m-d G:i:s"));

$dingdanhao = date("Y-m-dH-i-s");
$dingdanhao = str_replace("-","",$dingdanhao);
$dingdanhao .= rand(1000,2000);

////////////////////////////获取本地IP地址
function YJSK_Local_Ip() {  
    //判断服务器是否允许$_SERVER
    /*if(isset($_SERVER)){    
        if(isset($_SERVER[HTTP_X_FORWARDED_FOR])){
            $realip = $_SERVER[HTTP_X_FORWARDED_FOR];
        }elseif(isset($_SERVER[HTTP_CLIENT_IP])) {
            $realip = $_SERVER[HTTP_CLIENT_IP];
        }else{
            $realip = $_SERVER[REMOTE_ADDR];
        }
    }else{
        //不允许就使用getenv获取  
        if(getenv("HTTP_X_FORWARDED_FOR")){
              $realip = getenv( "HTTP_X_FORWARDED_FOR");
        }elseif(getenv("HTTP_CLIENT_IP")) {
              $realip = getenv("HTTP_CLIENT_IP");
        }else{
              $realip = getenv("REMOTE_ADDR");
        }
    }*/

    return $_SERVER["REMOTE_ADDR"];
}

/***************YJSK_PAPI_GetSafeParam()用来获取安全的参数值防PHP注入*****************/    

function YJSK_guolv($str)
{
$search = array("'",",","(",")");
$replace = array("","","","");
return str_replace($search, $replace, $str);
}

define("YJSK_XH_PARAM_INT",0);
define("YJSK_XH_PARAM_TXT",1);
function YJSK_PAPI_GetSafeParam($pi_strName,$pi_Def ="",$pi_iType = YJSK_XH_PARAM_TXT){
if ( isset($_GET[$pi_strName]) ) 
$t_Val = trim($_GET[$pi_strName]);
else if ( isset($_POST[$pi_strName]))
$t_Val = trim($_POST[$pi_strName]);
else 
return $pi_Def;

$t_Val = YJSK_guolv($t_Val);

// INT
if (YJSK_XH_PARAM_INT == $pi_iType)
{
if (is_numeric($t_Val))
return $t_Val;
else
return $pi_Def;
}

// String
$t_Val = str_replace("&", "&amp;",$t_Val); 
$t_Val = str_replace("<", "&lt;",$t_Val);
$t_Val = str_replace(">", "&gt;",$t_Val);
if ( get_magic_quotes_gpc() )
{
$t_Val = str_replace("\\\"", "&quot;",$t_Val);
$t_Val = str_replace("\\''", "&#039;",$t_Val);
}
else
{
$t_Val = str_replace("\"", "&quot;",$t_Val);
$t_Val = str_replace("'", "&#039;",$t_Val);
}

return $t_Val;
}
/***************结束防止PHP注入*****************/
?>