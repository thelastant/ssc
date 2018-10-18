<div id="message-send-dom" class="common container" style="margin-top: 20px;">
    <div class="head">
        <div class="name icon-mail-alt">私信</div>
        <div class="tab">
            <span href="/user/message_receive">收件箱</span>
            <a href="/user/message_send">发件箱</a>
            <a href="/user/message_write">编写私信</a>
        </div>
    </div>
    <div class="body">
        <h5><?php echo $article['content']; ?></h5>


    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('#message-receive').addClass('on');
    });
</script>