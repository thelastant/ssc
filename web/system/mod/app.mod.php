<?php

class mod_app extends mod
{

    public function download()
    {

        $baseUrl = "{$_SERVER["SERVER_NAME"]}:{$_SERVER['SERVER_PORT']}/";
        $url = array(
            "android" => $baseUrl . "downloads/app.apk",
            "ios" => $baseUrl . "downloads/app.ipa",
            'wap' => $baseUrl . "?client_type=mobile",
        );
        $this->smarty->assign("urls", $url);
        $this->smarty->display("app/download.tpl");
    }
}