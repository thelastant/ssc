<input type="hidden" name="playedGroup" value="<?php echo $group_id; ?>"/>
<input type="hidden" name="playedId" value="<?php echo $play_id; ?>"/>
<input type="hidden" name="type" value="<?php echo $type_id; ?>"/>
<div class="unique">
    <?php foreach (array('第一', '第二', '第三') as $var) { ?>
        <div class="pp pp11" action="tz5xDwei" length="1" delimiter=" ">
            <div class="title"><?= $var ?>位</div>
            <div class="circle-num-box">
                <div class="ey-num-box">
                    <input type="button" value="01" class="code d min"/>
                    <input type="button" value="02" class="code s min"/>
                    <input type="button" value="03" class="code d min"/>
                    <input type="button" value="04" class="code s min"/>
                    <input type="button" value="05" class="code d min"/>
                    <input type="button" value="06" class="code s max"/>
                    <input type="button" value="07" class="code d max"/>
                    <input type="button" value="08" class="code s max"/>
                    <input type="button" value="09" class="code d max"/>
                    <input type="button" value="10" class="code s max"/>
                    <input type="button" value="11" class="code d max"/>

                </div>
                <div class="ey-select-box">
                    <input type="button" value="清" class="action none"/>
                    <input type="button" value="双" class="action even"/>
                    <input type="button" value="单" class="action odd"/>
                    <input type="button" value="小" class="action small"/>
                    <input type="button" value="大" class="action large"/>
                    <input type="button" value="全" class="action all"/>
                </div>
            </div>
        </div>
        <?php
    }
    $maxPl = $this->get_play_bonus($play_id);
    ?>
</div>

<script type="text/javascript">
    $(function () {
        lottery.set_play_Pl(<?php echo json_encode($maxPl);?>);
    })
</script>