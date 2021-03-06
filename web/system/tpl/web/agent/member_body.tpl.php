<?php
if ($data) {
    ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="title">
            <td>用户名</td>
            <td>用户类型</td>
            <td>返点</td>
            <td>工资%</td>
            <td>余额</td>
            <td>团队余额</td>
            <td>状态</td>
            <td>在线</td>
            <td>注册时间</td>
            <td>操作</td>
        </tr>
        <?php
        foreach ($data as $v) {
            $this_uid = $v['uid'];
            $login = $this->db->query("SELECT * FROM `{$this->db_prefix}member_session` WHERE `uid`=$this_uid ORDER BY `id` DESC LIMIT 1", 2);
            $online = (
                $login &&
                $login['isOnLine'] &&
                ceil(time() - $login['accessTime']) < MEMBER_SESSIONTIME
            ) ? true : false;
            ?>
            <tr>
                <td><?php echo $v['username'];
                    if ($this_uid == $this->user['uid']) echo '<span class="red" style="margin-left:3px">(自己)</span>'; ?></td>
                <td><?php echo $v['type'] ? '代理' : '会员'; ?></td>
                <td><?php echo $v['fanDian']; ?>%</td>
                <td><?php echo $v['day_rate']; ?>%</td>
                <td><?php echo $v['coin']; ?></td>
                <td><?php echo $this->get_team_coin($v['uid']); ?></td>
                <td><?php echo $v['enable'] ? '正常' : '冻结'; ?></td>
                <td><?php echo $online ? '<span class="green">在线</span>' : '<span class="gray">离线</span>'; ?></td>
                <td><?php echo date('Y-m-d', $v['regTime']); ?></td>
                <td style="text-align:left;padding:10px 15px">
                    <?php
                    if ($v['uid'] == $this->user['uid']) {
                        $url_member = '/agent/member?type=1';
                        $url_log = '/bet/log';
                        $url_money = '/user/money';
                        $url_coin = '/user/coin';
                        $url_mail = '';
                        $url_edit = '';
                    } else {
                        $url_member = '/agent/member?type=1&uid=' . $v['uid'];
                        $url_log = '/agent/log?username=' . $v['username'];
                        $url_money = '/agent/money?username=' . $v['username'];
                        $url_coin = '/agent/coin?username=' . $v['username'];
                        $url_mail = '/user/message_write?uid=' . $v['uid'];
                        $url_edit = '/agent/user_edit?uid=' . $v['uid'];
                    }
                    ?>
                    <a class="icon-download" href="<?php echo $url_member; ?>" container="#agent-member-dom .body"
                       data-ispage="true">下级</a>
                    <a class="icon-sweden" href="<?php echo $url_log; ?>">投注</a>
                    <a class="icon-yen" href="<?php echo $url_money; ?>">盈亏</a>
                    <a class="icon-chart-bar" href="<?php echo $url_coin; ?>">帐变</a>
                    <div<?php if ($v['uid'] != $this->user['uid']) echo ' style="margin-top:5px"'; ?>>
                        <?php if ($url_mail) { ?>
                            <a class="icon-mail" href="<?php echo $url_mail; ?>">私信</a>
                        <?php } ?>
                        <?php
                        if ($this->user['uid'] != $v['uid'] && $v['parentId'] == $this->user['uid'] && $this->config['recharge'] && $this->user['xq_zz']==1) {
                            echo '<a class="icon-exchange ajax-href-form" href="/agent/recharge?uid=' . $v['uid'] . '" >转账</a>';
                        }
                        ?>
                        <?php if ($url_edit) { ?>
                            <a class="icon-pencil-squared ajax-href-form" href="<?php echo $url_edit; ?>">改返点</a>
                            <a class="icon-pencil-squared ajax-href-form"
                               href="/agent/edit_rate_day?uid=<?php echo $v['uid'] ?>">改工资比</a>
                            <a class="icon-sweden qy-qd-setting" data-id="<?php echo $v['uid']; ?>"
                               href="/agent/qy_set?uid=<?php echo $v['uid']; ?>">契约</a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } else { ?>
    <div class="empty"></div>
<?php } ?>
<?php require(TPL . '/page.tpl.php'); ?>