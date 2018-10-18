<html>
<head>
    <title>识别二维码付款</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta name="menu" content="terminalVersion"/>
    <script src="js/jquery.min.1.7.2.js" type="text/javascript"></script>
    <script src='js/jquery.qrcode.js' type="text/javascript"></script>
    <script src='js/utf.js' type="text/javascript"></script>
</head>
<script>
    <?php
    $code = '';
    if (isset($_GET['code'])) {
        $code = $_GET['code'];
    }
    echo "qrcode_url = '$code'";
    ?>

</script>
<body style="text-align: center;background:#FFF;">
<div id="contentWrap">
    <div id="widget table-widget">
        <div class="pageTitle"></div>
        <div class="pageColumn">
            <div>
                <input id="qrcodeURL" type="hidden"/>
            </div>
            <table>
                <div id="code" style="margin-top: 100px;display: none;"></div>
                <div style="margin-top: 100px;">
                    <img id="qrcode" width='100%' src=""/>
                </div>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    if (qrcode_url == null) {

    } else {
        $("#code").qrcode({
            width: 650,
            height: 650,
            text: qrcode_url
        });
        $(function () {
            var type = "png";
            var oCanvas = document.getElementById("myCanvas");
            var imgData = oCanvas.toDataURL(type);
            var qrcode = document.getElementById("qrcode");
            qrcode.src = imgData;
        });

    }

</script>
</body>
</html>