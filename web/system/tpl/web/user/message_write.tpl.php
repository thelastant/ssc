<div id="message-receive-dom" class="common container" style="margin-top: 20px;">
    <div class="head">
        <div class="name icon-mail-alt">私信</div>
        <div class="tab">
            <a href="/user/message_receive">收件箱</a>
            <a href="/user/message_send">发件箱</a>
            <span>编写私信</span>
        </div>
    </div>
    <div class="body">
        <form action="/user/message_write_submit" class="search" method="post" target="ajax-form">
            <div class="form-group">
                <label>收件人：</label>
                <div style="height:40px;line-height:40px">
                    <?php if ($uid >= 0) { ?>
                        <input type="hidden" name="touser" value="<?php echo $uid; ?>">
                        <?php echo $username; ?>
                    <?php } else { ?>
                        <?php if ($this->user['parentId']) { ?>
                            <label><input name="touser" value="parent" checked="checked" type="radio">上级代理</label>
                            <label><input name="touser" value="children" type="radio">直属下级会员</label>
                        <?php } else { ?>
                            <label><input name="touser" value="children" checked="checked" type="radio">直属下级会员</label>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group">
                <label>主&nbsp;&nbsp;&nbsp;题：</label>
                <input class="form-control" name="title" required placeholder="请输入私信主题" style="width: 50%">
            </div>
            <div class="form-group">
                <label>内&nbsp;&nbsp;&nbsp;容：</label>
                <textarea class="form-control" name="content" required
                          style="width:700px;border:1px solid #ddd;color:#666;padding:10px;height:200px"
                          placeholder="请输入私信内容"></textarea>
            </div>
            <div class="form-group" style="width: 50%;margin-top: 20px">
                <button type="submit" class="btn btn-primary icon-ok btn-block">
                    发送
                </button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('#message-receive').addClass('on');
        seajs.use([], function () {
            $(document).on('submit', "form[target='ajax-form']", function (e) {
                e.preventDefault();
                var $this = $(this);
                var data = $this.serialize();
                var url = $this.attr("action");
                var ref = $this.attr("ref");
                $.post(url, data, function (ret) {
                    layer.open({content: ret.msg});
                    if (ret.code === 200) {
                        window.location.reload();
                    }
                });
            });
        });

    });
</script>