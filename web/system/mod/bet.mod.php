<?php

class mod_bet extends mod
{
    private $errMsg = "";

    #region prop

    /**
     * @return string
     */
    public function getErrMsg()
    {
        return $this->errMsg;
    }

    /**
     * @param string $errMsg
     */
    public function setErrMsg($errMsg)
    {
        $this->errMsg = $errMsg;
    }

#endregion


    private function remove($id)
    {
		$con = mysql_connect(DB_HOST,DB_USER,DB_PASS);
		mysql_select_db(DB_NAME, $con);
        $this->db->transaction('begin');
        try {
            //$data = $this->DBC->get("bets", "*", ["id" => $id, "isDelete" => 0, "uid" => $this->user['uid']]);
			$sql = mysql_query("SELECT * FROM `{$this->db_prefix}bets` WHERE `id`=$id and isDelete=0 and uid={$this->user['uid']} LIMIT 1");
			$data = mysql_fetch_array($sql);
			
			//$data = $this->db->query("SELECT * FROM `{$this->db_prefix}bets` WHERE `id`=$id and isDelete=0 and uid=$this->user['uid'] LIMIT 1", 2);
            if (!$data) {
                throw new Exception("对不起，订单已经撤销或者不存在。");
            }
            //检查是否是今天之内撤单
            if (strtotime(date("Y-m-d", $data['actionTime']) . " +1day") <= time()) {
                throw new Exception("对不起，已经超出撤单时限。");
            }

            if ($data['kjTime'] <= $this->time || $data['lotteryNo']) {
                throw new Exception("您提交的下注正在开奖，不能撤单");
            }

            // 冻结时间后不能撤单
            $ftime = core::lib('game')->get_type_ftime($data['type']);
            if ($data['kjTime'] - $ftime < $this->time) {
                throw new Exception("您提交的下注正在开奖，不能撤单");
            }
            // 添加用户资金变更日志
            $amount = $data['beiShu'] * $data['mode'] * $data['actionNum'];
            $amount = abs($amount);
            $this->set_coin(array(
                'uid' => $data['uid'],
                'type' => $data['type'],
                'playedId' => $data['playedId'],
                'liqType' => 7,
                'info' => '撤单',
                'extfield0' => $id,
                'coin' => $amount,
            ));
            // 更改定单为已经删除状态
            $this->db->query("UPDATE `{$this->db_prefix}bets` SET `isDelete`=1 WHERE `id`=$id LIMIT 1", 0);
            $this->db->transaction('commit');
            return true;
        } catch (Exception $e) {
            $this->db->transaction('rollBack');
            $this->setErrMsg($e->getMessage());
            return false;
        }
		mysql_close($con);
    }

    //快速撤单
    public function clear_order()
    {
        $type = $this->request->request("type_id", 0, "intval");
        $map = [
            'uid' => $this->user['uid'],
            'isDelete' => 0,
            'lotteryNo' => '',
        ];
        if ($type > 0) {
            $map['type'] = $type;
        }
        $bets = $this->DBC->select("bets", ["id"], $map);
        foreach ($bets as $bet) {
            $this->remove($bet['id']);
        }
        $this->api_return("撤单成功");
    }

    public function remove_batch()
    {
        $this->check_post();
        if (!array_key_exists('ids', $_POST) || !is_array($_POST['ids'])) core::__403();
        foreach ($_POST['ids'] as $id) {
            if (!core::lib('validate')->number($id)) core::__403();
            $id = intval($id);
            if (!$this->remove($id)) {
                core::error($this->getErrMsg());
            }
        }
    }

    public function remove_single()
    {
        $this->check_post();
        $id = $this->get_id();
        if (!$this->remove($id)) {
            $this->api_err_return($this->getErrMsg());
        } else {
            $this->api_return("撤单成功");
        }
    }

    public function info()
    {
        //$this->check_post();
        $id = $this->get_id();
        $bet = $this->db->query("SELECT * FROM `{$this->db_prefix}bets` WHERE `id`=$id LIMIT 1", 2);
        if (!$bet) core::__403();
        $weiShu = $bet['weiShu'];
        $wei = '';
        if ($weiShu) {
            $w = array(16 => '万', 8 => '千', 4 => '百', 2 => '十', 1 => '个');
            foreach ($w as $p => $v) {
                if ($weiShu & $p) $wei .= $v;
            }
            $wei .= '：';
        }
        $betCont = $bet['mode'] * $bet['beiShu'] * $bet['actionNum'];
        $types = core::lib('game')->get_types();
        $plays = $this->get_plays();
        $html = '<div class="detail">';
        $html .= '<table class="table table-bordered" width="100%">';
        $html .= '<tr>';
        $html .= '<td class="k" width="14%">所属彩种</td>';
        $html .= '<td class="v" width="20%">' . ($types[$bet['type']]['shortName'] ? $types[$bet['type']]['shortName'] : $types[$bet['type']]['title']) . '</td>';
        $html .= '<td class="k" width="13%">订单玩法</td>';
        $html .= '<td class="v" width="20%">' . $plays[$bet['playedId']]['name'] . '</td>';
        $html .= '<td class="k" width="13%">订单状态</td>';
        if ($bet['isDelete'] == 1) {
            $status = '<span class="gray">已撤单</span>';
        } else if (!$bet['lotteryNo']) {
            $status = '<span class="green">未开奖</span>';
        } else if ($bet['zjCount']) {
            $status = '<span class="red">已派奖</span>';
        } else {
            $status = '未中奖';
        }
        $html .= '<td class="v" width="20%">' . $status . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="k">订单编号</td>';
        $html .= '<td class="v">' . $bet['wjorderId'] . '</td>';
        $html .= '<td class="k">倍数模式</td>';
        $html .= '<td class="v">' . $bet['beiShu'] . ' [' . $this->modes[$bet['mode']] . ']</td>';
        $html .= '<td class="k">奖金返点</td>';
        $html .= '<td class="v">' . $bet['bonusProp'] . ' - ' . number_format($bet['fanDian'], 1, '.', '') . '%</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="k">开奖号码</td>';
        $html .= '<td class="v">' . ($bet['lotteryNo'] ? $bet['lotteryNo'] : '--') . '</td>';
        $html .= '<td class="k">投注时间</td>';
        $html .= '<td class="v">' . date('Y-m-d H:i:s', $bet['actionTime']) . '</td>';
        $html .= '<td class="k">投注期号</td>';
        $html .= '<td class="v">' . $bet['actionNo'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="k">开奖时间</td>';
        $html .= '<td class="v">' . ($bet['lotteryNo'] ? date('m-d H:i:s', $bet['kjTime']) : '--') . '</td>';
        $html .= '<td class="k">购买注数</td>';
        $html .= '<td class="v">' . $bet['actionNum'] . ' 注</td>';
        $html .= '<td class="k">购买金额</td>';
        $html .= '<td class="v">' . number_format($betCont, 3, '.', '') . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="k">返点金额</td>';
        $html .= '<td class="v">' . ($bet['fanDian'] ? number_format(($bet['fanDian'] / 100) * $betCont, 4, '.', '') : '0') . ' 元</td>';
        $html .= '<td class="k">中奖金额</td>';
        $html .= '<td class="v">' . ($bet['lotteryNo'] ? $bet['bonus'] . ' 元' : '--') . '</td>';
        $html .= '<td class="k">购买盈亏</td>';
        if ($bet['lotteryNo']) {
            $money = number_format($bet['bonus'] - $betCont + ($bet['fanDian'] / 100) * $betCont, 4, '.', '');
            $loss_gain = ($money > 0 ? '赢' : '亏') . abs($money) . '元';
        } else {
            $loss_gain = '---';
        }
        $html .= '<td class="v">' . $loss_gain . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="6">';
        $html .= '<div class="actionData">' . $wei . $bet['actionData'] . '</div>';
        $html .= '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        $this->api_return($html);
    }

    // 获取游戏记录
    public function log()
    {
        if ($this->post) {
            $tpl = $this->ispage ? '/bet/log_body' : '/bet/log';
            $this->get_time();
            $args = $this->log_get_args();
            $page_current = $this->get_page();
            $game_log = $this->log_search_func($args, $page_current);
            $page_max = $this->get_page_max($game_log['total']);
            if ($page_current > $page_max) core::__403();
            $page_args = $this->log_page_args($args);

            $this->display($tpl, array(
                'args' => $args,
                '_types' => $this->get_types(),
                'types' => core::lib('game')->get_types(),
                'plays' => $this->get_plays(),
                'state' => array(0 => '所有状态', 1 => '已派奖', 2 => '未中奖', 3 => '未开奖', 4 => '追号', /*5 => '合买跟单',*/
                    6 => '撤单'),
                'data' => $game_log['data'],
                'page_current' => $page_current,
                'page_max' => $page_max,
                'page_url' => '/bet/log?' . http_build_query($page_args),
                'page_container' => '#bet-log-dom .body',
            ));
        } else {
            $this->ajax();
        }
    }

    // 搜索游戏记录
    public function log_search()
    {
        $this->check_post();
        $this->get_time(false);
        $args = $this->log_get_args(false);
        $game_log = $this->log_search_func($args, 1);
        $page_max = $this->get_page_max($game_log['total']);
        $page_args = $this->log_page_args($args);
        $this->display('/bet/log_body', array(
            'types' => core::lib('game')->get_types(),
            'plays' => $this->get_plays(),
            'data' => $game_log['data'],
            'page_current' => 1,
            'page_max' => $page_max,
            'page_url' => '/bet/log?' . http_build_query($page_args),
            'page_container' => '#bet-log-dom .body',
        ));
    }

    //获取投注,api
    public function api_bet_logs()
    {
        $this->get_time();
        $args = $this->log_get_args(false);
        $p = $this->request->request("page");
        $p = ($p == '') ? 1 : intval($p);
        $args['start_date'] = $this->request->request("search.start_date");
        $args['end_date'] = $this->request->request("search.end_date");
        $game_log = $this->log_search_func($args, $p);
        $json['args'] = $args;
        $json['states'] = array(0 => '所有状态', 1 => '已派奖', 2 => '未中奖', 3 => '未开奖', 4 => '追号', 5 => '合买跟单', 6 => '撤单');
        $json['data'] = $game_log['data'];
        if (count($json['data']) <= 0) {
            $this->api_err_return("没有更多了");
        }
        $this->api_return("success", 200, $json);
    }

    // 游戏记录搜索函数
    private function log_search_func($args, $page_current)
    {
        $uid = $args["uid"];
        if ($args["uid"] != $this->user["uid"]) {
            //check
            if (!$this->is_child($uid, $this->user['uid'])) {
                $this->api_err_return("对不起,您没有权限查看此数据");
            }
        }

        $pagesize = $this->pagesize;
        $skip = ($page_current - 1) * $pagesize;
        $sql = "SELECT ~field~ FROM `{$this->db_prefix}bets` LEFT JOIN {$this->db_prefix}type as t ON t.id=`{$this->db_prefix}bets`.type WHERE";
        $where = " `uid`={$uid}";
        if ($args['type']) $where .= " AND `type`={$args['type']}";
        if ($args['state']) {
            switch ($args['state']) {
                case 1: // 已派奖
                    $where .= ' AND `zjCount`>0';
                    break;

                case 2: // 未中奖
                    $where .= " AND `zjCount`=0 AND `lotteryNo`!='' AND `lottery_bets`.isDelete=0";
                    break;

                case 3: // 未开奖
                    $where .= " AND `lotteryNo`=''";
                    break;

                case 4: // 追号
                    $where .= ' AND `zhuiHao`=1';
                    break;

                case 5: // 合买跟单
                    $where .= ' AND `hmEnable`=1';
                    break;

                case 6: // 撤单
                    $where .= ' AND `lottery_bets`.isDelete=1';
                    break;
            }
        }


        if ($args['mode'] !== '0.000') $where .= " AND `mode`={$args['mode']}";
        if ($args['betId']) $where .= " AND `wjorderId`='{$args['betId']}'";

        if ($args['start_date'] != '' && $args['end_date'] != '') {
            $where .= " AND actionTime between " . strtotime($args['start_date']) . " AND " . strtotime($args['end_date']) . " ";
        } else {
            $where .= $this->build_where_time('`actionTime`');
        }
        if (substr($where, 0, 5) === ' AND ') $where = substr($where, 5);
        $sql .= $where;
        $sql .= ' ~order~ ~limit~';
        $sql_total = str_replace('~field~', 'COUNT(1) AS __total', $sql);
        $sql_total = str_replace('~limit~', '', $sql_total);
        $sql_total = str_replace('~order~', '', $sql_total);
        $total = $this->db->query($sql_total, 2);
        $total = $total['__total'];
        $sql_data = str_replace('~field~', "`{$this->db_prefix}bets`.*,t.title", $sql);
        $sql_data = str_replace('~limit~', "LIMIT $skip,$pagesize", $sql_data);
        $sql_data = str_replace('~order~', "ORDER BY {$this->db_prefix}bets.`id` DESC", $sql_data);
        $data = $this->db->query($sql_data, 3);
        return array(
            'data' => $data,
            'total' => $total,
        );
    }

    // 获取搜索参数
    private function log_get_args($get = true)
    {
        $data = $get ? $_GET : $_POST;
        $args = array();
        $args['type'] = (array_key_exists('type', $data) && core::lib('validate')->number($data['type'])) ? intval($data['type']) : 0;
        $args['state'] = (array_key_exists('state', $data) && in_array($data['state'], array(0, 1, 2, 3, 4, 5, 6))) ? intval($data['state']) : 0;
        $args['mode'] = (array_key_exists('mode', $data) && array_key_exists($data['mode'], $this->modes)) ? strval($data['mode']) : '0.000';
        $args['betId'] = (array_key_exists('betId', $data) && preg_match('/^[a-zA-Z0-9]{8}$/', $data['betId'])) ? $data['betId'] : '';
        $args['uid'] = $this->request->request("uid", $this->user["uid"], "intval");
        return $args;
    }

    // 组装网址参数
    private function log_page_args($args)
    {
        $page_args = array_filter($args);
        if ($this->request_time_from) $page_args['fromTime'] = date('Y-m-d H:i', $this->request_time_from);
        if ($this->request_time_to) $page_args['toTime'] = date('Y-m-d H:i', $this->request_time_to);
        $page_args['page'] = '{page}';
        return $page_args;
    }

}