<input type="hidden" name="playedGroup" value="<?php echo $group_id;?>" />
<input type="hidden" name="playedId" value="<?php echo $play_id;?>" />
<input type="hidden" name="type" value="<?php echo $type_id;?>" />
<?php
foreach (array('选') as $var) { ?>
    <div class="pp" action="lhctmdx" length="1" random="sscRandom">
        <div class="title"><?= $var ?>择</div>
        <input type="button" value="单" class="code"/>
        <input type="button" value="双" class="code"/>
    </div>
    <?php
}
$maxPl = $this->get_play_bonus($play_id);
?>
<script type="text/javascript">
    $(function () {
        lottery.set_play_Pl(<?=json_encode($maxPl)?>, false,<?=$this->user['fanDianBdw']?>);
    })
</script>
