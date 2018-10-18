<?php
if ($lottery) {
    $_lottery = $lottery;
} else {
    $_lottery = array();
    for ($i = 0; $i < 100; $i++) array_push($_lottery, 0);
}
?>
<div class="container">
    <div class="data row">
        <div class="col-xs-1 game-log-box">
            <div class="row">
                <img class="game-logo-pic"
                     src="<?php echo THEME_PATH; ?>images/pic_imgs/<?php echo $type_this['pic_img']; ?>"
                     alt="">
            </div>
        </div>
        <div class="col-xs-1 kj-time-show">
            <div class="open">
                <div class="">
                    <p>第<span id="last_action_no"><?php echo $current['actionNo']; ?></span> 期</p>
                    <p><span>剩余投注时间</span></p>
                </div>
            </div>
        </div>
        <div class="col-xs-3 last-open-timer">
            <div class="open">
                <div id="timer_lottery">
                    <div class="h">
                        <span class="number">0</span>
                        <span class="number">0</span>
                    </div>
                    <div class="sep"></div>
                    <div class="m">
                        <span class="number">0</span>
                        <span class="number">0</span>
                    </div>
                    <div class="sep"></div>
                    <div class="s">
                        <span class="number">0</span>
                        <span class="number">0</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-1 last-open-text">
            <div class="open">
                <div class="name">
                    <p>第<span id="lottery-current-text"> <?php echo $last['actionNo']; ?></span>期</p>
                    <p><span class="val">正在开奖</span></p>
                </div>
            </div>
        </div>
        <div class="col-xs-4 last-open-codes">
            <div class="open">

                <?php if ($types[$type_id]['type'] == 4) { //快乐十分?>
                    <div class="num_right_new kj-hao" ctype="kl10">
                        <em class="num_red_b_new ball_01"> <?php echo $_lottery[0]; ?> </em>
                        <em class="num_red_b_new ball_02"> <?php echo $_lottery[1]; ?> </em>
                        <em class="num_red_b_new ball_03"> <?php echo $_lottery[2]; ?> </em>
                        <em class="num_red_b_new ball_04"> <?php echo $_lottery[3]; ?> </em>
                        <br>
                        <em class="num_red_b_new ball_06"> <?php echo $_lottery[4]; ?> </em>
                        <em class="num_red_b_new ball_07"> <?php echo $_lottery[5]; ?> </em>
                        <em class="num_red_b_new ball_08"> <?php echo $_lottery[6]; ?> </em>
                        <em class="num_red_b_new ball_09"> <?php echo $_lottery[7]; ?> </em>
                    </div>
                <?php } else if ($types[$type_id]['type'] == 6) { //PK10?>
                    <div class="num_right_pk10 kj-hao" ctype="pk10">
                        <em class="num_red_b_pk10 ball_01"> <?php echo $_lottery[0]; ?> </em>
                        <em class="num_red_b_pk10 ball_02"> <?php echo $_lottery[1]; ?> </em>
                        <em class="num_red_b_pk10 ball_03"> <?php echo $_lottery[2]; ?> </em>
                        <em class="num_red_b_pk10 ball_04"> <?php echo $_lottery[3]; ?> </em>
                        <em class="num_red_b_pk10 ball_05"> <?php echo $_lottery[4]; ?> </em>
                        <br>
                        <em class="num_red_b_pk10 ball_06"> <?php echo $_lottery[5]; ?> </em>
                        <em class="num_red_b_pk10 ball_07"> <?php echo $_lottery[6]; ?> </em>
                        <em class="num_red_b_pk10 ball_08"> <?php echo $_lottery[7]; ?> </em>
                        <em class="num_red_b_pk10 ball_09"> <?php echo $_lottery[8]; ?> </em>
                        <em class="num_red_b_pk10 ball_010"> <?php echo $_lottery[9]; ?> </em>
                    </div>
                <?php } else if ($types[$type_id]['type'] == 8) { //快8
                    /*$lotteryS=explode("|",$_lottery[19]);
                    $_lottery[19]=$lotteryS[0];
                    $feipan=$lotteryS[1];*/
                    ?>
                    <div class="num_right_kl kj-hao" ctype="g1" cnum="80">
                        <em id="span_lot_0" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[0]; ?> </em>
                        <em id="span_lot_1" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[1]; ?> </em>
                        <em id="span_lot_2" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[2]; ?> </em>
                        <em id="span_lot_3" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[3]; ?> </em>
                        <em id="span_lot_4" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[4]; ?> </em>
                        <em id="span_lot_5" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[5]; ?> </em>
                        <em id="span_lot_6" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[6]; ?> </em>
                        <em id="span_lot_7" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[7]; ?> </em>
                        <em id="span_lot_8" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[8]; ?> </em>
                        <em id="span_lot_9" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[9]; ?> </em>
                        <em id="span_lot_10" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[10]; ?> </em>
                        <em id="span_lot_11" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[11]; ?> </em>
                        <em id="span_lot_12" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[12]; ?> </em>
                        <em id="span_lot_13" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[13]; ?> </em>
                        <em id="span_lot_14" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[14]; ?> </em>
                        <em id="span_lot_15" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[15]; ?> </em>
                        <em id="span_lot_16" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[16]; ?> </em>
                        <em id="span_lot_17" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[17]; ?> </em>
                        <em id="span_lot_18" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[18]; ?> </em>
                        <em id="span_lot_19" class="num_red_b_kl gr_s gr_s020"> <?php echo $_lottery[19]; ?> </em>
                    </div>
                <?php } else if ($types[$type_id]['type'] == 9) { //快3?>
                    <div class="num_right_k3 kj-hao" ctype="k3">
                        <em class="num_red_b_k3  ball_0"><?php echo intval($_lottery[0]); ?> </em>
                        <em class="num_red_b_k3  ball_1"><?php echo intval($_lottery[1]); ?> </em>
                        <em class="num_red_b_k3  ball_2"><?php echo intval($_lottery[2]); ?> </em>
                    </div>
                <?php } else if ($types[$type_id]['type'] == 3) { //3D?>
                    <div class="num_right_3d  kj-hao" ctype="3d">
                        <em class="num_red_b ball_0"><?php echo intval($_lottery[0]); ?> </em>
                        <em class="num_red_b ball_1"><?php echo intval($_lottery[1]); ?> </em>
                        <em class="num_red_b ball_2"><?php echo intval($_lottery[2]); ?> </em>
                    </div>
                <?php } else if ($types[$type_id]['type'] == 2) { //11选5?>
                    <div class="num_right  kj-hao" ctype="11x5">
                        <em class="num_red_b ball_0"><?php echo intval($_lottery[0]); ?> </em>
                        <em class="num_red_b ball_1"><?php echo intval($_lottery[1]); ?> </em>
                        <em class="num_red_b ball_2"><?php echo intval($_lottery[2]); ?> </em>
                        <em class="num_red_b ball_3"><?php echo intval($_lottery[3]); ?> </em>
                        <em class="num_red_b ball_4"><?php echo intval($_lottery[4]); ?> </em>
                    </div>
                <?php } else if ($types[$type_id]['type'] == 10) { //香港六合彩?>
					<div class="num_right_pk10 kj-hao" ctype="xglhc">
                        <em class="num_red_b_pk10 ball_01"> <?php echo $_lottery[0]; ?> </em>
                        <em class="num_red_b_pk10 ball_02"> <?php echo $_lottery[1]; ?> </em>
                        <em class="num_red_b_pk10 ball_03"> <?php echo $_lottery[2]; ?> </em>
                        <em class="num_red_b_pk10 ball_04"> <?php echo $_lottery[3]; ?> </em>
                        <em class="num_red_b_pk10 ball_05"> <?php echo $_lottery[4]; ?> </em>
                        <em class="num_red_b_pk10 ball_06"> <?php echo $_lottery[5]; ?> </em>
						<br>
                        <em class="num_red_b_pk10 ball_07"> <?php echo $_lottery[6]; ?> </em>
                    </div>
                <?php } else { ?>
                    <div class="num_right kj-hao" ctype="ssc0<?=$types[$type_id]['type']?>">
                        <em class="num_red_b ball_0"><?php echo intval($_lottery[0]); ?></em>
                        <em class="num_red_b ball_1"><?php echo intval($_lottery[1]); ?></em>
                        <em class="num_red_b ball_2"><?php echo intval($_lottery[2]); ?></em>
                        <em class="num_red_b ball_3"><?php echo intval($_lottery[3]); ?></em>
                        <em class="num_red_b ball_4"><?php echo intval($_lottery[4]); ?></em>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="col-xs-1">
            <div class="action-voi-type-box">
                <a href="javascript:;" class="gray" onclick="voice.switcher()">
                    <span id="voice" class="icon-volume-down">关闭声音</span>
                </a>
                <a href="javascript:;" class="gray icon-shareable quick-clear-order" style="display:none">快速撤单</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    seajs.use([], function () {

        //快速撤单
        $(".quick-clear-order").bind('click', function (ret) {
            if (confirm("确定撤销所有订单么？")) {
                $.post("/bet/clear_order", {type_id: game.type}, function (ret) {
                    layer.open({content: ret.msg});
                    //重新刷新窗体
                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }, "JSON");
            }
        });

        if (typeof HISTORY !== undefined) {
            HISTORY.getHistory();
        }
        window.S = <?php echo json_encode($diffTime > 0);?>;
        window.KS = <?php echo json_encode($kjDiffTime > 0);?>;
        window.kjTime = parseInt(<?php echo json_encode($kjdTime);?>);
        if (lottery.timer.T) clearTimeout(lottery.timer.T);
        if (lottery.timer.KT) clearTimeout(lottery.timer.KT);
        if (lottery.timer.moveno) clearInterval(lottery.timer.moveno);
        lottery.timer.T = setTimeout(function () {
            lottery.countdown(<?php echo $diffTime;?>);
        }, 1000);
        <?php if($kjDiffTime > 0){?>
        lottery.timer.KT = setTimeout(function () {
            lottery.waiting(<?php echo $kjDiffTime;?>);
        }, 1000);
        <?php }?>
        <?php if($lottery){?>
        voice.play('/static/sound/open.mp3', 'voice-lottery');
        lottery.get_loss_gain(game.type, '<?php echo $last['actionNo'];?>');
        if (lottery.switcher.bets_fresh) lottery.bets_fresh();
        <?php } else {?>
        lottery.load_last_data();
        <?php }?>
    });
</script>
