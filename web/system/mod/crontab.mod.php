<?php

//计划任务,比如定期删除数据，日结算工资等
const TOKEN = "test";

class mod_crontab extends mod
{

    public function __construct()
    {
        $this->user_check = false;
        parent::__construct();
    }

    private function _auth()
    {
        $token = $this->request->request("token");
        if ($token != TOKEN) {
            exit("AUTH FAIL");
        }
    }

    public function remove_type_order()
    {
        $type_id = $this->request->request("type_id");
        $action_no = $this->request->request("action_no");

    }


    /**
     * 日结工资
     */
    public function task_day_money()
    {
        echo "开始结算日工资\n";
        //checkerTimer
        $startTime = strtotime(date("Y-m-d 02:00:00"));
        $endTime = strtotime(date("Y-m-d 02:05:00"));
        //判断今日日结工资是否发放
        $timeNow = time();
        if ($startTime > $timeNow || $timeNow > $endTime) {
            exit("TIME IS ERR\n");
        }
        //发日结工资
        $this->DBC->pdo->beginTransaction();
        //日结工资： 0:00-23:59的 团队投注总额*百分比

        //遍历所有代理用户
        $lastDate = date("Y-m-d", strtotime(date("Y-m-d") . " -1day"));
        $timeToday = strtotime(date("Y-m-d"));

        $usersAgents = $this->DBC->select("members", ["uid", 'username', 'day_rate'], ['isDelete' => 0, 'type' => 1,]);
        foreach ($usersAgents as $agent) {
            //查询是否已经发放了日工资
            $had = $this->DBC->get("day_logs", "id", ['date' => $lastDate, 'status' => 1, 'uid' => $agent['uid']]);
            if ($had) {
                continue;
            }
            //发放日工资
            $money = 0;
            //1.查询自己得投注额度
            $total = $this->getTeamUsed($lastDate, $timeToday, $agent['uid']);
            //如果是负数，就发放日工资
            if ($total <= 0) {
                $money += ($total * $agent['day_rate'] / 100);
            }
            $money = abs($money);
            ##减去下级分红的金额
            $zsChilds = $this->_getChildren("uid,type,day_rate", 1, $agent['uid']);

            foreach ($zsChilds as $child) {
                if ($child['type'] != 1) {
                    continue;
                }
                $tmpTotal = $this->getTeamUsed($lastDate, $timeToday, $child['uid']);
                //如果是负数，就发放日工资
                if ($tmpTotal <= 0) {
                    //减去下级用户的发放日工资
                    $money -= abs($tmpTotal * $child['day_rate'] / 100);
                }
            }

            //发放日工资
            $this->set_coin(array(
                'uid' => $agent['uid'],
                'type' => 0,
                'liqType' => 167,
                'info' => '日结工资',
                'extfield0' => 0,
                'extfield1' => 0,
                'coin' => abs($money),
            ));

            //添加日结工资日志
            $this->DBC->insert("day_logs", [
                'uid' => $agent['uid'],
                'start_time' => strtotime($lastDate),
                'date' => $lastDate,
                'end_time' => $timeToday - 1,
                'day_rate' => $agent['day_rate'],
                'team_coins' => abs($total),
                'coin' => abs($money),
                'status' => 1,
                'username' => $agent['username'],
            ]);

            if (!$this->DBC->id()) {
                $this->DBC->pdo->rollback();
                echo "FAIL";
                return false;
            } else {
                echo "UID:{$this->DBC->id()}发放成功,￥{$money}\n";
            }
        }
        $this->DBC->pdo->commit();
        echo "SUCCESS\n";
        return true;
    }

    //获取团队的消费
    private function getTeamUsed($lastDate, $timeToday, $uid)
    {
        //1.查询自己得投注额度
        $children = $this->_getChildren("uid,username", 0, $uid);
        $uids = array();
        foreach ($children as $child) {
            array_push($uids, $child['uid']);
        }
        //把自己加进去
        array_push($uids, $uid);
        return $total = $this->DBC->sum("coin_log", 'coin', ['uid' => $uids, 'liqType' => [5, 7, 101, 102, 255, 108], 'actionTime[<>]' => [strtotime($lastDate), $timeToday]]);
    }
}