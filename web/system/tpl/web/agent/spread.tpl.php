<div id="agent-spread-dom" class="common container">
    <div class="head">
        <div class="name icon-link-ext">推广链接</div>
        <a href="javascript:;" class="link_add btn btn-brown icon-plus">添加推广链接</a>
    </div>
    <form class="link_add_box hide"
          method="post" action="/agent/api_spread_link_add" target="ajax-form">
        <div class="item">
            <div class="name">推广类型</div>
            <div class="value type">
                <label><input type="radio" name="type" value="1" title="代理" checked="checked">代理</label>
                <label><input name="type" type="radio" value="0" title="会员">会员</label>
            </div>
        </div>
        <div class="item">
            <div class="name">用户返点</div>
            <div class="value fandian">
                <input type="text" name="fanDian" class="form-control" required title="用户返点" placeholder="用户注册后投注返点最大值">
            </div>
            <div class="addon name">%，设置上限：<?php echo $max; ?></div>
        </div>
        <button type="submit" class="btn btn-blue icon-ok">确认添加</button>
    </form>
    <div class="body"><?php require(TPL . '/agent/spread_body.tpl.php'); ?></div>
</div>
<script type="text/javascript">
    $(function () {

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
                        setTimeout(function () {
                            window.location.reload();
                        }, 1300);
                    }
                });
            });
            $(document).on('click', ".ajax-bet-info", function (e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $.post(url, {}, function (ret) {
                    layer.open({content: ret.msg});
                    setTimeout(function () {
                        window.location.reload();
                    }, 1300);
                })
            });
        });

        // 添加推广链接
        $('#agent-spread-dom .link_add').bind('click', function () {
            $('#agent-spread-dom .link_add_box').toggleClass("hide");
        });
        // 菜单下拉固定
        $.scroll_fixed('#agent-spread-dom .head');
    });
</script>