<kf_service_page class="app-page">
    <header class="header-bar">
        <button class="btn icon icon-arrow-back pull-left" id="msgsback" data-navigation="$previous-page"></button>
        <div class="center">
            <h1 class="title">客服咨询</h1>
        </div>
    </header>
    <div class="content" id="mm-iframe">
        <div class="iframebox">
            <iframe src="" id="mmiframe" frameborder="0" scrolling="no"
                    style="width:100%;height:100%;"></iframe>
        </div>
        <div class="padded-full" id="loading-part">
            <br/><br/><br/>
            <div class="circle-progress active">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
    <script>
        var initMm = function () {
            if ($('#mm-iframe iframe').size() > 0) {
                $("#mmiframe").attr('src', "<?php echo $this->config["kefuGG"];?>").on('load', function () {
                    $('servicemm #servicemm-loading').hide();
                    $(this).height($(window).height());
                    setTimeout(function () {
                        $("#mmiframe").height($(window).height());
                        $('.iframebox').height($(window).height());
                        $("#mmiframe").css({"padding-bottom": "52px"});
                        $('#loading-part').hide();
                    }, 300)
                });
            }
        };
        initMm();
    </script>
</kf_service_page>