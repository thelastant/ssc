<?php
/**
 * Email:##NONE
 * Date: 2017/2/24
 * Time: 17:15
 */
?>
<agent_member_list class="app-page">
    <div class="agent_member_list_app" id="agentMemberApp">
        <header class="header-bar home-bar">
            <a class="btn pull-left icon icon-chevron-left" data-navigation="$previous-page"></a>
            <div class="center">
                <h1 class="title">团队管理</h1>
            </div>
        </header>
        <div class="content container">
            <nav class="ey-tabs ey-header-tabs" style="top: 50px;">
                <div class="ey-tab-items">
                    <form action="" class="inline-form">
                    </form>
                </div>
            </nav>
            <div class="table-box">
                <div class="row table-head">
                    <div class="phone-3 column">用户名</div>
                    <div class="phone-3 column">余额(元)</div>
                    <div class="phone-3 column">返点</div>
                    <div class="phone-3 column">操作</div>
                </div>
                <div class="row table-body" v-for="vo in member_list">
                    <div class="phone-3 column" v-html="vo.username"></div>
                    <div class="phone-3 column">
                        <span v-html="vo.coin" class="money text-danger"></span>
                    </div>
                    <div class="phone-3 column">
                        <span v-html="vo.fanDian+'%'" class="money text-danger"></span>
                    </div>
                    <div class="phone-3 column">
                        无
<!--                        <a href="" class="list-action">设置</a>-->
<!--                        <a href="" class="list-action">转账</a>-->
                    </div>
                </div>
            </div>
            <div class="go-next">
                <div class="next-btn" @click="getNext()">点击加载更多数据</div>
            </div>
        </div>

        <script>
            seajs.use(["vue", "apiJs", "moment"], function () {
                var api = seajs.require("apiJs");
                //var moment = seajs.require("moment");
                Vue.debug = true;
                var vm = new Vue({
                    el: "#agentMemberApp",
                    data: {
                        member_list: [],
                        search: {
                            uid: parseInt("<?php echo $this->request->request("uid", $this->user['uid'], "intval"); ?>"),
                            limit: 100,
                        }
                    },
                    filters: {},
                    methods: {
                        parseDate: function (time) {
                            return moment(time * 1000).format("YYYY-MM-DD..");
                        },
                        getTypeTitle: function (type) {

                            return api.COIN_TYPES[type];
                        },
                        getDataList: function () {
                            //get
                            var _self = this;
                            LoadStart();
                            _self.member_list = [];
                            $.post(api.API_ROUTES.get_agent_member, {search: _self.search}, function (ret) {
                                if (ret.code === 200) {
                                    ret.data.data_list.forEach(function (item) {
                                        _self.member_list.push(item);
                                    });
                                }
                                LoadStop();
                            });
                        },
                        getNext: function () {

                        }
                    },
                    mounted: function () {
                        this.getDataList();
                    }
                });
            });

        </script>
    </div>
</agent_member_list>
