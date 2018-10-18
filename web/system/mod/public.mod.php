<?php

//公共
class mod_public extends mod
{

    public function notice()
    {
        $notices = $this->DBC->select("content", "*", ['enable' => 1, "ORDER" => [
            "addTime" => "DESC",
        ]]);
        $this->smarty->assign("notice_list", $notices);
        $this->smarty->display("public/notice.tpl");
    }

    public function api_get_notice_detail()
    {

    }


}