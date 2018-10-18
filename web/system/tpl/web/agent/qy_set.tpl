{extends file='common/base.tpl'}
{block name="content"}
    <style>
        .qy_set {
            background: #fff;
            min-height: 500px;
            padding: 15px;
        }

        .user-msg {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .set_qy {
            padding: 15px;
        }

        .qy-form .form-group {
            text-align: center;
        }
    </style>
    <div class="head">
        <div class="name icon-address-book">契约分红</div>
    </div>
    <div id="qySetApp" class="qy_set">
        <div class="row user-msg">
            <div class="col-xs-3">
                <h3>{$user.username}</h3>
                <h5>用户名</h5>
            </div>
            <div class="col-xs-3">
                <h3>{$user.fanDian}%</h3>
                <h5>返点</h5>
            </div>
            <div class="col-xs-3">
                <h3>{$user.day_rate}%</h3>
                <h5>日结工资比</h5>
            </div>
            <div class="col-xs-3">
                <h3 class="text-danger">{$user.coin}元</h3>
                <h5>账户余额</h5>
            </div>
        </div>
        <div class="row set_qy">
            <h4>
                您的契约分红最低为 <span class="text-danger">{$_user.qy_red_min_rate}%</span>,
                最高为
                <span class="text-danger">{$_user.qy_red_max_rate}%</span>
            </h4>
            <div class="col-md-8 col-md-offset-2">
                <form action="" class="form qy-form" method="post">
                    <div class="form-group">
                        <input type="checkbox" id="mode" name="mode" v-model="mode">累计上月模式
                    </div>
                    <div class="form-group" v-for="(vo,key) in qy_qd_list">
                        <label for="">半月累计销量（元）>= </label>
                        <input type="text" name="" class="input input-sm" v-model="vo.need_sale">
                        <label for="">分红 </label>
                        <input type="text" name="" class="input input-sm" v-model="vo.red_rate">
                        %
                        <button v-if="key!=(qy_qd_list.length-1)" type="button" class="btn btn-sm btn-danger"
                                @click="rmQy(key);">删除
                        </button>
                        <button v-if="key==(qy_qd_list.length-1)" type="button" class="btn btn-sm btn-info"
                                @click="addQy();">新增
                        </button>
                    </div>
                    {literal}
                    <div class="form-group">
                        <button type="button" @click="submit"  class="btn btn-sm btn-primary" :class="{disabled:!edit_enable}">签订契约</button>
                        <button type="reset" class="btn btn-sm btn-danger" :class="{disabled:!edit_enable}">重置</button>
                    </div>
                    {/literal}
                </form>
            </div>
        </div>
        <script>
            var TEMP = {
                    uid:{$user.uid},
                    edit_enable:{$edit_enable} == 1 ? true : false,
                    qy_mode:{$user.qy_mode} == 1 ? true : false,
                };
            console.log(TEMP);
        </script>
        {literal}
            <script>
                seajs.use(["vue", "apiJs"], function () {
                    var api = seajs.require("apiJs");
                    var vm = new Vue({
                        el: "#qySetApp",
                        data: {
                            qy_qd_list: [],
                            uid: TEMP.uid,
                            mode: TEMP.qy_mode,
                            edit_enable:TEMP.edit_enable
                        },
                        methods: {
                            rmQy: function (index) {
                                var _self = this;
                                _self.qy_qd_list.splice(index, 1);

                            },
                            addQy: function () {
                                var _self = this;
                                _self.qy_qd_list.push({
                                    need_sale: 0,
                                    red_rate: 0,
                                });
                            },
                            getUserQy: function () {
                                var _self = this;
                                $.post("/api/api_get_qy", {uid: _self.uid}, function (ret) {
                                    if (ret.code === 200) {
                                        if (ret.data.length <= 0) {
                                            _self.addQy();
                                        } else {
                                            ret.data.forEach(function (item) {
                                                _self.qy_qd_list.push(item);
                                            });
                                        }
                                    }
                                });
                            },
                            submit: function () {
                                //TODO 进行数据验证
                                var _self = this;
                                if(!confirm("确定么？签约以后将不可以修改")){
                                    return false;
                                }
                                $.post("/agent/api_set_qy", {
                                    mode: _self.mode,
                                    uid: _self.uid,
                                    qy_list: _self.qy_qd_list
                                }, function (ret) {
                                    layer.open({content: ret.msg});
                                });

                            }
                        },
                        mounted: function () {
                            this.getUserQy();
                        }
                    })

                });
            </script>
        {/literal}
    </div>
{/block}