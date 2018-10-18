console.log("* 本程序用于学习，研究彩票用途请勿商用！！！！！！！*");
define(function (require, exports, module) {
    require("functions");
    require("commonJs");

    // 玩法分类切换 1.group
    //lottery.group_tab
    $(document).on('change', '#group_list', lottery.group_tab);

    // 玩法切换 2.detail
    $(document).on('change', "#play_list_select", lottery.play_tab);

    // 位数一键全选
    $(document).on('click', '#digit_select_all', function () {
        $('#wei-shu :checkbox').attr('checked', true);
    });

    // 文本框投注号码清空
    $(document).on('click', '#clear_num_func', function () {
        $('#textarea-code').val('');
        lottery.calc_amount();
    });


    // 选号按钮点击事件
    $(document).on('click', '#num-select input.code', function () {
        var call = $(this).attr('action');
        if (call && $.isFunction(call = lottery.select_funcs[call])) {
            call.call(this, $(this).parent());
        } else {
            if ($(this).is('.checked')) {
                $(this).removeClass('checked');
            } else {
                $(this).addClass('checked');
            }
        }
        // 重新计算总预投注数和金额
        lottery.prepare_bets();
    });


    // 操作快速选号按钮点击事件
    $(document).on('click', '#num-select input.action', function () {
        var call = $(this).attr('action');
        var pp = $(this).parents(".pp");
        $(this).addClass('on').siblings('.action').removeClass('on');

        if (call && $.isFunction(call = lottery.select_funcs[call])) {
            call.call(this, pp);
        } else if ($(this).is('.all')) { // 全: 全部选中
            $('input.code', pp).addClass('checked');
        } else if ($(this).is('.large')) { // 大: 选中5到9
            $('input.code.max', pp).addClass('checked');
            $('input.code.min', pp).removeClass('checked');
        } else if ($(this).is('.small')) { // 小: 选中0到4
            $('input.code.min', pp).addClass('checked');
            $('input.code.max', pp).removeClass('checked');
        } else if ($(this).is('.odd')) { // 单: 选中单数
            $('input.code.d', pp).addClass('checked');
            $('input.code.s', pp).removeClass('checked');
        } else if ($(this).is('.even')) { // 双: 选中双数
            $('input.code.s', pp).addClass('checked');
            $('input.code.d', pp).removeClass('checked');
        } else if ($(this).is('.none')) { // 清: 全不选
            $('input.code', pp).removeClass('checked');
        }
        lottery.prepare_bets();
    });


    // 胆拖模式: 选项卡切换
    $(document).on('click', '#num-select .dantuo :radio', function () {
        var dom = $(this).closest('.dantuo');
        if (this.value) {
            dom.next().fadeOut(function () {
                $(this).next().fadeIn();
            });
        } else {
            dom.next().next().fadeOut(function () {
                $(this).prev().fadeIn();
            });
        }
    });


    // 胆拖模式: 胆码与拖码校验
    $(document).on('click', '#num-select .dmtm :input.code', function (event) {
        var $this = $(this);
        var $dom = $this.closest('.dmtm');
        if ($('.code.checked[value=' + this.value + ']', $dom).not(this).length == 1) {
            $this.removeClass('checked');
            $.error('选择胆码不能与拖码相同');
            return false;
        }
    });

    // 快3: 二同号单选处理
    $(document).on('click', '#num-select .zhixu115 :input.code', function (event) {
        var $this = $(this);
        if (!$this.is('.checked')) return false;
        var $dom = $this.closest('.zhixu115');
        $('.code.checked[value=' + this.value + ']', $dom).removeClass('checked');
        $this.addClass('checked');
    });


    // 模式切换
    $(document).on('click', '#play-mod .danwei', lottery.mod_tab);

    // 变更投注倍数事件处理
    $(document).on('change', '#beishu-value', lottery.prepare_bets);

    $(document).on('click', '#beishu-warp .sur', function () {
        var dom = $('#beishu-value');
        var new_val = parseInt(dom.val()) - 1;
        if (new_val < 1) new_val = 1;
        dom.val(new_val);
        lottery.prepare_bets();
    });

    $(document).on('click', '#beishu-warp .add', function () {
        var dom = $('#beishu-value');
        var new_val = parseInt(dom.val()) + 1;
        dom.val(new_val);
        lottery.prepare_bets();
    });

    //绑定投注投注
    $(document).on('click', '#btnPostBet', function () {
        var loadIndex = layer.open({type: 2});
        lottery.game_post_code();
        layer.close(loadIndex);
    });

    // 追号处理
    $(document).on('click', '#btnZhuiHao', lottery.game_zhui_hao);


    // 追号期数选择
    var zhuihao_data_func = function () {
        var num = $('.zhuihao_box td input:checked').length;
        var amount = $('#zhuihao_amount').val();
        var total = (amount * num).toFixed(2);
        $('#zhuihao_num').text(num);
        $('#zhuihao_total').text(total);
    };

    $(document).on('click', '.zhuihao_box td.choose_all', function () {
        $('.zhuihao_box td input:not(:checked)').attr('checked', 'checked');
        zhuihao_data_func();
    });

    $(document).on('click', '.zhuihao_box td input', zhuihao_data_func);

    // 玩法说明
    $(document).on('mouseover', '#game-play .play-info .showeg', function () {
        var action = $(this).attr('action');
        var ps = $(this).position();
        $('#' + action).siblings('.play-eg').hide();
        $('#' + action).css({top: ps.top + 22, left: ps.left - 7}).fadeIn();

    });

    $(document).on('mouseout', '#game-play .play-info .showeg', function () {
        $('#game-play .play-info .play-eg').hide();
    });

});