<style>
    #dom_body {
        background: url("<?php echo THEME_PATH?>images/index/skin_bg/skin-img04.jpg") no-repeat;
        background-size: 100% 100%;
    }

    .container-fluid {
        min-width: 1280px;
        max-width: 1280px;
    }
</style>
<style>
    /*#c_top_leftban {*/
    /*position: absolute;*/
    /*background: #fff;*/
    /*z-index: 1001;*/
    /*left: 0px;*/
    /*top: 0%;*/
    /*}*/
    #c_top_leftban {
        position: relative;
        background: #fff;
    }

    .open-data-num {
        margin: 2px;
        height: 25px;
        width: 25px;
        border-radius: 100%;
        padding: 5px;
        background: #7d2002;
        color: #fff;
        line-height: 15px;
        display: inline-block;
        text-align: center;
    }

    .history-box .title {
        line-height: 25px;
        margin: 2px;
    }

    #dom_body {
        /*background: #467bc7;*/
    }
</style>
<div class="container-fluid" style="min-width: 1370px;">
    <div class="">
        <div class="row" id="game-lottery" type="<?php echo $type_id; ?>"
             ctype="<?php echo $types[$type_id]['type'] ?>">
            <div class="lottery-container">
                <div class="loading">开奖数据加载中</div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-9" style="padding-right: 0px;">
                <div id="game-play" style="border-radius: 10px;">
                    <div class="group">
                        <div class="name icon-th-large">玩法分类</div>
                        <ul class="list" id="group_list">
                            <?php
                            foreach ($groups as $gid => $group) {
                                $class = $gid == $group_id ? ' class="on"' : '';
                                ?>
                                <li><a data-id="<?php echo $group['id']; ?>"
                                       href="javascript:;"<?php echo $class; ?>><?php echo $group['groupName']; ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="play">
                        <?php require(TPL . '/game/play_index.tpl.php'); ?>
                    </div>
                    <div class="play-work row" style="margin: 15px 0;">
                        <div id="play-work-setting" class="col-xs-12">
                            <div style="display: none" id="fandian-value"
                                 data-bet-count="<?php echo $this->config['betMaxCount']; ?>"
                                 data-bet-zj-amount="<?php echo $this->config['betMaxZjAmount']; ?>"
                                 max="<?php echo $this->user['fanDian']; ?>"
                                 data-user-fd="<?php echo $this->user['fanDian']; ?>"
                                 game-fan-dian="<?php echo $this->config['fanDianMax']; ?>"
                                 fan-dian="<?php echo $this->user['fanDian']; ?>"
                                 game-fan-dian-bdw="<?php echo $this->config['fanDianBdwMax']; ?>"
                                 fan-dian-bdw="<?php echo $this->user['fanDianBdw']; ?>" class="left">

                            </div>
                            <style>
                                .slider-bar-fd {
                                    width: 200px;
                                    margin-top: -5px;
                                    float: left;
                                    overflow: hidden;
                                    display: block;
                                    background: #fff;
                                    padding: 7px;
                                }

                                .nstSlider {
                                    margin-top: 5px;
                                    width: 190px;
                                    color: #000000;
                                    background: #9f0000;
                                    height: 10px;
                                }

                                .right_jjz {
                                    margin-right: 10px;
                                }

                                .nstSlider .leftGrip {
                                    background: #fff;
                                    top: -5px;
                                    border: 1px solid #ddd;
                                    border-radius: 0;
                                }
                            </style>

                            <div style="display: " class="slider-bar-fd" data-select-fd="0.0">

                                <div class="" style="width: 200px">
                                    <div class="left_fd_rate text-left pull-left">
                                        返点：<span class="left_fd_label" style="font-size: 13px;color: #9f0000;font-weight: bold;"></span>
                                    </div>
                                    <div class="right_jjz text-right">
                                        奖金：<span class="jiangjin_value right_jjz_label" style="font-size: 13px;color: #9f0000;font-weight: bold;"></span>
                                    </div>
                                </div>
                                <div class="nstSlider" data-range_min="0" data-range_max="131" data-cur_min="0">
                                    <div class="leftGrip"></div>
                                </div>
                            </div>

                            <script>
                                seajs.use(["sliderCss"], function () {
                                    var baseJJZ = 1900;
                                    //计算最大奖金组

                                    var maxJJZ = parseInt("<?php echo ($this->user['fanDian'] / 0.1 * 2) + 1900; ?>");
                                    $('.nstSlider').nstSlider({
                                        "left_grip_selector": ".leftGrip",
                                        "value_changed_callback": function (cause, leftValue, rightValue) {
                                            //计算奖金组
                                            var fanDTmp = parseFloat(leftValue / 10).toFixed(1);
                                            $(this).parent().find('.left_fd_label').text(leftValue / 10 + "%");
                                            $(this).parent().find('.right_jjz_label').text(maxJJZ - 2 * leftValue);
                                            //计算奖金
                                            $('.play-work').find('.jiangjin_value').text(window.fandianSelArr[fanDTmp].bonus);
                                            $(".slider-bar-fd").attr('data-select-fd', fanDTmp);
                                        }
                                    });
                                });
                            </script>

                            <div id="play-mod">
                                <span class="name icon-gauge">模式：</span>
                                <?php
                                $mods = array(
                                    array(
                                        'switch' => $this->config['yuanmosi'],
                                        'rebate' => $this->config['betModeMaxFanDian0'],
                                        'value' => '2.000',
                                        'name' => '元',
                                    ),
                                    array(
                                        'switch' => $this->config['jiaomosi'],
                                        'rebate' => $this->config['betModeMaxFanDian1'],
                                        'value' => '0.200',
                                        'name' => '角',
                                    ),
                                    array(
                                        'switch' => $this->config['fenmosi'],
                                        'rebate' => $this->config['betModeMaxFanDian2'],
                                        'value' => '0.020',
                                        'name' => '分',
                                    ),
                                    array(
                                        'switch' => $this->config['limosi'],
                                        'rebate' => $this->config['betModeMaxFanDian3'],
                                        'value' => '0.002',
                                        'name' => '厘',
                                    ),
                                );
                                $first = true;
                                foreach ($mods as $mod) {
                                    if ($mod['switch'] == 1) {
                                        if ($first) {
                                            $class = 'danwei trans on';
                                            $first = false;
                                        } else {
                                            $class = 'danwei trans';
                                        }
                                        echo '<b value="' . $mod['value'] . '" data-max-fan-dian="' . $mod['rebate'] . '" class="' . $class . '">' . $mod['name'] . '</b>';
                                    }
                                }
                                ?>
                            </div>
                            <div id="beishu-warp">
                                <span class="name icon-wrench">倍数：</span>
                                <i class="sur trans icon-minus"></i>
                                <input type="text" autocomplete="off" id="beishu-value"
                                       value="<?php echo (array_key_exists('beiShu', $_COOKIE) && is_numeric($_COOKIE['beiShu']) && $_COOKIE['beiShu'] > 0) ? intval($_COOKIE['beiShu']) : 1; ?>">
                                <i class="add trans icon-plus"></i>
                            </div>
                            <div class="opt">
                                <a href="javascript:lottery.game_add_code();"
                                   class="add btn btn-danger icon-pin">添加至购物篮</a>
                                <a href="javascript:lottery.game_remove_code();"
                                   style="background: #467bc7"
                                   class="del btn btn-primary icon-trash-1">清空购物篮</a>
                            </div>

                        </div>
                        <div id="play-work-data" class="col-xs-12">
                            <div id="bets-cart" class="col-xs-9">
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr class="head">
                                        <td>玩法</td>
                                        <td>投注号码</td>
                                        <td>投注数量</td>
                                        <td>投注金额</td>
                                        <td>投注倍数</td>
                                        <td>投注模式</td>
                                        <td>奖金 - 返点</td>
                                        <td>操作</td>
                                    </tr>
                                </table>
                            </div>
                            <div id="play-btn" class="col-xs-3" style="text-align: center">
                                <div class="row">
                                    <input type="hidden" id="zhuiHao" name="zhuiHao" value="0">
                                    <div class="col-xs-12 text-center">
                                        <div class="bet-info icon-chart-bar"><span id="all-count">0</span>注，<span
                                                    id="all-amount">0.00</span>元
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <a href="javascript:;" id="btnQuickPostBet"
                                           class="btn btn-success icon-sitemap">一键投注</a>
                                    </div>
                                    <div class="col-xs-12">
                                        <a href="javascript:;" id="btnPostBet"
                                           class="btn btn-danger icon-basket">确认投注</a>
                                    </div>
                                    <div class="col-xs-12">
                                        <a href="javascript:;" id="btnZhuiHao"
                                           class="btn btn-primary icon-magic">智能追号</a>
                                    </div>
                                    <div class="col-xs-12">
                                        <!--<div style="line-height: 30px;text-align:center;font-size: 18px">
                                            奖金：<span class="jiangjin_value text-danger"></span>
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="game-bets">
                    <div class="menu">
                        <a href="javascript:;" class="icon-flag-empty on">近期投注<span class="triangle"></span></a>
                        <a href="javascript:beter.remove_batch();" class="icon-trash hide"
                           id="bet-cancel">批量撤销选中投注</a>
                        <a href="/bet/log" class="more icon-flag">所有投注记录</a>
                    </div>
                    <div class="row">
                        <div id="my-bets">
                            <?php require(TPL . '/game/bets_recent.tpl.php'); ?>
                        </div>
                    </div>
                </div>
            </div>
			<div class="col-xs-3">
                <div id="c_top_leftban">
                    <div class="history-box">
                        <table width="100%" cellpadding="0" cellspacing="0" class="table table-hover table-bordered">
                            <tr class="head">
                                <th>最近期号</th>
                                <th>开奖号码</th>
                            </tr>
                            <tr v-for="it in history_list">
                                <td class="title"><span v-html="it.number"></span>期</td>
                                <td>
                                    <span class="open-code" v-for="(co,index) in it.data">
                                        <span v-html="co"></span>
                                        <br v-if="index===4"/>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .layermend::after, .layermend::before {
        color: #fff;
        background: #fff;
    }
</style>


<script type="text/javascript">
    var HISTORY;
    seajs.use(["layer", "layerCss", "vue"], function () {
        var vm = new Vue({
            el: "#c_top_leftban",
            data: {
                history_list: [],
            },
            methods: {
                getHistory: function () {
                    var _self = this;
                    _self.history_list = [];
                    $.post("/lottery/get_last_data", {id: parseInt("<?php echo $type_id; ?>")}, function (ret) {
                        if (ret.code === 200) {
                            ret.data.forEach(function (item) {
                                var data = item.data;
                                item.data = data.split(",");
                                _self.history_list.push(item);
                            })
                        }
                    });
                }
            },
            mounted: function () {
                this.getHistory();
            }
        });
        HISTORY = vm;
        $(document).on('click', ".ajax-bet-info", function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $.get(url, function (ret) {
                layer.open({content: ret.msg});
            })
        });
    });
    ~(function () {
        window.game = {
            type: <?php echo $type_id;?>,
            played: <?php echo $play_id;?>,
            groupId: <?php echo $group_id;?>,
            stop: <?php echo $this->config['switchBuy'] == 0 ? 'true' : 'false';?>,
            ban: <?php echo ($this->config['switchDLBuy'] == 0 && $this->user['type']) ? 'true' : 'false';?>,
        };
        lottery.switcher.bets_fresh = false;
        // 声音初始化
        voice.init();
        // 初始化左侧菜单
        $('#home').removeClass('on');
        var dom_nav = $('#game-nav .g .list li a[type-id=' + game.type + ']');
        if (!dom_nav.hasClass('on')) {
            dom_nav.addClass('on').parent().parent().slideDown(function () {
                $(this).prev().removeClass('icon-bookmark').addClass('icon-bookmark-empty');
            });
        }
        // 初始化历史选择模式
        var mode = $.cookie('mode');
        if (mode) $('#play-mod b[value="' + mode + '"]').addClass('on').siblings('b.on').removeClass('on');
        // 选择追号投注
        $('#bets-cart tr.code').live('click', function () {
            $(this).addClass('choosed').siblings('tr.choosed').removeClass('choosed');
        });
        // 确认购买事件绑定
        $('#btnPostBet').unbind('click');
        $('#btnPostBet').bind('click', lottery.game_post_code);
        $("#btnQuickPostBet").bind('click', function () {
            lottery.game_add_code();
            setTimeout(function () {
                lottery.game_post_code();
            }, 500);
        });


        // 开奖数据块首次加载
        setTimeout(function () {
            $.load('/game/lottery?id=<?php echo $type_id;?>', '#game-lottery .lottery-container');
        }, 1000);
        // 订单菜单下拉固定
        beter.game_bets_menu_fixed();
        // 订单选择
        beter.bet_select();
    })();
</script>