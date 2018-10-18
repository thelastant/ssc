<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<header class="header-bar">
    <a class="btn pull-left icon icon-menu"></a>
    <div class="center">
        <h1 class="title">彩票大厅</h1>
    </div>
    <a class="btn pull-right icon icon-settings" onclick="ref();"></a>
</header>
<div class="content container" id="typeListApp">
    <div class="game-list-page" id="type_list_app">
        <!--theme 时时彩-->
        <div class="theme row" v-for=" it in type_list">
            <div class="title">
                <span v-html="it.title"></span>
            </div>
            <div class="cp_list">
                <ul class="list-unstyled">
                    <li class="list-group-item" v-for="vit in it.type_list">
                        <div class="row">
                            <a :href="'?#!cp_game/mb_index?id='+vit.id">
                                <div class="col-xs-3 item-title">
                                    <div class="title-box">
                                    <span>
                                        <span v-html="vit.title"></span>
                                        <br>
                                    </span>
                                    </div>
                                </div>
                                <div class="col-xs-9 item-data">
                                    <p class="big-title">
                                        <span v-html="vit.title"></span>
                                        <small>第 <span v-html="vit.last_no"></span>期</small>
                                    </p>
                                    <p class="open-box">
                                        <code v-for="(code,key) in vit.last_result" v-html="code" v-if="key<=4"></code>
                                    </p>
                                    <p class="open-box">
                                        <code v-for="(code,key) in vit.last_result" v-html="code" v-if="key>4"></code>
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

        var vm = new Vue({
            el: "#typeListApp",
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
