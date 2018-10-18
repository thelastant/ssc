<?php

?>
<notice_page class="app-page">
    <div id="noticeApp">
        <header class="header-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">系统通知</h1>
            </div>
            <a class="btn pull-right fa fa-refresh fa-lg" @click="updateNotice();"></a>
        </header>
        <div class="content container">
            <ul class="list-unstyled notice-list">
                <li v-for="it in notice_list">
                    <a class="change" @click="showContent(it.id)" v-html="'系统通知：'+it.title"></a>
                    <span class="pull-right" style="margin-top: 10px" v-html="parseDate(it.addTime)"></span>

                    <pre style="width:100%">
                    <p v-html="it.content" style="text-overflow: ellipsis;width:100%"></p>
                    </pre>

                </li>
            </ul>
        </div>
        <script>
            seajs.use(['vue', "apiJs", "moment"], function () {
                var api = seajs.require("apiJs");
                var vm = new Vue({
                    el: "#noticeApp",
                    data: {
                        notice_list: [],
                    },
                    methods: {
                        parseDate: function (time) {
                            return moment(time * 1000).format("YYYY-MM-DD HH:mm:ss");
                        },
                        showContent: function (id) {
                            //获取data

                        },
                        updateNotice: function () {
                            this.getDataList();
                        },
                        getDataList: function () {
                            //get
                            var _self = this;
                            _self.notice_list = [];
                            LoadStart();
                            $.post(api.API_ROUTES.get_notice_list, {state: _self.type, page: _self.p}, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.forEach(function (item) {
                                        _self.notice_list.push(item);
                                    });
                                }
                                LoadStop();
                            });
                        },
                    },
                    mounted: function () {
                        this.getDataList();
                    }
                });
            });
        </script>
    </div>
</notice_page>