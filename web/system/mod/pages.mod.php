<?php

/**
 * Email:##NONE
 * Date: 2017/3/10
 * Time: 16:47
 */
class mod_pages extends mod
{
    public function __construct()
    {
        parent::__construct();
        $this->client_type = "mobile";
    }

    //mobile
    public function show_page()
    {
        $tpl = $this->request->request("page");
        //$tpl = strval($_REQUEST['page']);
        $this->display("pages/{$tpl}");
    }

    public function notice()
    {
        if ($this->post) {
            if (!array_key_exists('type', $_GET)) $_GET['type'] = 'list';
            if ($_GET['type'] === 'list') {
                $this->notice_list();
            } else if ($_GET['type'] === 'content') {
                $this->notice_content();
            } else {
                core::__403();
            }
        } else {
            $this->ajax();
        }
    }

}