function changeVerify() {
    $("#verify-img").attr("src", "/user/get_verify?rand=" + Math.random());
}

$(function () {
    seajs.use(["layer", "layerCss"], function () {
        //提交表单
        $(document).on('submit', "form[target='ajax-form']", function (e) {
            e.preventDefault();
            var $this = $(this);
            var data = $this.serialize();
            var url = $this.attr("action");
            $.post(url, data, function (ret) {
                layer.open({content: ret.msg});
                changeVerify();
                if (ret.code === 200) {
                    setTimeout(function () {
                        window.location.href = ret.data.url;
                    }, 1200);
                }
            });
        });
    });
});