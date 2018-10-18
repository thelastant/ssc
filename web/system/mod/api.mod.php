<?php

/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 19:15
 */
class mod_api extends mod
{
    public function get_agent_finance_logs()
    {

    }

    //获取手机版数据
    public function get_wp_types()
    {
        $map = array(
            "enable" => 1,
            "isDelete" => 0,
        );
        $data = $this->db->search("{$this->db_prefix}type", $map, "id,title,is_hot,is_sale");
        //遍历吧，以后再优化都忘记sql怎么写了
        foreach ($data as &$item) {
            $lottery = $this->db->query("SELECT `data`,`number` FROM `{$this->db_prefix}data` WHERE `type`={$item['id']} ORDER BY `number` DESC LIMIT 1", 2);
            if ($lottery) {
                $item['last_result'] = explode(",", $lottery['data']);
                $item['last_no'] = $lottery['number'];
            }
            unset($item);
        }
if(wappc==0){
        //进行数据分组
        $groups = array(
            array(
                'title' => "时时彩",
                'types' => array(1, 24, 120, 121, 123, 125, 126, 35, 66, 67, 68, 69, 127, 128),
                'type_list' => array(),
            ),
            array(
                'title' => "澳门全天彩",
                'types' => array(5, 26, 29, 14),
                'type_list' => array(),
            ),
            array(
                'title' => "十一选五",
                'types' => array(6, 7, 16, 23),
                'type_list' => array(),
            ),
            array(
                'title' => "其他彩种",
                'types' => array(9, 10, 20, 25, 53, 39, 50, 60),
                'type_list' => array(),
            ),
        );
}else{
        //进行数据分组
        $groups = array(
            array(
                'title' => "时时彩",
                'types' => array(1, 24, 120, 121, 123, 125, 126, 35, 66, 67, 68, 69, 127, 128),
                'type_list' => array(),
            ),
            array(
                'title' => "澳门全天彩",
                'types' => array(5, 26, 29, 14),
                'type_list' => array(),
            ),
            array(
                'title' => "十一选五",
                'types' => array(6, 7, 16, 23),
                'type_list' => array(),
            ),
            array(
                'title' => "其他彩种",
                'types' => array(9, 122, 10, 20, 25, 53, 39, 50, 60),
                'type_list' => array(),
            ),
        );
}
        foreach ($data as $item) {
            foreach ($groups as &$groupItem) {
                if (in_array(intval($item['id']), $groupItem['types'], true)) {
                    $groupItem["type_list"][] = $item;
                }
            }
        }
        $this->api_return("success", 200, $groups);
    }

    public function get_api_groups()
    {
        $request = $_REQUEST;
        if (!array_key_exists("type", $request)) {
            $this->api_err_return("system err");
        }
        $type = intval($_REQUEST["type"]);
        if ($type <= 0) {
            $this->api_err_return("system err");
        }
        $groups = $this->get_groups($type);
        $this->api_return("success", 200, $groups);
    }

    public function get_api_plays()
    {
        $request = $_REQUEST;
        if (!array_key_exists("group_id", $request)) {
            $this->api_err_return("system err");
        }
        $group_id = intval($_REQUEST["group_id"]);
        $plays = $this->get_my_plays($group_id);
        $this->api_return("success", 200, $plays);
    }


    public function get_index_types()
    {

        $sql = "SELECT id,name,title,sort FROM {$this->db_prefix}type WHERE `enable`=1 AND `isDelete`=0";
        $data = $this->db->query($sql, 3);
        //遍历吧，以后再优化都忘记sql怎么写了
        foreach ($data as &$item) {
            $lottery = $this->db->query("SELECT `data`,`number` FROM `{$this->db_prefix}data` WHERE `type`={$item['id']} ORDER BY `number` DESC LIMIT 1", 2);
            if ($lottery) {
                $item['last_result'] = explode(",", $lottery['data']);
                $item['last_no'] = $lottery['number'];
            }
        }
        $this->api_return("success", 200, $data);
    }

    public function get_cp_list()
    {
        $plays = core::lib('game')->get_type_list();
        $this->api_return("success", 200, $plays);
    }

    public function api_get_qy()
    {
        $uid = $this->request->request("uid", $this->user["uid"], "intval");
        if (!$this->is_child($uid, $this->user['uid'], null, 1) && $uid != $this->user["uid"]) {
            $this->api_err_return("对不起,你无权查看");
        }
        $data = $this->db->search("{$this->db_prefix}member_contract", array('uid' => $uid), "*", "ORDER BY `red_rate` ASC");
        $this->api_return("success", 200, $data);
    }

    //契约发放日志
    public function api_get_qy_logs()
    {
        $search = $this->request->request("search/a");
        $search['uid'] = $this->request->request("search.uid", 0, "intval");
        $search['start_time'] = date("Y-m-d", strtotime($search['start_time']));
        $search['end_time'] = date("Y-m-d", strtotime($search['end_time']));
        if (!$this->is_child($search['uid'], $this->user['uid']) && $search['uid'] != $this->user['uid'] && $search['uid'] != 0) {
            $this->api_err_return("对不起,你无权查看");
        }
        $map = array();
        $uidStr = "0";
        if ($search['uid'] == 0) {
            //查询儿子
            $uidStr .= ",{$this->user['uid']}";
            $uids = $this->_getChildren("uid", 1);
            foreach ($uids as $uid) {
                $uidStr .= ",";
                $uidStr .= intval($uid['uid']);
            }
        } else {
            $uidStr .= ",";
            $uidStr .= intval($search['uid']);
        }
        $map['uid'] = "in({$uidStr})";
        //{$this->db_prefix}member_contract_logs", $map, "*", "ORDER BY `send_time` DESC
        $sql = <<<SQL
    SELECT l.*,m.username FROM {$this->db_prefix}member_contract_logs l LEFT JOIN {$this->db_prefix}members m ON m.uid=l.uid WHERE l.`uid`{$map['uid']} AND 
  `send_date`>='{$search['start_time']}' AND `send_date`<='{$search['end_time']}' ORDER BY l.`send_time` DESC LIMIT 10000
SQL;
        $data = $this->db->query($sql, 3);
        $this->api_return("success", 200, $data);
    }


    public function get_cp_types()
    {
        $json['type_list'] = $this->get_ey_types(true);
        $this->api_return("success", 200, $json);
    }

    public function get_user_info()
    {

        //查询用户信息
        $this->user = $this->db->find("{$this->db_prefix}members", array("uid" => $this->user["uid"]));
        $user = $this->user;
        unset($user['password']);
        unset($user['isDelete']);
        $this->api_return("success", 200, $user);
    }

    //银行
    public function get_user_banks()
    {
        $uid = intval($this->user['uid']);
        $sql = "SELECT m.*,b.name as bank_name FROM `{$this->db_prefix}member_bank` as m LEFT JOIN `{$this->db_prefix}bank_list` as b ON(m.bankId=b.id) WHERE `uid`=$uid ORDER BY `bdtime`";
        $banks = $this->db->query($sql, 3);
        if (count($banks) <= 0) {
            $this->api_err_return("没有数据配置");
        }
        $this->api_return("success", 200, $banks);
    }

    public function get_system_banks()
    {
        $banks = $this->db->query("SELECT `id`,`name`,`sort` FROM `{$this->db_prefix}bank_list` WHERE `enable`=1 AND `isDelete`=0 ORDER BY `sort`", 3);
        if (count($banks) <= 0) {
            $this->api_err_return("没有数据配置");
        }
        $this->api_return("success", 200, $banks);
    }
}
