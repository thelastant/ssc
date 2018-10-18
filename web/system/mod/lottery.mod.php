<?php

/**
 * Email:##NONE
 * Date: 2017/3/2
 * Time: 16:32
 */
class mod_lottery extends mod
{

    public function __construct()
    {
        $this->user_check = false;
        parent::__construct();
    }

    //彩票
    public function category()
    {
        if ($this->post) {
            $this->display('lottery/category', array(
                'all_plays' => $this->get_plays(),
            ));
        } else {
            $this->ajax();
        }

    }

    public function get_last_data()
    {
        $type_id = intval($_REQUEST['id']);
        // 获取开奖历史: 获取20期
        $sql = "SELECT `time`,`number`,`data` FROM `{$this->db_prefix}data` WHERE `type`={$type_id} ORDER BY `id` DESC LIMIT 16";
        $history = $this->db->query($sql, 3);
        $this->api_return("suc", 200, $history);
    }
}