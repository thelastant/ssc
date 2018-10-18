//系统开发：209900956
var SEA_CONF = {
    theme_path: "/static/theme/web/",
    common_path: "/static/theme/common/",
    VERSION: 1.0
};
if ("undefined" !== typeof SYSTEM_SETTINGS) {
    SEA_CONF.VERSION = SYSTEM_SETTINGS.version;
}
seajs.config({
    base: "/static/",
    alias: {
        'jquery': 'libs/jquery/jquery-2.1.4.min.js',
        'lazyload': 'libs/jquery/lazyload.js',
        'fontAwesome': 'libs/font-awesome-4.6.3/css/font-awesome.min.css',
        'angular': 'libs/angular/angular.min.js',
        'layer': 'libs/layer-m/layer.js',
        'layerCss': 'libs/layer-m/need/layer.css',
        'bootstrap': 'libs/bootstrap/js/bootstrap.min.js',
        'bootstrapCss': 'libs/bootstrap/css/bootstrap.min.css',
        'toaster': 'libs/jquery/jquery.toaster.js',
        'go': 'libs/go.min.js',
        'webuploader': 'libs/webuploader/webuploader.js',
        'vue': 'libs/vue-2.0/vue.min.js',
        'swiperJQ': 'libs/swiper-3.4.1/js/swiper.jquery.min.js',
        'fullPageJs': 'libs/jquery-fullpage/jquery.fullpage.js',
        'fullPageCss': 'libs/jquery-fullpage/jquery.fullpage.css',
        'swiper': 'libs/swiper-3.4.1/js/swiper.js',
        'swiperCss': 'libs/swiper-3.4.1/css/swiper.min.css',
        'animate': 'libs/animate/animate-3.5.1.css',
        'weui': 'libs/weui/weui.min.css',
        'moment': 'libs/moment.min.js',
        'qrcode': 'libs/qrcode.min.js',
        'muiJs': 'libs/mui/js/mui.min.js',
        'muiCss': "libs/mui/css/mui.min.css",
        'sliderJs': SEA_CONF.common_path + "js/jquery.sliderbar.js",
        'sliderCss': SEA_CONF.common_path + "css/jquery.sliderbar.css",
        //common
        'apiJs': SEA_CONF.common_path + "js/api.js?version=" + SEA_CONF.VERSION,
        "publicJs": SEA_CONF.common_path + "js/public.js?version=" + SEA_CONF.VERSION,
        //js
        'jqueryPlugins': SEA_CONF.theme_path + "js/jquery.plugin.js?version=" + SEA_CONF.VERSION,
        'signJs': SEA_CONF.theme_path + "js/sign.js?version=" + SEA_CONF.VERSION,
        'gameJs': SEA_CONF.theme_path + "js/game.js?version=" + SEA_CONF.VERSION,
        'commonJs': SEA_CONF.theme_path + "js/common.js?version=" + SEA_CONF.VERSION,
        'functions': SEA_CONF.theme_path + "js/functions.js?version=" + SEA_CONF.VERSION,
        'selectJs': SEA_CONF.theme_path + "js/select.js?version=" + SEA_CONF.VERSION,
        //css
        //public
        //
        'commonCss': SEA_CONF.theme_path + "css/common.css?version=" + SEA_CONF.VERSION,
        'gameCss': SEA_CONF.theme_path + "css/game.css?version=" + SEA_CONF.VERSION,
        'signCss': SEA_CONF.theme_path + "css/sign.css?version=" + SEA_CONF.VERSION,


    },
    preload: [],
    debug: true,
    charset: 'utf-8'
});
