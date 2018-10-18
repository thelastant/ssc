<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<cp_data_open class="app-page">
    <header class="header-bar">
        <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
        <div class="center">
            <h1 class="title">开奖中心</h1>
        </div>
        <a class="btn pull-right icon icon-sync"></a>
    </header>
    <div class="content container" id="cpOpenApp">
        <div class="game-list-open-page">
            <div class="theme row" v-for=" it in type_list">
                <div class="cp_list">
                    <ul class="list-unstyled">
                        <li class="list-group-item" v-for="vit in it.type_list">
                            <div class="row">
                                <a :href="'#!cp_open_logs/wb_data_logs?id='+vit.id">
                                    <div class="col-xs-12 item-data">
                                        <p class="big-title">
                                            <span v-html="vit.title"></span>
                                            <small>第 <span v-html="vit.last_no"></span>期</small>
                                        </p>
                                        <p class="open-box">
                                            <code v-for="(code,key) in vit.last_result" v-html="code"></code>
                                        </p>
                                    </div>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
    <script>

        seajs.use(["vue", "apiJs"], function () {
            var api = seajs.require("apiJs");
            $(".icon-sync").on('click', function () {
                vm.getTypeList();
            });
            var vm = new Vue({
                el: "#cpOpenApp",
                data: {
                    type_list: [],
                    is_loading: true,
                },
                methods: {
                    getTypeList: function () {
                        var _self = this;
                        _self.type_list = [];
                        api.getTypeMB(function (ret) {
                            setTimeout(function () {
                                _self.is_loading = false;
                            }, 2000);
                            if (ret.code !== 200) {
                                layer.msg(ret.msg);
                            }
                            ret.data.forEach(function (item) {
                                _self.type_list.push(item);
                            });
                            console.log(_self.type_list);
                        });

                    }
                },
                mounted: function () {
                    this.getTypeList();
                }
            });
        });
    </script>
</cp_data_open>