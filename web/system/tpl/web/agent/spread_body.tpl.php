<?php
if ($data) {
    ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr class="title">
            <td title="用于区分不同的推广链接">链接编号</td>
            <td title="通过推广注册的用户类型">推广类型</td>
            <td title="用户注册后投注返点最大值">用户返点</td>
            <td title="通过该推广链接注册的人数">注册人次</td>
            <td title="该推广链接最后一次被使用的时间">更新时间</td>
            <td>相关操作</td>
        </tr>
        <?php foreach ($data as $v) { ?>
            <tr id="spread-<?php echo $v['lid']; ?>">
                <td><?php echo $v['lid']; ?></td>
                <td><?php echo $v['type'] ? '代理' : '会员'; ?></td>
                <td><?php echo $v['fanDian']; ?>%</td>
                <td><?php echo $v['usedTimes']; ?></td>
                <td><?php echo $v['updateTime'] ? date('Y-m-d H:i', $v['updateTime']) : '--'; ?></td>
                <td>
                    <a href="javascript:;"
                       data-link="http://<?php echo $_SERVER['SERVER_NAME']; ?>/user/reg?pid=<?php echo $v['lid']; ?>"
                       class="link_get icon-link-ext-alt">获取链接</a>
                    <a href="/agent/spread_link_remove?lid=<?php echo $v['lid']; ?>" class="icon-trash-2 ajax-bet-info">删除</a>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } else { ?>
    <div class="empty"></div>
<?php } ?>
<?php require(TPL . '/page.tpl.php'); ?>
<script type="text/javascript">
    seajs.use([], function () {
        $(".link_get").on("click", function () {
            var link = $(this).attr("data-link");
            layer.open({
                content: "推广链接:" + link,
            });
        })
    });
</script>