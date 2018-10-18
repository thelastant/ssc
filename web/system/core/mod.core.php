<?php

use Medoo\Medoo;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class mod
{

    protected $db_prefix; // 数据库表前缀
    protected $version; // 版本号
    protected $db; // 数据库实例
    protected $post; // 是否post请求
    protected $IsAjax = false;//是否是ajax
    protected $ispage; // 是否页面框架内部请求
    protected $time; // 当前时间
    protected $request_time_from; // 传入的起始时间
    protected $request_time_to; // 传入的结束时间
    protected $config = array(); // 网站配置
    protected $clients = array('software', 'web', 'mobile'); //客户端类型列表
    protected $client_type = 'web'; //客户端类型
    protected $user_check = true; //登录检查
    protected $user_session = 'USER'; // 用户session名称
    protected $user; // 用户信息
    protected $pagesize = 30; // 每页的数据条数
    protected $modes = array('2.000' => '元', '0.200' => '角', '0.020' => '分', '0.002' => '厘');
    protected $coin_type_data = array(
        '账户类' => array(
            55 => '注册奖励',
            1 => '用户充值',
            9 => '系统充值',
            54 => '充值奖励',
            106 => '提现冻结',
            12 => '上级转款',
            8 => '提现失败返还',
            107 => '提现成功扣除',
            51 => '绑定银行奖励',
            167 => '日结工资',
            201 => '契约分红收入',
            202 => '契约分红支出',
        ),
        '游戏类' => array(
            101 => '投注扣款',
            108 => '开奖扣除',
            5 => '追号撤单',
            6 => '中奖奖金',
            7 => '撤单返款',
            102 => '追号投注',
            //11 => '合买收单',
            255 => '未开奖返还',
            150 => '统一撤单'
        ),
        /*
        '抢庄类' => array(
            100 => '抢庄冻结',
            10  => '撤庄返款',
            103 => '抢庄返点',
            104 => '抢庄抽水',
            105 => '抢庄赔付',
        ),
        */
        '代理类' => array(
            2 => '下级返点',
            3 => '代理分红',
            52 => '充值佣金',
            53 => '消费佣金',
            56 => '亏损佣金',
            13 => '转款给下级',
        ),
        '活动类' => array(
            50 => '签到赠送',
            120 => '幸运大转盘',
            121 => '积分兑换',
        ),
    );
    protected $coin_types = array();
    protected $dzpsettings = array();
    protected $exchange_config = array();
    protected $request;
    protected $smarty;
    //新版数据库控制器
    protected $DBC;
    static $instance;
    protected $logger;


    //error
    public function api_err_return($msg = "err", $code = 404, $data = null)
    {
        $json['msg'] = $msg;
        $json['code'] = $code;
        $json['data'] = $data;
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($json));
    }

    public function api_return($msg = "success", $code = 200, $data = null)
    {
        $json['msg'] = $msg;
        $json['code'] = $code;
        $json['data'] = $data;
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($json));
    }

    public function __construct()
    {
        $this->db_prefix = DB_PREFIX;
        if (APP_DEBUG) {
            $this->version = rand(1, 99999999);
        } else {
            $this->version = '';
        }
        $this->db = core::lib('db');
        //使用新版数据库控制器

        // Initialize
        $this->initDBC();

        $this->request = core::lib('request');
        $this->IsAjax = $this->request->isAjax();
        $this->post = strtolower($_SERVER['REQUEST_METHOD']) === 'post' ? true : false;
        $this->ispage = (array_key_exists('ispage', $_POST) && $_POST['ispage'] === 'true') ? true : false;
        $this->time = time();
        $config_data = $this->db->query("SELECT * FROM `{$this->db_prefix}params`", 3);
        foreach ($config_data as $v) $this->config[$v['name']] = $v['value'];
        if (!$this->config['switchWeb']) core::error($this->config['webCloseServiceResult']);
        $this->config['kefuGG'] = urldecode($this->config['kefuGG']);
        $this->_checkTheme();
        $this->_initSmarty();
        if ($this->user_check) {
            $this->user_check_func();
        }

        $this->smarty->assign("_config", $this->config);
        $this->smarty->assign("_user", $this->user);

        foreach ($this->coin_type_data as $vs) {
            foreach ($vs as $k => $v) $this->coin_types[$k] = $v;
        }
        $this->logger = new Logger('runtime_logger');
        $this->logger->pushHandler(new StreamHandler(RunTime_PATH . 'logs/runtime.log'));
    }

    protected function initDBC()
    {
        $this->DBC = new Medoo([
            'database_type' => 'mysql',
            'database_name' => DB_NAME,
            'server' => DB_HOST,
            'username' => DB_USER,
            'password' => DB_PASS,
            'charset' => 'utf8',
            'port' => DB_PORT,
            'prefix' => DB_PREFIX,
        ]);


    }


    private function _initSmarty()
    {
        //状态smarty
        $this->smarty = new Smarty();
        //$this->smarty->setDebugging(APP_DEBUG);
        $this->smarty->setTemplateDir(TEMPLATE_PATH);
        $this->smarty->setCompileDir(RunTime_PATH . "_runtime/");
        $this->smarty->setCacheDir(RunTime_PATH . "_cache/");
    }

    //1：直接下线
    protected function is_child($uid, $pid, $username = null, $check_type = 0)
    {
        $self = $pid;
        if ($self === 0) {
            $self = $this->user['uid'];
        }
        if ($uid && $pid && $check_type == 1) {
            return $this->db->find("{$this->db_prefix}members", array('uid' => $uid, "parentId" => $pid));
        }
        if ($uid) {
            return $this->db->query("SELECT `uid` FROM `{$this->db_prefix}members` WHERE `uid`='{$uid}' AND CONCAT(',',parents,',') LIKE '%,{$self},%' LIMIT 1", 2) ? true : false;
        } else if ($username) {
            return $this->db->query("SELECT `uid` FROM `{$this->db_prefix}members` WHERE `username`='{$username}' AND CONCAT(',',parents,',') LIKE '%,{$self},%' LIMIT 1", 2) ? true : false;
        } else {
            return false;
        }
    }

    //获取团队余额
    protected function get_team_coin($uid)
    {
        $children = $this->_getChildren("uid", 0, $uid);
        $ids = array($uid);
        foreach ($children as $c) {
            $ids[] = $c['uid'];
        }
        $total = $this->DBC->sum("members", "coin", [
            'uid' => $ids
        ]);
        return $total;
    }


    //获取字
    protected function _getChildren($fields = "*", $type = 0, $uid = 0)
    {
        if ($uid <= 0) {
            $uid = $this->user["uid"];
        }
        $sql = "SELECT ~field~ FROM `{$this->db_prefix}members` WHERE ";
        switch ($type) {
            case 0: // 所有人
                $sql .= "CONCAT(',',parents,',') LIKE '%,{$uid},%'";
                break;
            case 1: // 直属下级
                $sql .= "parentId={$uid}";
                break;
            case 2: // 所有下级
                $sql .= "CONCAT(',',parents,',') LIKE '%,{$uid},%' AND `uid`!={$uid}";
                break;
        }
        $sql .= ' ~order~';
        $sql_data = str_replace('~field~', $fields, $sql);
        $sql_data = str_replace('~order~', 'ORDER BY `uid` DESC', $sql_data);
        $data = $this->db->query($sql_data, 3);
        return $data;
    }


    private function _checkTheme()
    {
        //检查域名
        $com = $_SERVER['HTTP_HOST'];
        $com = explode(".", $com);
        $this->client_type = "web";
        if ($com['0'] === 'www') {
            $this->client_type = "web";
        } elseif ($com['0'] === 'm' || $_SERVER['HTTP_HOST'] === "10.1.56.116") {
            $this->client_type = "mobile";
        }
        define("THEME_PATH", STATIC_PATH . "theme/{$this->client_type}/");
    }

    // 用户登录检查
    protected function user_check_func()
    {
        $opt = array();
        $url_login = '/user/login';
        if (array_key_exists($this->user_session, $_SESSION) && $_SESSION[$this->user_session]) {
            $this->user = unserialize($_SESSION[$this->user_session]);
            $user_key = session_id();
            $user_sql = "SELECT `isOnLine`, `state` FROM `{$this->db_prefix}member_session` WHERE `uid`={$this->user['uid']} AND `session_key`='$user_key' ORDER BY `id` DESC LIMIT 1";
            $user_info = $this->db->query($user_sql, 2);
            if (!$user_info['isOnLine'] && $user_info['state'] == 1) {
                core::error('您的账号在别处登陆，您被强迫下线', "/user/login");
            } else if (!$user_info['isOnLine']) {
                core::error('由于登陆超时或网络不稳定，您的登录已失效', "/user/login");
            } else if (!array_key_exists('access_update', $_SESSION) || $_SESSION['access_update'] < $this->time - 15) {
                $id = $this->user['sessionId'];
                $update_sql = "UPDATE `{$this->db_prefix}member_session` SET `accessTime`={$this->time} WHERE `id`='$id' LIMIT 1";
                $this->db->query($update_sql, 0);
                $_SESSION['access_update'] = $this->time;
            }
            //重新获取用户信息
            $this->fresh_user_session();
            $this->smarty->assign('_user', $this->user);
        } else {
            header('Location: ' . $url_login);
            exit;
        }
        if (!empty($opt)) {
            unset($_SESSION[$this->user_session]);
            if ($this->post) {
                $this->dialogue($opt);
            } else {
                header('Location: ' . $url_login);
                exit;
            }
        }
    }

    protected function get_ey_types($parse = false)
    {
        static $ey_types = array();
        if (!$ey_types) {
            $ey_types = $this->db->query("SELECT `id`,`title` FROM `{$this->db_prefix}type` WHERE enable=1 AND `isDelete`=0", 3);
        }
        if ($parse === true) {
            $tmp = [];
            foreach ($ey_types as $type) {
                $tmp[$type['id']] = $type;
            }
            $ey_types = $tmp;
        }
        return $ey_types;
    }

    /**
     * @name 前台对话框交互
     * @param array opt 配置数组
     *    |-- type: error|success
     *    |-- text: 提示文本
     *    |-- auto: true|false 是否自动关闭(如果有确认选项，关闭时执行确认选项内容)
     *    |-- yes: 确认选项内容
     *        |-- text: 确认文本
     *        |-- func: 点击确认时执行函数(没有则默认为关闭对话框)
     *    |-- no: 取消选项内容
     *        |-- text: 取消文本
     *        |-- func: 点击取消时执行函数(没有则默认为关闭对话框)
     */
    protected function dialogue($opt)
    {
        echo json_encode($opt);
        exit;
    }

    protected function ip($return_long = false)
    {
        $ip = '';
        if (isset($HTTP_SERVER_VARS)) {
            if (array_key_exists('HTTP_X_FORWARDED_FOR', $HTTP_SERVER_VARS)) {
                $ip = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
            } else if (array_key_exists('HTTP_CLIENT_IP', $HTTP_SERVER_VARS)) {
                $ip = $HTTP_SERVER_VARS['HTTP_CLIENT_IP'];
            } else if (array_key_exists('REMOTE_ADDR', $HTTP_SERVER_VARS)) {
                $ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
            }
        }
        if (empty($ip)) {
            if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
                $ip = $_SERVER['REMOTE_ADDR'];
            } else {
                $ip = '0.0.0.0';
            }
        }
        if (strpos($ip, ',') !== false) {
            $ip = explode(',', $ip, 2);
            $ip = current($ip);
        }
        return $return_long ? bindec(decbin(ip2long($ip))) : $ip;
    }

    protected function get_play_bonus($play_id)
    {
        //$sql = "SELECT `bonusProp`,`bonusPropBase` FROM `{$this->db_prefix}played` WHERE `id`={$play_id} LIMIT 1";
        //$data = $this->db->query($sql, 2);
		$con = mysql_connect(DB_HOST,DB_USER,DB_PASS);
		mysql_select_db(DB_NAME, $con);
		$sql = mysql_query("SELECT `bonusProp`,`bonusPropBase` FROM `{$this->db_prefix}played` WHERE `id`={$play_id} LIMIT 1");
		$data2 = mysql_fetch_array($sql);

        $baseJJZ = 1900;
        $maxJJZ = $baseJJZ + $this->user['fanDian'] * 2 / 0.1;
		
		$js_bl=0.2;
		if(strlen(intval($data2['bonusPropBase']))==10){
			$js_bl=20000000;
		}else if(strlen(intval($data2['bonusPropBase']))==9){
			$js_bl=2000000;
		}else if(strlen(intval($data2['bonusPropBase']))==8){
			$js_bl=200000;
		}else if(strlen(intval($data2['bonusPropBase']))==7){
			$js_bl=20000;
		}else if(strlen(intval($data2['bonusPropBase']))==6){
			$js_bl=2000;
		}else if(strlen(intval($data2['bonusPropBase']))==5){
			$js_bl=200;
		}else if(strlen(intval($data2['bonusPropBase']))==4){
			$js_bl=20;
		}else if(strlen(intval($data2['bonusPropBase']))==3){
			$js_bl=2;
		}else if(strlen(intval($data2['bonusPropBase']))==2){
			$js_bl=0.2;
		}
		
		$js_bonusPropBase=($data2['bonusPropBase']+($this->user['fanDian']*$js_bl));

        //计算最大奖金
        $data['bonusProp'] = $data2['bonusPropBase'] / 1900 * $maxJJZ;

        $data['bonusProp'] = $js_bonusPropBase;
        $data['bonusPropBase'] = $data2['bonusPropBase'];
        $data['maxJJZ'] = $maxJJZ;
        $data['baseJJZ'] = $baseJJZ;
		mysql_close($con);

        return $data;
    }


    // 刷新session
    protected function fresh_user_session()
    {
        if (!$this->user) return false;
        $sessionId = $this->user['sessionId'];
        $uid = $this->user['uid'];
        $sql = "SELECT * FROM `{$this->db_prefix}members` WHERE `uid`=$uid LIMIT 1";
        $user = $this->db->query($sql, 2);
        $user['sessionId'] = $sessionId;
        $user['_gameFanDian'] = $this->config['fanDianMax'];
        $_SESSION[$this->user_session] = serialize($user);
        $this->user = $user;
        return true;
    }

    // 用户资金变动(请在一个事务里使用)
    protected function set_user_coin($log)
    {
        if (!array_key_exists("uid", $log)) {
            return false;
        }
        $default = array(
            'coin' => 0,
            'fcoin' => 0,
            'uid' => $log["uid"],
            'liqType' => 0,
            'type' => 0,
            'info' => '',
            'extfield0' => 0,
            'extfield1' => '',
            'extfield2' => '',
        );
        $sql = 'call setCoin(';
        foreach ($default as $k => $v) {
            $val = (array_key_exists($k, $log) && $log[$k]) ? $log[$k] : $v;
            if ($v !== 0) $val = "'$val'";
            $sql .= $val . ',';
        }
        $sql = substr($sql, 0, -1) . ')';
        $this->db->query($sql, 0);
    }

    // 用户资金变动(请在一个事务里使用)
    protected function set_coin($log)
    {
        $default = array(
            'coin' => 0,
            'fcoin' => 0,
            'uid' => $this->user['uid'],
            'liqType' => 0,
            'type' => 0,
            'info' => '',
            'extfield0' => 0,
            'extfield1' => '',
            'extfield2' => '',
        );
        $sql = 'call setCoin(';
        foreach ($default as $k => $v) {
            $val = (array_key_exists($k, $log) && $log[$k]) ? $log[$k] : $v;
            if ($v !== 0) $val = "'$val'";
            $sql .= $val . ',';
        }
        $sql = substr($sql, 0, -1) . ')';
        $this->db->query($sql, 0);
    }

    // 根据类型获取玩法列表
    protected function get_plays($group_id = 0)
    {
        $where = $group_id === 0 ? '' : " AND `groupId`=$group_id ";
        $sql = "SELECT `id`,`name`,`playedTpl`,`enable`,`maxcount`,`betCountFun`,`bonusPropBase`,`bonusProp`,`groupId`,`type`,`minCharge` FROM `{$this->db_prefix}played` WHERE `enable`=1 $where ORDER BY `sort`";
        $data = $this->db->query($sql, 3);
        $plays = array();
        foreach ($data as $v) $plays[$v['id']] = $v;

        return $plays;
    }

    protected function get_my_plays($group_id = 0)
    {
        $where = $group_id === 0 ? '' : " AND `groupId`=$group_id ";
        $sql = "SELECT `id`,`name`,`playedTpl`,`enable`,`maxcount`,`betCountFun`,`bonusPropBase`,`bonusProp`,`groupId`,`type`,`minCharge` FROM `{$this->db_prefix}played` WHERE `enable`=1 $where ORDER BY `sort`";
        $data = $this->db->query($sql, 3);
        return $data;
    }

    protected function get_groups($type = 0)
    {
        $where = $type === 0 ? '' : " AND `type`={$type} ";
        $sql = "SELECT * FROM {$this->db_prefix}played_group WHERE `enable`=1 __WHERE__ ORDER BY `sort` ASC";
        $sql = str_replace("__WHERE__", $where, $sql);
        $data = $this->db->query($sql, 3);
        return $data;
    }

    // 获取GET中的数字参数
    protected function get_id($key = 'id')
    {
        return $this->request->request($key, 0, "intval");
    }

    // 获取当前页码
    protected function get_page()
    {
        return $this->request->request("page", 1, "intval");
    }

    // 获取数据列表最大页码
    protected function get_page_max($total)
    {
        $page_max = ceil($total / $this->pagesize);
        return $page_max ? $page_max : 1;
    }

    // 获取查询起始时间和结束时间
    protected function get_time($get = true)
    {
        $data = $get ? $_GET : $_POST;
        $time_from = $this->time - 86400 * 7;
        $time_from = date('Y-m-d H:i', $time_from < $this->user['regTime'] ? $this->user['regTime'] : $time_from);
        $time_to = date('Y-m-d H:i', $this->time);
        $this->request_time_from = strtotime((array_key_exists('fromTime', $data) && $data['fromTime']) ? $data['fromTime'] : $time_from);
        $this->request_time_to = strtotime((array_key_exists('toTime', $data) && $data['toTime']) ? $data['toTime'] : $time_to);
        if (!$this->request_time_from || !$this->request_time_to) core::__403();
        //if ($this->request_time_from >= $this->request_time_to) core::error('查询[起始时间]必须小于[结束时间]');
        $now = date('H:i');
        if (date('H:i', $this->request_time_from) === $now) $this->request_time_from -= 60;
        if (date('H:i', $this->request_time_to)) $this->request_time_to += 60;
    }

    // 获取彩种列表(带分类)
    protected function get_types()
    {
        static $types = array();
        if (!$types) {
            $games = array(
                1 => '时时彩',
                2 => '11选5',
                9 => '快三',
                3 => '低频彩',
                6 => 'PK10',
                8 => '快乐8',
                4 => '快乐十分',
                11 => '全天彩',
            );
            foreach ($games as $type => $name) {
                $types[$type] = $this->db->query("SELECT `id`,`title` FROM `{$this->db_prefix}type` WHERE enable=1 AND `isDelete`=0 AND type=$type", 3);
            }
        }
        return $types;
    }


    protected function get_types_with_img()
    {
        $games = array(
            1 => array('title' => '时时彩', 'img' => 'ssc_logo.png'),
            2 => array('title' => '11选5', 'img' => '11x5_logo.png'),
            9 => array('title' => '快三', 'img' => 'k3_logo.png'),
            3 => array('title' => '低频彩', 'img' => '013.png'),
            6 => array('title' => 'PK10', 'img' => 'pk10_logo.png'),
//            8 => array('title' => '快乐8', 'img' => '015.png'),
//            4 => array('title' => '快乐十分', 'img' => '015.png'),
//            11 => array('title' => '全天彩', 'img' => '015.png'),
            //真人娱乐，体育博弈，棋牌游戏，电子游艺栏
            100 => array('title' => '真人娱乐', 'img' => 'zryl_logo.png', 'is_building' => true),
            101 => array('title' => '体育博弈', 'img' => 'tyby_logo.png', 'is_building' => true),
            102 => array('title' => '棋牌游戏', 'img' => 'qpyx_logo.png', 'is_building' => true),
            103 => array('title' => '电子游艺栏', 'img' => 'dzyx_logo.png', 'is_building' => true),
        );
        $types = $this->get_types();
        foreach ($types as $k => $v) {
            if (isset($games[$k])) {
                $games[$k]['type_list'] = $v;
            }
        }
        return $games;
    }


    // post校验
    protected function check_post()
    {
        if (!$this->post) core::__403();
    }

    // ajax加载
    protected function ajax()
    {
        $this->display('index', array('load_self' => true));
    }

    // 组装时间范围查询条件
    protected function build_where_time($field)
    {
        $where = '';
        $time_from = $this->request_time_from;
        $time_to = $this->request_time_to;
        if ($time_from && $time_to) {
            $where = " AND $field BETWEEN $time_from AND $time_to";
        } else if ($time_from) {
            $where = " AND $field>=$time_from";
        } else if ($time_to) {
            $where = " AND $field<$time_to";
        }
        return $where;
    }

    // 根据用户ID获取用户名
    protected function get_username($uid)
    {
        static $usernames = array();
        if (!array_key_exists($uid, $usernames)) {
            $data = $this->db->query("SELECT `username` FROM `{$this->db_prefix}members` WHERE `uid`={$uid} LIMIT 1", 2);
            $usernames[$uid] = $data['username'];
        }
        return $usernames[$uid];
    }

    protected function myxor($string, $key = '')
    {
        if ('' == $string) return '';
        if ('' == $key) $key = 'cd';
        $len1 = strlen($string);
        $len2 = strlen($key);
        if ($len1 > $len2) $key = str_repeat($key, ceil($len1 / $len2));
        return $string ^ $key;
    }

    protected function str2hex($string)
    {
        $hex = "";
        for ($i = 0; $i < strlen($string); $i++) {
            $hex .= dechex(ord($string[$i]));
        }
        $hex = strtoupper($hex);
        return $hex;
    }

    protected function hex2str($hex)
    {
        $string = "";
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    /**
     * @name 模板加载
     * @param string tpl_name 模板名称
     * @param array args 模板参数
     */
    protected function display($tpl_name, $args = array())
    {
        define('TPL', SYSTEM . '/tpl/' . $this->client_type);
        extract($args);
        require(TPL . '/' . $tpl_name . '.tpl.php');
    }

}