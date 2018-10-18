{extends file='common/base.tpl'}
{block name="content"}
    <style>
        #app-notice-box {
            background: #fff;
            min-height: 500px;
        }

        .main-dom {
            padding: 30px;
        }

        .page-notice {
            color: #444;
        }

        .page-notice .notice-content {
            position: relative;
            padding: 0 15px;
        }

        .page-notice .notice-list {
            position: relative;
            min-height: 20px;
        }

        .page-notice .notice-list > .item {
            background: #ffffff;
            border: 1px solid #efefef;
            margin-bottom: 20px;
        }

        .page-notice .notice-list > .item > .title {
            height: 60px;
            line-height: 60px;
            padding: 0 30px;
            font-size: 18px;
            cursor: pointer;
        }

        .page-notice .notice-list > .item > .title > .time {
            font-size: 14px;
            float: right;
        }

        .page-notice .notice-list > .item > .title > .time > i {
            font-size: 20px;
            margin-left: 14px;
        }

        .page-notice .notice-list > .item.active > .title {
            color: #25aae7;
            cursor: default;
        }

        .page-notice .notice-list > .item.active .content {
            display: block;
        }

        .page-notice .notice-list > .item > .content {
            border: 1px dashed #ccc;
            margin: 0 30px 30px 30px;
            padding: 16px 20px;
            font-size: 14px;
            transition: all .3s;
            min-height: 300px;
            line-height: 32px;
            display: none;
        }

    </style>
    <div id="app-notice-box">
        <div class="head">
            <div class="name"><i class=" fa fa-lg fa-bell"></i> &nbsp;系统公告</div>
        </div>
        <div class="row main-dom">
            <div class=" page-notice">
                <div class="notice-content"
                <div class="loading"></div>
                <div class="notice-list">
                    {foreach $notice_list as $item}
                        <div class="item">
                            <div class="title">{$item.title}<span class="time">发布时间：{'Y-m-d'|date:$item.addTime}<i
                                            class="fa fa fa-arrow-circle-down"></i></span>
                            </div>
                            <div class="content">
                                <pre>
                                {$item.content}
                                </pre>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
    </div>
{literal}
    <script>
        seajs.use([""], function () {

            $(document).on('click', '.page-notice .item', function (e) {
                $(this).toggleClass("active");
            });
        });
    </script>
{/literal}
{/block}