<?php

class lib_game
{

    private $db; // 数据库连接
    private $db_prefix; // 数据库表前缀
    private $time; // 当前时间
    private $types = array(); // 彩种列表
    private $ftimes = array(); // 彩种延迟时间列表
    private $lhc = array(122);

    public function __construct()
    {
        $this->db = core::lib('db');
        $this->db_prefix = DB_PREFIX;
        $this->time = time();
    }

    // 获取彩种列表
    public function get_types()
    {
        if ($this->types) return $this->types;
        $sql = "SELECT * FROM `{$this->db_prefix}type` WHERE `isDelete`=0 AND `enable`=1 ORDER BY `sort` ASC";
        $data = $this->db->query($sql, 3);
        foreach ($data as $v) $this->types[$v['id']] = $v;
        return $this->types;
    }

    //获取当期时间
    public function get_game_current_time($type_id, $old = 0)
    {
        $current = $this->get_game_no($type_id);
        if ($type_id == 1 && $current['actionTime'] == '00:00') {
            $actionTime = strtotime($current['actionTime']) + 24 * 3600;
        } else {
            $actionTime = strtotime($current['actionTime']);
        }
        if (!$actionTime) $actionTime = $old;
        return $actionTime;
    }


    //封单时间
    public function get_type_ftime($type_id)
    {
        if (!array_key_exists($type_id, $this->ftimes)) {
            $ftime = $this->db->query("SELECT `data_ftime` FROM `{$this->db_prefix}type` WHERE `id`=$type_id LIMIT 1", 2);
            $ftime = $ftime ? $ftime['data_ftime'] : 10;
            $this->ftimes[$type_id] = $ftime;
        }
        return $this->ftimes[$type_id];
    }

    // 期号格式化
    private function no_format($no)
    {
        $no = str_replace('-', '', $no);
        return $no;
    }

    /**
     * @name 读取下期期号
     * @param int type_id 彩种ID
     * @param int time 时间，默认为当前时间
     */
    public function get_game_no($type_id, $time = null)
    {
        $type_id = intval($type_id);
        if ($time === null) $time = $this->time;
        $ftime = $this->get_type_ftime($type_id);
        $action_time = date('H:i:s', $time + $ftime);

        $sql = "SELECT `actionNo`,`actionTime` FROM `{$this->db_prefix}data_time` WHERE `type`=$type_id AND `actionTime`>'$action_time' ORDER BY `actionTime` LIMIT 1";
        $result = $this->db->query($sql, 2);

        if (!$result) {
            $sql = "SELECT `actionNo`,`actionTime` FROM `{$this->db_prefix}data_time` WHERE `type`=$type_id ORDER BY `actionTime` LIMIT 1";
            $result = $this->db->query($sql, 2);
            $time = $time + 24 * 3600;
        }
        $types = $this->get_types();
        if (($func = $types[$type_id]['onGetNoed']) && method_exists($this, $func)) {
            $this->$func($result['actionNo'], $result['actionTime'], $time);
        }
        //如果
        if (in_array($type_id, $this->lhc)) {
            $action_time = date("Y-m-d H:i:s", $time + $ftime);
			$action_timed2 = date("Y-m-d H:i:s", $time);
            $sql = "SELECT * FROM `{$this->db_prefix}data_lhc_time` WHERE `type`=$type_id AND `action_date`>'$action_timed2' ORDER BY `actionNo` DESC LIMIT 1";
            $result = $this->db->query($sql, 2);
            if (!$result) {
				$sql = "SELECT * FROM `{$this->db_prefix}data_lhc_time` WHERE `type`=$type_id ORDER BY `actionNo` DESC LIMIT 1";
				$result = $this->db->query($sql, 2);
                //core::error("SYSTEM ERR,对不起6合彩不支持追号");
            }
            $result['actionTime'] = $result['action_date'];
        }

        $result['actionNo'] = $this->no_format($result['actionNo']);
        return $result;
    }


    /**
     * 读取上期(当前期) 期号
     * @param $type_id
     * @param null $time
     * @return mixed
     */
    public function get_game_last_no($type_id, $time = null)
    {
        $type_id = intval($type_id);
        if ($time === null) $time = $this->time;
        $ftime = $this->get_type_ftime($type_id);
        $action_time = date('H:i:s', $time + $ftime);

        $sql = "SELECT `actionNo`,`actionTime` FROM `{$this->db_prefix}data_time` WHERE `type`=$type_id AND `actionTime`<='$action_time' ORDER BY `actionTime` DESC LIMIT 1";
        $result = $this->db->query($sql, 2);

        if (!$result) {
            $sql = "SELECT `actionNo`,`actionTime` FROM `{$this->db_prefix}data_time` WHERE `type`=$type_id ORDER BY `actionNo` DESC LIMIT 1";
            $result = $this->db->query($sql, 2);
            $time = $time - 24 * 3600;
        }

        $types = $this->get_types();
        if (($func = $types[$type_id]['onGetNoed']) && method_exists($this, $func)) {
            $this->$func($result['actionNo'], $result['actionTime'], $time, false);
        }
        $result['actionNo'] = $this->no_format($result['actionNo']);
        return $result;
    }

    // 获取近期期号
    public function get_game_recent_no($type_id, $num)
    {
        $type_id = intval($type_id);
        $time = $this->time;
        $ftime = $this->get_type_ftime($type_id);
        $action_time = date('H:i:s', $time + $ftime);

        $where = "WHERE `type`=$type_id AND `actionTime`<='$action_time'";
        $data = $this->db->query("SELECT COUNT(1) AS `__total` FROM `{$this->db_prefix}data_time` $where", 2);
        $total = $data['__total'] ? $data['__total'] : 1;
        $skip = $total > $num ? $num : $total - 1;
        $sql = "SELECT `actionNo`,`actionTime` FROM `{$this->db_prefix}data_time` $where ORDER BY `actionTime` DESC LIMIT $skip,1";
        $result = $this->db->query($sql, 2);

        if (!$result) {
            $sql = "SELECT `actionNo`,`actionTime` FROM `{$this->db_prefix}data_time` WHERE `type`=$type_id ORDER BY `actionNo` DESC LIMIT 1";
            $result = $this->db->query($sql, 2);
            $time = $time - 24 * 3600;
        }

        $types = $this->get_types();
        if (($func = $types[$type_id]['onGetNoed']) && method_exists($this, $func)) {
            $this->$func($result['actionNo'], $result['actionTime'], $time, true);
        }

        $result['actionNo'] = $this->no_format($result['actionNo']);
        return $result;
    }

    // 获取近期期号
    public function get_game_next_nos($type_id, $num)
    {
        $type_id = intval($type_id);
        $time = $this->time;
        $ftime = $this->get_type_ftime($type_id);
        $action_time = date('H:i:s', $time + $ftime);

        $where = "WHERE `type`=$type_id AND `actionTime`>='$action_time'";
        $data = $this->db->query("SELECT COUNT(1) AS `__total` FROM `{$this->db_prefix}data_time` $where", 2);
        $total = $data['__total'] ? $data['__total'] : 1;
        $limit = $num ? ($total > $num ? $num : $total) : $total;
        $sql = "SELECT `actionNo`,`actionTime` FROM `{$this->db_prefix}data_time` $where ORDER BY `actionTime` ASC LIMIT {$limit}";
        $result = $this->db->query($sql, 3);

        if (!$result) {
            $sql = "SELECT `actionNo`,`actionTime` FROM `{$this->db_prefix}data_time` WHERE `type`=$type_id ORDER BY `actionNo` ASC LIMIT {$num}";
            $result = $this->db->query($sql, 3);
            $time = $time - 24 * 3600;
        }

        $types = $this->get_types();
        if (($func = $types[$type_id]['onGetNoed']) && method_exists($this, $func)) {
            foreach ($result as &$r) {
                $this->$func($r['actionNo'], $r['actionTime'], $time, true);
                $r['actionNo'] = $this->no_format($r['actionNo']);
            }
        }

        return $result;
    }


    #region 时间解析器

    private function setTimeNo(&$actionTime, &$time = null)
    {
        if (!preg_match('/^(\d{2}\:){2}\d{2}$/', $actionTime)) {
            return false;
        }
        if (!$time) $time = $this->time;
        $actionTime = date('Y-m-d ', $time) . $actionTime;
    }

    //这里是修复时间得
    private function noHdCQSSC(&$actionNo, &$actionTime, $time = null)
    {
        if (!is_numeric($actionNo)) core::error('开奖时间表中期号数据错误');
        $this->setTimeNo($actionTime, $time);
		if(($actionNo==0 || $actionNo==120) and ((date("H")==00 and date("i")==00) or (date("H")==23 and date("i")>=55))){
			$actionNo=date('Ymd120', $time - 24*3600);
			$actionTime=date('Y-m-d 00:00', $time);
		}else{
			$actionNo=date('Ymd', $time).substr(1000+$actionNo,1);
		}
    }

    private function no0Hd(&$actionNo, &$actionTime, $time = null)
    {
        $this->setTimeNo($actionTime, $time);
        $no = substr(1000 + $actionNo, 1);
        if (substr($no, 0, 1) === '0') $no = substr($no, 1);
        $actionNo = date('Ymd', $time) . $no;
    }

    private function no0Hd_1(&$actionNo, &$actionTime, $time = null)
    {
        $this->setTimeNo($actionTime, $time);
        $actionNo = date('Ymd', $time) . substr(100 + $actionNo, 1);
    }

    private function no0Hd_2(&$actionNo, &$actionTime, $time = null)
    {
        $this->setTimeNo($actionTime, $time);
        $actionNo = date('Ymd', $time) . substr(1000 + $actionNo, 1);
    }

    private function no0Hd_3(&$actionNo, &$actionTime, $time = null)
    {
        $this->setTimeNo($actionTime, $time);
        $actionNo = date('ymd', $time) . substr(100 + $actionNo, 1);
    }

    private function pai3(&$actionNo, &$actionTime, $time = null)
    {
        $this->setTimeNo($actionTime, $time);
        $actionNo = date('Yz', $time) - 7;
        $actionNo = substr($actionNo, 0, 4) . substr(substr($actionNo, 4) + 1001, 1);
        if ($actionTime < date('Y-m-d H:i:s', $time)) $actionTime = date('Y-m-d 18:30', $time);
    }

    private function pai3x(&$actionNo, &$actionTime, $time = null)
    {
        $this->setTimeNo($actionTime, $time);
        $actionNo = date('Yz', $time) - 7;
        $actionNo = substr($actionNo, 0, 4) . substr(substr($actionNo, 4) + 1001, 1);
        if ($actionTime < date('Y-m-d H:i:s', $time)) $actionTime = date('Y-m-d 20:30', $time);
    }

    private function noxHd(&$actionNo, &$actionTime, $time = null)
    {
        $this->setTimeNo($actionTime, $time);
        if ($actionNo > 84) $time -= 24 * 3600;
        $actionNo = date('Ymd', $time) . substr(1000 + $actionNo, 1);
    }

    //计算期数算法
    private function BJpk10(&$actionNo, &$actionTime, $time = null)
    {
        $this->setTimeNo($actionTime, $time);
        $actionNo = 179 * (strtotime(date('Y-m-d', $time)) - strtotime('2007-11-11')) / 3600 / 24 + $actionNo - 3793-1253;
    }

    //分分彩时间规划
    private function no0Hdx(&$actionNo, &$actionTime, $time = null)
    {
		$this->setTimeNo($actionTime, $time);
		if($actionNo==1440 and ((date("H")==00 and date("i")==00) or (date("H")==23 and date("i")>=59))){
			$actionNo=date('Ymd1440', $time - 24*3600);
		}else{
			$actionNo=date('Ymd', $time) . substr(10000 + $actionNo, 1);
		}
    }

    private function bj_Kuai8(&$actionNo, &$actionTime, $time = null)
    {
        //开始第一期
        $startNo = 813835;
        $startTime = strtotime("2017-03-23");
        //看看过了多少天了
        $this->setTimeNo($actionTime, $time);
        $tmpTime = strtotime($actionTime);
        $nowUp = floor(($tmpTime - $startTime) / (24 * 3600));
        $startNo += (179 * $nowUp);
        $actionNo = $startNo + $actionNo;

    }

    private function noHd(&$actionNo, &$actionTime, $time = null)
    {
        $this->setTimeNo($actionTime, $time);
        $actionNo = date('Ymd', $time) . substr(100 + $actionNo, 1);
    }

    //新加坡2分彩时间修复
    private function sigpo_2fc(&$actionNo, &$actionTime, $time = null)
    {
        //开始第一期
        $startNo = 2723387;
        $startTime = strtotime("2017-05-09");
        //看看过了多少天了
        $this->setTimeNo($actionTime, $time);
        $tmpTime = strtotime($actionTime);
        $nowUp = floor(($tmpTime - $startTime) / (24 * 3600));
        $startNo += (660 * $nowUp);
        $actionNo = $startNo + $actionNo;
    }

    //新加坡2分彩时间修复,快乐8
    private function sigpo_kl8(&$actionNo, &$actionTime, $time = null)
    {
        //开始第一期
        $startNo = 2723387;
        $startTime = strtotime("2017-05-09");
        //看看过了多少天了
        $this->setTimeNo($actionTime, $time);
        $tmpTime = strtotime($actionTime);
        $nowUp = floor(($tmpTime - $startTime) / (24 * 3600));
        $startNo += (660 * $nowUp);
        $actionNo = $startNo + $actionNo;
    }


    //香港六合彩
    public function xgl_hc(&$actionNo, &$actionTime, $time = null, $current = false)
    {
        $this->setTimeNo($actionTime, $time);
        //查询一下数据库的上次的期数
        $db = core::lib('db');
        $dbPrefix = DB_PREFIX;
        if ($current) {
            //获取下期数据了

        } else {
            //上期数据
            $date = date("Y-m-d", strtotime(date("Y-m-d") . " -2day"));
            $sql = "SELECT * FROM `{$dbPrefix}data_lhc_time` WHERE 1 AND `action_date`>='{$date}' ORDER BY `action_date` ASC  LIMIT 1";
            $data = $db->query($sql, 2);
            $actionNo = $data['actionNo'];
        }
    }


    //台湾冰果
    public function taiwan_bingo(&$actionNo, &$actionTime, $time = null)
    {
        //开始第一期
        $startNo = 106016443;
        $startTime = strtotime("2017-03-23");
        //看看过了多少天了
        $this->setTimeNo($actionTime, $time);
        $tmpTime = strtotime($actionTime);
        $nowUp = floor(($tmpTime - $startTime) / (24 * 3600));
        $startNo += (203 * $nowUp);
        $actionNo = $startNo + $actionNo;
    }

    //韩国1.5,首尔
    public function hanguo_1d5sefc(&$actionNo, &$actionTime, $time = null)
    {
        //开始第一期
        $startNo = 1809358;
        //中国时间第0期
        $startTime = strtotime("2017-03-28 23:01:30");
        //看看过了多少天了
        $this->setTimeNo($actionTime, $time);
        $tmpTime = strtotime($actionTime);
        $nowUp = floor(($tmpTime - $startTime) / (24 * 3600));
        $startNo += (960 * $nowUp);
        $actionNo = $startNo + $actionNo;
    }

    //韩国1.5
    public function hanguo_1d5fc(&$actionNo, &$actionTime, $time = null)
    {
        //开始第一期
        $startNo = 1804204;
        $startTime = strtotime("2017-03-23");
        //看看过了多少天了
        $this->setTimeNo($actionTime, $time);
        $tmpTime = strtotime($actionTime);
        $nowUp = floor(($tmpTime - $startTime) / (24 * 3600));
        $startNo += (880 * $nowUp);
        $actionNo = $startNo + $actionNo;
    }


    //新西兰45分彩
    public function xxl_4d5fc(&$actionNo, &$actionTime, $time = null)
    {
        $this->setTimeNo($actionTime, $time);
        //判断
        $timeCheck = strtotime(date("Y-m-d 20:00:40"));
        //如果大于这时间那个期数得加一
        if (time() >= $timeCheck) {
            $actionNo = date('Ymd', strtotime('+1day')) . substr(10000 + $actionNo, 1);
        } else {
            $actionNo = date('Ymd', $time) . substr(10000 + $actionNo, 1);
        }
    }
    #endregion
}