<?php

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set("display_errors", "On");
} else {
    error_reporting(0);
    ini_set("display_errors", "Off");
}

header('Content-Type: text/html;charset=utf-8');
mb_internal_encoding('UTF-8');
date_default_timezone_set('PRC');
set_error_handler(array('core', 'error_handler'));
session_start();
ob_start(array('core', 'ob_output'));

class core
{

    //初始化
    public static function init()
    {
        $uri = array_key_exists('REQUEST_URI', $_SERVER) ? $_SERVER['REQUEST_URI'] : '/';
        $uri_info = parse_url($uri);
        if (!is_array($uri_info) || !array_key_exists('path', $uri_info)) self::__403();
        $path = explode('/', $uri_info['path'] === '/' ? '/index/web' : $uri_info['path']);
        if (count($path) !== 3) self::__403();
        $mod_name = $path[1];
        $act_name = $path[2];
        if (!preg_match('/^[a-z0-9_\-]+$/', $mod_name) || !preg_match('/^[a-z0-9_\-]+$/', $act_name)) self::__403();
        $mod_file = SYSTEM . '/mod/' . $mod_name . '.mod.php';
        if (!is_file($mod_file)) self::__403();
        require(SYSTEM . '/core/mod.core.php');
        require($mod_file);
        $mod_classname = 'mod_' . $mod_name;
        $model = new $mod_classname;
        $methods = get_class_methods($model);
        if (!in_array($act_name, $methods)) self::__403();
        call_user_func_array(array($model, $act_name), array());
    }

    public static function ob_output($html)
    {
        // 一些用户喜欢使用windows笔记本编辑文件，因此在输出时需要检查是否包含BOM头
        if (ord(substr($html, 0, 1)) === 239 && ord(substr($html, 1, 2)) === 187 && ord(substr($html, 2, 1)) === 191) $html = substr($html, 3);
        // gzip输出
        if (
            !headers_sent() && // 如果页面头部信息还没有输出
            extension_loaded("zlib") && // 而且zlib扩展已经加载到PHP中
            array_key_exists('HTTP_ACCEPT_ENCODING', $_SERVER) &&
            stripos($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") !== false // 而且浏览器说它可以接受GZIP的页面
        ) {
            $html = gzencode($html, 3);
            header('Content-Encoding: gzip');
            header('Vary: Accept-Encoding');
        }
        header('Content-Length: ' . strlen($html));
        return $html;
    }

    // 非法请求简写模式
    public static function __403()
    {
        self::api_err_return('非法请求');
    }

    /**
     * @name 错误输出
     * @param string message 错误信息
     */
    public static function error($message, $url = '')
    {
        if (self::IsAjax()) {
            self::api_err_return($message);
        }
        header('X-Error-Message: ' . rawurlencode($message));
        $msg = $message;
        if (self::IsMobile()) {
            $filePath = SYSTEM . "/tpl/mobile/error.tpl.php";
            if (file_exists($filePath)) {
                require($filePath);
                exit();
            }
        }
        require(SYSTEM . "/tpl/common/error.tpl.php");
        exit;
    }

    public static function redirect($url, $msg = "")
    {

    }

    public static function IsMobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    public static function IsAjax()
    {
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
            return true;
        } else {
            return false;
        }
    }

    //error
    public static function api_err_return($msg = "err", $code = 404, $data = null)
    {
        $json['msg'] = $msg;
        $json['code'] = $code;
        $json['data'] = $data;
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($json));
    }

    public static function createOrderNo()
    {
        $year_code = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        return date('d') . $year_code[intval(date('Y')) - 2010] .
            strtoupper(dechex(date('m'))) . date('d') .
            substr(time(), -5) . substr(microtime(), 2, 5);
    }

    public static function api_return($msg = "success", $code = 200, $data = null)
    {
        $json['msg'] = $msg;
        $json['code'] = $code;
        $json['data'] = $data;
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($json));
    }

    /**
     * @name 类库调用
     * @param string name 类库名称
     * @return object
     */
    public static function lib($name)
    {
        static $libs = array();
        if (!array_key_exists($name, $libs)) {
            require(SYSTEM . '/lib/' . $name . '.lib.php');
            $classname = 'lib_' . $name;
            $libs[$name] = new $classname;
        }
        return $libs[$name];
    }

    /**
     * @name 日志记录
     * @param array data 日志数据
     */
    public static function logger($data)
    {
        $text = '';
        $data['TIME'] = date('Y-m-d H:i:s');
        $data['URI'] = $_SERVER['REQUEST_URI'];
        foreach ($data as $k => $v) $text .= '[' . $k . ']: ' . $v . "\r\n";
        $text .= "\r\n";
        file_put_contents(SYSTEM . '/data/logs/' . date('Y.m.d') . '.log', $text, FILE_APPEND);
    }

    /**
     * @name 错误捕获
     * @param int type 错误类型
     * @param string message 错误信息
     * @param string file 错误文件
     * @param string line 错误行号
     */
    public static function error_handler($type, $message, $file, $line)
    {
        $data = array(
            'MSG' => $message,
            'FILE' => $file,
            'LINE' => $line,
        );
        $err = [
            "FILE" => $file,
            "LINE" => $line,
            "MESSAGE" => $message,
        ];
        if (APP_DEBUG) {
            self::error(json_encode($err));
        } else {
            self::logger($data);
        }
    }
}