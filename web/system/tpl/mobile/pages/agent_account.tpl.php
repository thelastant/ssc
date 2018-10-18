<agent_account data-loader="true" class="app-page">
    <header class="header-bar">
        <button class="btn icon icon-arrow-back pull-left" data-navigation="user_dashboard"></button>
        <div class="center">
            <h1 class="title">精准开户</h1>
        </div>
    </header>
    <div id="agentAccountApp" class="content account">
        <input type="hidden" name="userType" value="0">
        <ul class="list">
            <li class="divider deep-gray">① 选择账户类型</li>
        </ul>
        <div class="account-type">
            <ul class="list-unstyled">
                <li class="left" :class="{'current':account.type===0}" data-value="0" @click="changeType(0)">
                    <i class="fa fa-lg fa-user"></i>
                    <span>会员账号</span>
                </li>
                <li class="right no-margin" :class="{'current':account.type===1}" data-value="1" @click="changeType(1)">
                    <i class="fa fa-lg fa-users"></i>
                    <span>代理账号</span>
                </li>
            </ul>
        </div>
        <ul class="list">
            <li class="divider deep-gray">② 设置账户信息</li>
            <li><span class="account-span padded-list">登录的用户名：</span>
                <input type="text" v-model="account.username"
                       required
                       placeholder="点击输入"/>
            </li>
            <li><span class="account-span padded-list">登录密码设置：</span>
                <input type="text" v-model="account.password"
                       required
                       placeholder="点击输入"/>
            </li>
            <li><span class="account-span padded-list">配置用户返点：</span>
                <input type="text" v-model="account.fanDian"
                       required
                       placeholder="上限设置<?php echo $this->user['fanDian'] - 0.1; ?>%"/>
            </li>
            <li><span class="account-span padded-list">日工资百分比：</span>
                <input type="text" v-model="account.day_rate"
                       required
                       placeholder="上限设置<?php echo($this->user['day_rate'] - 0.1); ?>%"/>
            </li>
        </ul>
        <div class="padded-full">
            <button type="button" name="button" @click="submit" class="btn fit-parent primary">
                生成账户
            </button>
        </div>
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
</agent_account>
