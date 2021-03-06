$(function () {

    // 加载动画
    $.inited = false;
    $.dom_body = $('#dom_body');
    $.dom_body.css('min-height', "1080px").fadeIn();

    // 刷新当前页面
    $.reload = function () {
        var url = arguments[0] || window.location.href;
        $.dom_body.fadeOut(function () {
            if (history && history.pushState) {
                var state = {
                    title: document.title,
                    url: url,
                    selector: 'html'
                };
                history.pushState(state, document.title, url);
            }
            window.location.href = url;
        });
    };

    // 基础函数映射
    Number.prototype.round = Number.prototype.toFixed;
    // 下拉固定
    $.scroll_fixed_tops = {};

    $.scroll_fixed = function (selector) {
        var dom = $(selector);
        var scroll_fixed_func = function () {
            var this_dom = $(selector);
            if (this_dom.length > 0) {
                if ($(window).scrollTop() > $.scroll_fixed_tops[selector]) {
                    this_dom.addClass('fixed');
                } else {
                    this_dom.removeClass('fixed');
                }
            } else {
                $(window).unbind('scroll', scroll_fixed_func);
                delete $.scroll_fixed_tops[selector];
            }
        };
        var sid = setInterval(function () {
            $.scroll_fixed_tops[selector] = dom.offset().top;
            if ($.scroll_fixed_tops[selector] > 0) {
                clearInterval(sid);
                $(window).bind('scroll', scroll_fixed_func);
            }
        }, 300);
    };
    // 对话框
    var dialogue_func = function () {
        var dom_dialogue = $('#dialogue');
        var dom_warp = dom_dialogue.find('.dialogue-warp');
        var dom_body = dom_dialogue.find('.dialogue-body');
        var dom_foot = dom_dialogue.find('.dialogue-foot');
        var dom_auto = dom_foot.find('.dialogue-auto');
        var dom_sec = dom_auto.find('.dialogue-sec');
        var dom_yes = dom_foot.find('.dialogue-yes');
        var dom_no = dom_foot.find('.dialogue-no');
        var interval_id;
        var interval_clear = function () {
            if (interval_id) {
                clearInterval(interval_id);
                interval_id = null;
            }
        };
        var icon_types = {
            error: 'dialogue-icon error icon-attention-alt',
            success: 'dialogue-icon success icon-ok-1',
        };
        var opt_parse = function (opt) {
            try {
                opt = JSON.parse(opt.text);
            } catch (e) {
            }
            return opt;
        };
        var close = function () {
            if (ing) {
                dom_dialogue.fadeOut(function () {
                    ing = false;
                    if (queue.length > 0) {
                        var opt = queue.splice(0, 1);
                        init(opt[0]);
                    }
                });
            }
        };
        var queue = [];
        var ing = false;
        var init = function (opt) {
            if (opt === null) { // 清空队列
                queue = [];
            } else if (opt === 'close') {
                close();
            } else if (ing) { // 添加到队列
                queue.push(opt);
            } else {
                ing = true;
                var hide_foot = true;
                var func_yes = close;
                var func_no = close;
                opt = opt_parse(opt);
                dom_yes.unbind('click');
                dom_no.unbind('click');
                if (opt.class) {
                    dom_dialogue.attr('class', 'dialogue ' + opt.class);
                } else {
                    dom_dialogue.attr('class', 'dialogue');
                }
                if (opt.text) {
                    dom_body.css('min-height', '37px').html('<div class="dialogue-icon"></div><div class="dialogue-text"></div>');
                    var dom_icon = dom_body.find('.dialogue-icon');
                    var dom_text = dom_body.find('.dialogue-text');
                    dom_icon.attr('class', icon_types[opt.type || 'error']);
                    dom_text.html(opt.text || '未定义的错误');
                } else if (opt.body) {
                    dom_body.css('min-height', 0).html(opt.body.replace('\\n', '\n'));
                }
                if (opt.auto) {
                    dom_auto.css('display', 'inline-block');
                    hide_foot = false;
                    dom_sec.text('10');
                    if (interval_id) interval_clear();
                    interval_id = setInterval(function () {
                        var sec = parseInt(dom_sec.text());
                        if (sec === 0) {
                            interval_clear();
                            func_yes();
                            if (func_yes !== close) close();
                        } else {
                            sec--;
                            dom_sec.text(sec);
                        }
                    }, 1000);
                } else {
                    dom_auto.css('display', 'none');
                }
                if (opt.yes) {
                    dom_yes.css('display', 'inline-block');
                    hide_foot = false;
                    dom_yes.text(opt.yes.text);
                    if (opt.yes.func) func_yes = typeof opt.yes.func === 'function' ? opt.yes.func : new Function(opt.yes.func);
                    dom_yes.bind('click', function () {
                        interval_clear();
                        func_yes();
                        if (func_yes !== close) close();
                    });
                } else {
                    dom_yes.css('display', 'none');
                }
                if (opt.no) {
                    dom_no.css('display', 'inline-block');
                    hide_foot = false;
                    dom_no.text(opt.no.text);
                    if (opt.no.func) func_no = typeof opt.no.func === 'function' ? opt.no.func : new Function(opt.no.func);
                    dom_no.bind('click', function () {
                        interval_clear();
                        func_no();
                        if (func_no !== close) close();
                    });
                } else {
                    dom_no.css('display', 'none');
                }
                if (hide_foot) {
                    dom_foot.css('display', 'none');
                } else {
                    dom_foot.css('display', 'block');
                }
                dom_dialogue.fadeIn(function () {
                    var height = dom_warp.height();
                    dom_warp.animate({'margin-top': '-' + (height / 2) + 'px'});
                });
            }
        };
        return init;
    };
    $.dialogue = dialogue_func();
    // 对话框简写方式
    $.error_show = true; // 一次性临时开关
    $.error = function (text) {
        if ($.error_show) {
            $.dialogue({
                type: 'error',
                text: text,
                auto: true,
                yes: {text: '确定'},
            });
        } else {
            $.error_show = true;
        }
    };

    $.success = function (text) {
        $.dialogue({
            type: 'success',
            text: text,
            auto: true,
            yes: {text: '确定'},
        });
    };

    // load: 页面加载
    var show_map = {
        fade: {
            show: 'fadeIn',
            hide: 'fadeOut',
        },
        slide: {
            show: 'slideDown',
            hide: 'slideUp',
        },
    };
    $.load = function (url, selector, opt, send_data) {
        if (typeof opt === 'undefined') {
            opt = {top: false, show: 'fade'};
        } else {
            if (!opt.hasOwnProperty('top')) opt.top = false;
            //if (!opt.hasOwnProperty('show')) opt.show = 'fade';
        }
        var send_data = send_data || {};
        send_data.client_type = 'web';
        var backtop = function (callback) {
            if (opt.top) {
                $('body').animate({scrollTop: 0}, 0, callback);
            } else {
                callback();
            }
        };
        var show = function (selector, data) {
            var dom = $(selector);
            if (opt.show) {
                dom[show_map[opt.show].hide](function () {
                    $(this).html(data)[show_map[opt.show].show]();
                    if (opt.callback) opt.callback();
                });
            } else {
                dom.html(data);
                if (opt.callback) opt.callback();
            }
        };
        $.ajax({
            url: url,
            cache: false,
            data: send_data,
            type: 'POST',
            dataType: 'text',
            success: function (data, textStatus, xhr) {
                var error_message = xhr.getResponseHeader('X-Error-Message');
                if (error_message) {
                    $.error(error_message === 'dialogue' ? data : decodeURIComponent(error_message));
                } else if (selector) {
                    backtop(function () {
                        show(selector, data);
                    });
                }
            },
            complete: function (xhr) {
                if (opt.complete) opt.complete(xhr);
            },
        });
    };


    // 链接点击事件
    var core_load_func = function () {

        var self = this;
        var $this = $(this);
        var func = window[$this.attr('func')];

        var onajax = func.onajax;
        var call = func.call;
        var before = func.before || function (callback) {
                callback()
            };
        var ask = $this.attr('ask');
        var url;
        var isform = $this.is('form');
        if (isform) {
            $.dialogue('close');
            $this.find('input,select,textarea').each(function () {
                if ($(this).attr('type') == 'radio' && $(this).attr('checked') != 'checked') return;
                var name = $(this).attr('name');
                var value = $(this).attr('value');
                if (name && value) $this.data(name, value);
            });
            url = $this.attr('action');
        } else {
            url = $this.attr('href');
        }
        var data = $this.data();
        data.client_type = 'web';
        if (ask && !confirm(ask)) return false;
        if (typeof call != 'function') call = function () {
        };
        if (typeof onajax === 'function') {
            try {
                var onajax_ret = onajax.call(self);
                if (onajax_ret !== true) {
                    $.error(onajax_ret);
                    return false;
                }
            } catch (err) {
                call.call(self, err.toString());
                return false;
            }
        }
        before(function () {
            $.ajax({
                url: url,
                cache: false,
                data: data,
                type: $this.attr('method') || 'POST',
                dataType: $this.attr('dataType') || 'text',
                error: function (xhr, textStatus, errThrow) {
                    call.call(self, errThrow || textStatus);
                },
                success: function (data, textStatus, xhr) {
                    $.dialogue(null);
                    var error_message = xhr.getResponseHeader('X-Error-Message');
                    if (error_message) {
                        call.call(self, error_message === 'dialogue' ? data : decodeURIComponent(error_message));
                    } else {
                        if (lottery.timer.T) clearTimeout(lottery.timer.T);
                        if (lottery.timer.KT) clearTimeout(lottery.timer.KT);
                        if (lottery.timer.moveno) clearInterval(lottery.timer.moveno);
                        call.call(self, null, data);
                        if (!isform && history && history.pushState) {
                            var selector = $this.data('container') ? $this.data('container') : '#container_warp';
                            var state = {
                                title: document.title,
                                url: url,
                                selector: selector
                            };
                            history.pushState(state, document.title, url);
                        }
                    }
                }
            });
        });
        return false;
    };
    //菜单
    $(document).on('click', 'a[target=ajax]', core_load_func);


    // 后退事件处理
    if (history && history.pushState) {
        window.onpopstate = function (e) {
            if (e.state) {
                document.title = e.state.title;
                if (e.state.selector === 'html') {
                    $.reload(e.state.url);
                } else {
                    $.load(e.state.url, e.state.selector, {top: true});
                }
            }
        }
    }

});