<div id="bet-log-dom" class="common">
    <div class="head">
        <div class="name icon-th-list">投注记录</div>
        <form action="/bet/log_search" class="search" data-ispage="true" container="#bet-log-dom .body" target="ajax"
              func="form_submit">
            <select name="type">
                <option>选择彩种</option>
                <?php foreach ($_types as $n => $vs) { ?>
                    <option disabled><?php echo $n; ?></option>
                    <?php foreach ($vs as $v) { ?>
                        <option value="<?php echo $v['id']; ?>"<?php if ($args['type'] == $v['id']) echo ' selected'; ?>>
                            &nbsp;&nbsp;&nbsp;|--<?php echo $v['title']; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <div class="select-box">
                <select name="state" class="cs-select">
                    <?php foreach ($state as $k => $v) { ?>
                        <option value="<?php echo $k; ?>"<?php if ($k == $args['state']) echo ' selected'; ?>><?php echo $v; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-brown icon-search">查询</button>
        </form>
    </div>
    <div class="body"><?php require(TPL . '/bet/log_body.tpl.php'); ?></div>
</div>
<script type="text/javascript">
    seajs.use(["functions", "commonJs"], function () {
        var func = seajs.require("functions");

        $('#home').removeClass('on');
        $('#bet-log').addClass('on');
        document.title = "投注日志";
        window.setMuiTitle("投注日志");
        // 绑定撤单事件
        $(document).on('click', '#bet-log-dom .remove_single', window.beter.remove_single);

        // 菜单下拉固定
        $.scroll_fixed('#bet-log-dom .head');

        // 时间选择插件
        $('#datetimepicker_fromTime,#datetimepicker_toTime').datetimepicker(datetimepicker_opt);
    });

</script>