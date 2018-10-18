<div id="agent-member-dom" class="common container">
    <div class="head">
        <div class="name icon-address-book">会员管理</div>
        <form action="/agent/member" class="search" method="get">
            <div class="select-box mode">
                <select name="type" class="cs-select mode">
                    <option value="0"<?php if ($args['type'] == 0) echo ' selected'; ?>>所有成员</option>
                    <option value="1"<?php if ($args['type'] == 1) echo ' selected'; ?>>直属下级</option>
                    <option value="2"<?php if ($args['type'] == 2) echo ' selected'; ?>>所有下级</option>
                </select>
            </div>
            <div class="select-box state">
                <select name="online" class="cs-select state">
                    <option<?php if ($args['online'] != 0 && $args['online'] != 1) echo ' selected'; ?> value="">状态
                    </option>
                    <option value="0"<?php if ($args['online'] == 0) echo ' selected'; ?>>在线</option>
                    <option value="1"<?php if ($args['online'] == 1) echo ' selected'; ?>>离线</option>
                </select>
            </div>
            <input type="text" name="username" value="<?php echo $args['username'] ? $args['username'] : ''; ?>"
                   class="input" placeholder="用户名">
            <button type="submit" class="btn btn-brown btn-no-shadow">查询</button>
        </form>
        <a href="javascript:;" class="member_add btn btn-green icon-plus">添加会员</a>
    </div>
    <form id="agentAccountApp" class="member_add_box hide" method="post" action="/agent/member_add" target="ajax-form">
        <div class="item">
            <div class="name">用户名</div>
            <div class="value fandian">
                <input type="text" name="username" v-model="account.username" required title="用户名" placeholder="请输入用户名"
                       style="width:300px">
            </div>
        </div>
        <div class="item">
            <div class="name">登录密码</div>
            <div class="value fandian">
                <input type="text" name="password" v-model="account.password" required title="登录密码"
                       placeholder="请输入登录密码" style="width:300px">
            </div>
        </div>
        <div class="item">
            <div class="name">会员类型</div>
            <div class="value type">
                <label><input type="radio" name="type" v-model="account.type" value="1" title="代理" checked="checked">代理</label>
                <label><input name="type" type="radio" v-model="account.type" value="0" title="会员">会员</label>
            </div>
        </div>
        <div class="item">
            <div class="name">用户返点</div>
            <div class="value fandian">
                <input type="text" name="fanDian" v-model="account.fanDian" required title="用户返点" placeholder="投注返点最大值">
            </div>
            <div class="addon name">%，设置上限：<?php echo $max; ?></div>
        </div>
        <div class="item">
            <div class="name">工资百分比</div>
            <div class="value fandian">
                <input type="text" name="day_rate" v-model="account.day_rate" required title="工资百分比"
                       placeholder="投注返点最大值">
            </div>
            <div class="addon name">%，设置上限：<?php echo($this->user['day_rate']); ?></div>
        </div>
        <button type="button" @click="submit" class="btn btn-blue icon-ok">确认添加</button>
    </form>
    <div class="body"><?php require(TPL . '/agent/member_body.tpl.php'); ?></div>
</div>
<script>
    seajs.use(["vue"], function () {
        var vm = new Vue({
            el: "#agentAccountApp",
            data: {
                account: {
                    type: 0,
                    username: '',
                    password: '',
                    day_rate: '',
                    fanDian: '',
                }
            },
            methods: {
                changeType: function (type) {
                    this.account.type = type;
                    console.log(type);
                },
                submit: function () {
                    var _self = this;
                    $.post("/agent/member_add", {account: _self.account}, function (ret) {
                        layer.open({content: ret.msg});
                        if (ret.code === 200) {
                            //清空数据
                            _self.account = {
                                type: 0,
                                username: '',
                                password: '',
                                day_rate: '',
                                fanDian: '',
                            };
                        }
                    }, "JSON");
                }
            }
        })
    });
</script>
<script type="text/javascript">
    $(function () {
        seajs.use([], function () {

            $(document).on('click', ".ajax-href-form", function (e) {
                e.preventDefault();
                var $this = $(this);
                var data = $this.serialize();
                var url = $this.attr("href");
                var ref = $this.attr("ref");
                $.get(url, function (ret) {
                    layer.open({content: ret.data.body});
                    if (ret.code === 200) {
                        //window.location.reload();
                    }
                });

            });
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
                        }, 1200);
                    }
                });

            });
        });

        // 添加会员
        $('#agent-member-dom .member_add').bind('click', function () {
            $('#agent-member-dom .member_add_box').toggleClass("hide");
        });
        // 其他选择
        $('#agent-member-dom select.cs-select').each(function () {
            new SelectFx(this);
        });
        // 菜单下拉固定
        $.scroll_fixed('#agent-member-dom .head');
    });
</script>