define(function (require, exports, module) {
    exports.WindowReload = function (delay) {
        window.setTimeout(function () {
            window.location.reload();
        }, delay);
    };
    exports.Log = function (msg) {
        console.log(msg);
    }
});