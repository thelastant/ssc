{extends file='common/base.tpl'}
{block name="content"}
    <style>
        #app-download-box {
            background: #fff;
            min-height: 400px;
        }

        .main-dom {
            text-align: center;
            padding: 30px;
        }

        .main-dom .col-xs-6 {
            margin-bottom: 30px;
        }

        .qr-code-img {
            overflow: hidden;
            width: 100%;
            margin-top: 15px;
        }
    </style>
    <div id="app-download-box">
        <div class="head">
            <div class="name"><i class=" fa fa-lg fa-mobile"></i> &nbsp;APP下载</div>
        </div>
        <div class="row main-dom">
            <div class="col-xs-4">
                <h1><i class="fa fa-android"></i> Android端下载</h1>
                <div class="qr-code-img" id="app-android">
                </div>
            </div>
            <div class="col-xs-4">
                <h1><i class="fa fa-apple"></i> IOS客户端下载</h1>
                <div class="qr-code-img" id="app-ios">
                </div>
                <h4>
                    <a target="_blank" class="text-danger"
                       href="http://jingyan.baidu.com/article/a3f121e4c1f544fc9052bbee.html">IOS安装教程</a>
                </h4>
            </div>
            <div class="col-xs-4">
                <h1><i class="fa fa-edge"></i> WAP手机浏览</h1>
                <div class="qr-code-img" id="app-wap">
                </div>
            </div>
        </div>
    </div>
    <script>
        var TEMP = {
            android: "{$urls.android}",
            ios: "{$urls.ios}",
            wap: "{$urls.wap}",
        };
    </script>
{literal}
    <script>
        seajs.use(["qrcode"], function () {
            $("#app-android").qrcode(TEMP.android);
            $("#app-ios").qrcode(TEMP.ios);
            $("#app-wap").qrcode(TEMP.wap);
        });
    </script>
{/literal}
{/block}