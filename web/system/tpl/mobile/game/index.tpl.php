<!--头部导航-->
<div>
    <header class="header-bar">
        <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
        <div class="center">
            <h1 class="title"><?php echo $types[$type_id]['title']; ?>
                <span id="timer_lottery"></span>
            </h1>
        </div>
        <a class="btn pull-right fa fa-lg fa-bars toggle-history"></a>
    </header>
    <style>
        .history_show {
            display: none;
            position: fixed;
            transition: all .3s;
            top: 52px;
            background: #fff;
            max-height: 70%;
            overflow: scroll;
            right: 0;
            z-index: 1001;
            box-shadow: 1px 1px 1px #eee;
        }

        .history_show.is_show {
            display: block;
        }

        .history_show code {
            margin-left: 3px;
            line-height: 26px;
            height: 26px;
            overflow: hidden;
            width: 26px;
            font-size: 14px;
            color: #fff;
            background: #00a2d4;
            border-radius: 100%;
            text-align: center;
            display: inline-block;
        }
    </style>
    <div class="content container" id="gameApp">
        <div id="game-lottery" type="<?php echo $type_id; ?>"
             ctype="<?php echo $types[$type_id]['type'] ?>">
            <div class="lottery-container"></div>
        </div>
        <div id="game-play">
            <div class="row" id="groupApp">
                <div class="group col-xs-6">
                    <select class="select" id="group_list" @change="getPlays();" v-model="current.group_id">
                        <option :value="g.id" v-for="g in group_list" v-html="g.groupName"></option>
                    </select>
                </div>
                <div class="play-list col-xs-6">
                    <select name="play_list_select" id="play_list_select" v-model="current.play_id">
                        <option :value="p.id" v-html="p.name" v-for="p in play_list"></option>
                    </select>
                </div>

                <div class="history_show">
                    <ul class="list-unstyled">
                        <li class="list-group-item" v-for="it in history_list">
                            <p>
                                <span v-html="it.number"></span>期
                                <code v-for="vit in it.data" v-html="vit"></code>
                            </p>

                        </li>
                    </ul>
                </div>
            </div>
<style>
#gameApp #game-play .num-table .pp .ey-num-box .code.checked {
    line-height: 40px;
    background-color: #ce1229;
    color: #fff;
    font-size: 14px;
}
</style>
            <div class="play">
                <?php require(TPL . '/game/play_index.tpl.php'); ?>
            </div>
            <div class="play-work">
                <!---->
                <div id="play-work-setting">
                    <!--返点配置-->
                    <!--<div class="game-fixed-bottom nav-fixed-bottom" style="    bottom: 0;
    display: table;
    width: 100%;
    padding: 0;
    table-layout: fixed;
    border-top: 0;
    position: fixed;
    z-index: 10;
    right: 0;
    left: 0;
    height: 95px;
    border-bottom: 0;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;">
                        <div class="row game-row-1">
                            <div class="phone-3 column">
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
                                        color: #fff;
                                    }
                                </style>
                                <div class="slider-bar-fd" data-select-fd="0.0">
                                    <div class="left_fd_rate">1返点:<span class="left_fd_label"></span>%</div>
                                    <div class="right_jjz">奖金:<span class="right_jjz_label"></span></div>
                                </div>
                            </div>
                            <div class="phone-5 column">
                                <div id="play-mod">
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
                                            echo '<b value="' . $mod['value'] . '" data-max-fan-dian="' . $mod['rebate'] . '" class="phone-3 ' . $class . '">' . $mod['name'] . '</b>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="phone-4 column">
                                <div id="beishu-warp">
                                    <i class="sur trans beishu-action-btn fa fa-minus"></i>
                                    <input type="number"
                                           autocomplete="off"
                                           disabled="disabled"
                                           id="beishu-value"
                                           value="<?php echo (array_key_exists('beiShu', $_COOKIE) && is_numeric($_COOKIE['beiShu']) && $_COOKIE['beiShu'] > 0) ? intval($_COOKIE['beiShu']) : 1; ?>">
                                    <i class="add trans beishu-action-btn fa fa-plus"></i>
                                    <span>倍</span>
                                </div>
                            </div>
                        </div>
                        <div class="row game-row-2">
                            <div class="phone-6 column">
                                <div class="bet-info icon-chart-bar"><span id="all-count">0</span>注，<span
                                            id="all-amount">0.00</span>元
                                </div>
                            </div>
                            <div class="phone-6 column">
                                <div class="row">
                                    <div id="play-work-data">
                                        <div id="play-btn">
                                            <input type="hidden" id="zhuiHao" name="zhuiHao" value="0">
                                            <a href="javascript:void(0);" id="btnZhuiHao"
                                               class="icon-magic">追号</a>
                                            <a href="javascript:void(0);"
                                               id="btnPostBet"
                                               class=" icon-basket">确认投注
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>-->
                </div>
            </div>
        </div>
        <div style="height: 120px;display: block;"></div>
    </div>
</div>
<script type="text/javascript">
    window.game = {
        type: <?php echo $type_id;?>,
        game_type:<?php echo $current_type;?>,
        played: <?php echo $play_id;?>,
        groupId: <?php echo $group_id;?>,
        stop: <?php echo $this->config['switchBuy'] == 0 ? 'true' : 'false';?>,
        ban: <?php echo ($this->config['switchDLBuy'] == 0 && $this->user['type']) ? 'true' : 'false';?>,
    };
    window.GAME_TOTAL = window.game;

    seajs.use(["gameJs", "vue"], function () {
        $(".toggle-history").on('click', function () {
            groupVm.getHistory();
            $(this).toggleClass("fa-times");
            $(this).toggleClass("fa-bars");
            $(".history_show").toggleClass("is_show");
        });

        var groupVm = new Vue({
            el: "#groupApp",
            data: {
                group_list: [],
                play_list: [],
                history_list: [],
                current: {
                    group_id: GAME_TOTAL.groupId,
                    play_id: GAME_TOTAL.played,
                }
            },
            methods: {
                getGroup: function () {
                    var _self = this;
                    $.post("/api/get_api_groups", {type: GAME_TOTAL.game_type}, function (ret) {
                        if (ret.code === 200) {
                            ret.data.forEach(function (item) {
                                _self.group_list.push(item);
                            });
                        }
                    });
                },
                getPlays: function () {
                    var _self = this;
                    _self.play_list = [];
                    $.post("/api/get_api_plays", {group_id: _self.current.group_id}, function (ret) {
                        if (ret.code === 200) {
                            ret.data.forEach(function (item) {
                                _self.play_list.push(item);

                            });
                            _self.current.play_id = ret.data[0].id;
                        }
                        //调用一下
                        console.log(_self.play_list);
                    });
                },
                getHistory: function () {
                    var _self = this;
                    LoadStart();
                    _self.history_list = [];
                    $.post("/game/api_get_history", {id: GAME_TOTAL.type, limit: 20}, function (ret) {
                        if (ret.code === 200) {
                            ret.data.forEach(function (item) {
                                item.data = item.data.split(",");
                                _self.history_list.push(item);
                            });
                        }
                        LoadStop();
                    })
                }
            },
            mounted: function () {
                this.getGroup();
                this.getPlays();
            }
        });

        lottery.switcher.bets_fresh = false;

        // 声音初始化
        voice.init();

        // 初始化历史选择模式
        var mode = $.cookie('mode');
        if (mode) $('#play-mod b[value="' + mode + '"]').addClass('on').siblings('b.on').removeClass('on');

        // 开奖数据块首次加载
        $('#game-lottery .lottery-container').load('/game/lottery?id=' + GAME_TOTAL.type);

        // 订单菜单下拉固定
        beter.game_bets_menu_fixed();

        // 订单选择
        beter.bet_select();
    });
</script>
<nav id="bottom-bar" class="mui-bar mui-bar-tab">
<div id="gameApp">
<div id="game-play">
<div class="game-fixed-bottom nav-fixed-bottom" >
                        <div class="row game-row-1" style="background: rgba(0, 0, 0, 0);margin-left: 10px;">
                            <div class="phone-3 column">
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
                                        color: #fff;
                                    }
                                </style>
                                <div class="slider-bar-fd" data-select-fd="0.0">
                                    <div class="left_fd_rate">返点:<span class="left_fd_label"></span>%</div>
                                    <div class="right_jjz">奖金:<span class="right_jjz_label"></span></div>
                                </div>
                            </div>
                            <div class="phone-5 column">
                                <div id="play-mod">
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
                                            echo '<b value="' . $mod['value'] . '" data-max-fan-dian="' . $mod['rebate'] . '" class="phone-3 ' . $class . '">' . $mod['name'] . '</b>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="phone-4 column">
                                <div id="beishu-warp">
                                    <i class="sur trans beishu-action-btn fa fa-minus"></i>
                                    <input type="number"
                                           autocomplete="off"
                                           id="beishu-value"
                                           value="<?php echo (array_key_exists('beiShu', $_COOKIE) && is_numeric($_COOKIE['beiShu']) && $_COOKIE['beiShu'] > 0) ? intval($_COOKIE['beiShu']) : 1; ?>">
                                    <i class="add trans beishu-action-btn fa fa-plus"></i>
                                    <span>倍</span>
                                </div>
                            </div>
                        </div>
                        <div class="row game-row-2" style="margin-left: 10px;width: 90%;">
                            <div class="phone-6 column">
                                <div class="bet-info icon-chart-bar"><span id="all-count">0</span>注，<span
                                            id="all-amount">0.00</span>元
                                </div>
                            </div>
                            <div class="phone-6 column">
                                <div class="row">
                                    <div id="play-work-data">
                                        <div id="play-btn">
                                            <input type="hidden" id="zhuiHao" name="zhuiHao" value="0">
                                            <a href="javascript:void(0);" id="btnZhuiHao"
                                               class="icon-magic">追号</a>
                                            <a href="javascript:void(0);"
                                               id="btnPostBet"
                                               class=" icon-basket">确认投注
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					</div>
					</div>
</nav>
