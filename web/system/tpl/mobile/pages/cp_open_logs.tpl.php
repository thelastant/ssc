<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<div class="" id="cpOpenHistoryLogApp">
    <header class="header-bar">
        <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
        <div class="center">
            <h1 class="title" v-html="type_detail.title"></h1>
        </div>
        <a class="btn pull-right" :href="'#!cp_game/mb_index?id='+type_detail.id">立即投注</a>
    </header>
    <div class="content container">
        <div class="game-list-page">
            <div class="cp_list">
                <ul class="list-unstyled">
                    <li class="list-group-item row" v-for="it in history_list">
                        <div class="col-xs-5">
                            <span v-html="it.number"></span>期
                        </div>
                        <div class="col-xs-7">
                            <code v-for=" vit in it.data" v-html="vit"></code>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>

    seajs.use(["vue", "apiJs"], function () {
        $("#bottom-bar").hide();//隐藏
        var api = seajs.require("apiJs");
        var vm = new Vue({
            el: "#cpOpenHistoryLogApp",
            data: {
                type_detail: (<?php echo json_encode($current_type);?>),
                history_list: [],
            },
            methods: {
                getTypeList: function () {

                },
                getHistory: function () {
                    var _self = this;
                    LoadStart();
                    _self.history_list = [];
                    $.post("/game/api_get_history", {id: _self.type_detail.id}, function (ret) {
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
                this.getTypeList();
                this.getHistory();
            }
        });
    });
</script>
