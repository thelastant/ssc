<div id="message-send-dom" class="common container" style="margin-top: 20px;">
    <div class="head">
        <div class="name icon-mail-alt">私信</div>
        <form action="/user/message_send" class="search" method="get">
            <div class="select-box mode">
                <select name="state" class="cs-select state">
                    <option value="0"<?php if ($state === 0) echo ' selected'; ?>>所有</option>
                    <option value="1"<?php if ($state === 1) echo ' selected'; ?>>未读</option>
                    <option value="2"<?php if ($state === 2) echo ' selected'; ?>>已读</option>
                </select>
            </div>
            <div class="timer">
                <input type="text" autocomplete="off" name="fromTime"
                       value="<?php echo date('Y-m-d H:i', $this->request_time_from); ?>" id="datetimepicker_fromTime"
                       class="timer">
                <span class="icon icon-calendar"></span>
            </div>
            <div class="sep icon-exchange"></div>
            <div class="timer">
                <input type="text" autocomplete="off" name="toTime"
                       value="<?php echo date('Y-m-d H:i', $this->request_time_to); ?>" id="datetimepicker_toTime"
                       class="timer">
                <span class="icon icon-calendar"></span>
            </div>
            <button type="submit" class="btn btn-brown ">查询</button>
        </form>
        <div class="tab">
            <a href="/user/message_receive">收件箱</a>
            <span>发件箱</span>
            <a href="/user/message_write">编写私信</a>
        </div>
    </div>
    <div class="body"><?php require(TPL . '/user/message_send_body.tpl.php'); ?></div>
</div>
<script type="text/javascript">
    $(function () {
        // 其他选择
        $('#message-send-dom select.cs-select').each(function () {
            new SelectFx(this);
        });
        // 菜单下拉固定
        $.scroll_fixed('#message-send-dom .head');
        // 时间选择插件
        $('#datetimepicker_fromTime,#datetimepicker_toTime').datetimepicker(datetimepicker_opt);
    });
</script>