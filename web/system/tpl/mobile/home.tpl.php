<div class="content container" id="indexApp">
    <div class="game-list-page">
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
                                    <div class="cart-box">
                                        <img draggable="false" src="<?php echo THEME_PATH; ?>images/cart.png" alt="">
                                    </div>
                                </div>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<style>
#gameApp #groupApp .select, #gameApp #groupApp #play_list_select {
    background: transparent;
    border-bottom: 3px solid #00b7ee;
    color: #00b7ee;
    text-align: center;
}
#gameApp .game-play-log p {
    color: #00B7EE;
}
#gameApp .current-action {
    line-height: 50px;
    color: #00B7EE;
    font-size: 16px;
}
#gameApp #game-play .num-table .pp {
    padding: 10px;
    margin-top: 15px;
    background: rgba(0,0,0,.0001);
}
#gameApp #game-play .num-table .pp .title {
    text-align: center;
    position: relative;
    display: block;
    color: #00B7EE;
    margin-bottom: 10px;
}
#cpOpenApp .game-list-open-page .list-group-item .big-title {
    font-size: 14px;
    line-height: 30px;
    color: #00b7ee;
}
</style>
<script>
    seajs.use(["vue", "apiJs", "commonJs"], function () {
        var api = seajs.require("apiJs");
        var vm = new Vue({
            el: "#indexApp",
            data: {
                type_list: [],
                is_loading: true,
            },
            methods: {
                getTypeList: function () {
                    var _self = this;
                    _self.type_list = [];
                    api.getTypeMB(function (ret) {
                        if (ret.code !== 200) {
                            layer.msg(ret.msg);
                        }
                        ret.data.forEach(function (item) {
                            _self.type_list.push(item);
                        });
                    });
                }
            },
            mounted: function () {
                this.getTypeList();
            }
        });
    });
</script>
