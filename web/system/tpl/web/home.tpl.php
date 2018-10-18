<div class="banner-box">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img src="<?php echo THEME_PATH; ?>images/banner3.png" alt="">
            </div>
        </div>
        <!-- Add Pagination -->
        <div class="swiper - pagination"></div>
    </div>
    <!-- Initialize Swiper -->
    <script>
        seajs.use(["swiper", "swiperCss"], function () {
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true
            });
        });
    </script>
</div>
<div class="home-notice">
    <div class="container">
        <div class="row">
            <div class="col-xs-1 text-right">
                <img src="<?php echo THEME_PATH; ?>images/laba.png" alt="">
            </div>
            <div class="col-xs-11">
                <ul class="list-unstyled">
                    <li>
                        <a href="/public/notice">系统消息：<?php echo $this->config['webGG']; ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<style>
.center{margin: 0 auto;width: 1260px; padding:0 55px;}


.main-bg ul li{ float:left; }
.main-bg .title{ width: 230px;height: 65px; margin: 35px auto 65px;text-align: right;padding-top: 5px; }
.main-bg .body{ padding:0 30px; overflow: hidden;}
.main-bg .title h3 {padding-right: 5px; color:#143058;}
.main-bg .title p{margin-top: 10px;color: #7186a3;font-size: 19px;font-family: Arial;font-size: 8px \9;-webkit-transform: scale(0.8);-moz-transform: scale(0.6); -o-transform: scale(1); }

.game .title h3{ padding-right: 10px; }
.game .title{ background: url(<?php echo THEME_PATH; ?>images/index/main/qiao.png)no-repeat 16px 0px; } 
.game .title p{margin-left: 15px;width: 100%;}

.game .game-into{ padding:0 50px 0 80px; }
.game .game-into li{ float:left; margin-right: 20px; position: relative; overflow: hidden;}
/*.game .game-into li p{ display: none; }
*/
.game .game-into li:hover a{ display: block; }
.game .game-into li a{display: none;width: 128px;height: 44px;background: #fff;color: #25779d;line-height: 44px;text-align: center;margin: 0 auto; position: absolute;top: 190px;left: 27px;}
.game .game-into li .lott-logo{width: 182px;height: 90px;position: absolute; top:0px; background-size: 180px 90px !important;}
.game .game-into li .ssc{background: url(<?php echo THEME_PATH; ?>images/index/main/ho-ssc.png) no-repeat 0px 0px;}
.game .game-into li .klc{background: url(<?php echo THEME_PATH; ?>images/index/main/ho-klc.png) no-repeat 0px 0px;}
.game .game-into li .xy28{background: url(<?php echo THEME_PATH; ?>images/index/main/ho-xy28.png) no-repeat 0px 0px;}
.game .game-into li .real{background: url(<?php echo THEME_PATH; ?>images/index/main/ho-real.png) no-repeat 0px 0px;}
.game .game-into li .slot{background: url(<?php echo THEME_PATH; ?>images/index/main/ho-slot.png) no-repeat 0px 0px;}
.game .game-into li a:hover{ color:#25779d; }
.game .game-into img{ width:180px; height: 400px;
-webkit-transform: scale(1, 1);
-webkit-transition-timing-function: ease-out;
-webkit-transition-duration: 250ms;

-moz-transform: scale(1, 1);
-moz-transition-timing-function: ease-out;
-moz-transition-duration: 250ms; }

.game .game-into li img:hover{ 
-webkit-transform: scale(1.1);
-webkit-transition-timing-function: ease-out;
-webkit-transition-duration: 750ms;

-moz-transform: scale(1.1);
-moz-transition-timing-function: ease-out;
-moz-transition-duration: 750ms;
overflow: hidden;

}
.download .title,
.servise .title{ background: url(<?php echo THEME_PATH; ?>images/index/main/dht.png)no-repeat 0px 5px; }
.download .title{ width:200px; } 
.download .title h3{ padding-right: 32px; } 
.download ul li{ margin-right: 10px;width:218px;height: 302px;background: #fff;border:1px solid #ccc; }
.download ul li i{ display: block;width:128px;height: 128px; border-radius: 50%;margin: 30px auto 0;}
.download ul li p{ font-size: 24px;color:#333;width:100%; text-align: center;margin:20px 0; }
.download ul li a{ display: block;width:130px;height: 46px;line-height: 46px; margin: 0 auto;
 text-align:center;color: #333;border-radius: 30px;border:1px solid #ccc; background: #fff;}
.download ul .win i{background: url(<?php echo THEME_PATH; ?>images/index/main/win-ho.png)#f8f8f8 no-repeat 28px 30px;}

.download ul .mac i{background: url(<?php echo THEME_PATH; ?>images/index/main/mac-ho.png)#f8f8f8 no-repeat 36px 30px;}

.download ul .iph i{background: url(<?php echo THEME_PATH; ?>images/index/main/iph-ho.png)#f8f8f8 no-repeat 43px 30px;}

.download ul .and i{background: url(<?php echo THEME_PATH; ?>images/index/main/and-ho.png)#f8f8f8 no-repeat 41px 30px;}

.download ul .wap i{background: url(<?php echo THEME_PATH; ?>images/index/main/wap-ho.png)#f8f8f8 no-repeat 41px 30px;}
.download ul li:hover{ border:1px solid #25779d; }
.download ul li:hover p{color:#25779d;}
.download ul li:hover a{ color:#fff;background: #25779d; border:none;}


.servise .title{ background: url(<?php echo THEME_PATH; ?>images/index/main/dht.png)no-repeat 0px 5px; }
.download .title{ width:200px; } 
.download .title h3{ padding-right: 32px; } 
.servise{ position:relative; }
.servise .title p{ padding-left: 112px;font-size: 16px;}
.servise h3{letter-spacing: 4px;}
.servise .body{padding:0;}
.servise ul li{ width:33%; text-align: center;}
.servise ul li div{ width:162px; height: 162px; padding-top: 50px; color:#fff; margin: 0 auto;}
.servise ul li div span{ display:block; margin-top: 18px;} 
.servise ul li .txt { color:#a5abae; margin-top: 25px; line-height: 24px;}
.servise ul li div span strong{ font-size: 24px; }
.servise ul li h5{color:#143058; margin-top: 42px;}
.servise .ctsd div,
.servise .yhfw div{ background: url(<?php echo THEME_PATH; ?>images/index/main/lan.png) no-repeat -10px -9px; }
.servise .fhzq div{ background: url(<?php echo THEME_PATH; ?>images/index/main/hong.png) no-repeat -16px -9px; }

.promotion { margin-top: 75px; }
.promotion .title{ background: url(<?php echo THEME_PATH; ?>images/index/main/bus.png)no-repeat 0px 11px; } 
.promotion .title h3{ padding-right: 15px;}
.promotion .title p{font-size: 21px;margin-left: 6px;}


.promo-banner{ margin-top: 25px;height:380px; }
.promo-banner .ef-left{  text-align: center; padding: 110px 35px 0px;}
.promo-banner .ef-left h5{ color:#333; font-size: 16px;}
.promo-banner .ef-left p{ color:#666; margin-top: 10px; line-height: 24px; }
.promo-banner .ef-right .promo-bg{ width:800px; height: 300px;background: #e44540; position: relative; }
.promo-banner .ef-right img{ position:absolute; top:20px; right:20px; } 

.efb-g14{ width:14.285714285714%;}
.efb-g16{width:16.6666666%;}
.efb-g20{ width:20%;}
.efb-g25{ width:25%;}
.efb-g30{ width:30%;}
.efb-g33{ width:33.333%;}
.efb-g35{ width:35%; }
.efb-g65{ width:65%; }
.efb-g40{ width:40%; }
.efb-g50{ width:50%;}
.efb-g60{ width: 60%;}
.efb-g66{ width:66.666%;}
.efb-g70{ width:70%;}
.efb-g75{ width:75%;}
.efb-g80{ width:80%;}
.efb-g100{ width:100%;}
.ym-left{ float:left; }
.ym-right{ float:right; }
.ef-left{ float: left;}
.ef-right{ float: right;}
.ym-gl {float: left;margin: 0;}
.ym-gr {float: right;margin: 0 0 0 -5px;}
</style>
<div class="main-bg">
<div class="center">
			<div class="game">
				<div class="title">
					<h3>游戏大厅</h3>
					<p>Games&nbsp;Center</p>
				</div>
				<ul class="game-into overhd" style="height: 400px;">
                
					  <li class="ssc"><div><img src="<?php echo THEME_PATH; ?>images/index/main/in-ssc.jpg" alt="#"><p class="lott-logo ssc"></p><p class="into-game"><a href="/game/index?id=1">进入游戏</a></p></div></li>
					  <li class="klc"><div><img src="<?php echo THEME_PATH; ?>images/index/main/in-klc.jpg" alt="#"><p class="lott-logo klc"></p><p class="into-game"><a class="future-open" href="javascript:;">进入游戏</a></p></div></li>
					<li class="xy28"><div><img src="<?php echo THEME_PATH; ?>images/index/main/in-xy28.jpg" alt="#"><p class="lott-logo xy28"></p><p class="into-game"><a target="future-open" href="javascript:;">进入游戏</a></p></div></li>
					<li class="zryl"><div><img src="<?php echo THEME_PATH; ?>images/index/main/in-zryl.jpg" alt="#"><p class="lott-logo real"></p><p class="into-game"><a class="future-open" href="javascript:;">进入游戏</a></p></div></li>
					<li class="slot"><div><img src="<?php echo THEME_PATH; ?>images/index/main/in-slot.jpg" alt="#"><p class="lott-logo slot"></p><p class="into-game"><a class="future-open" href="javascript:;">进入游戏</a></p></div></li>
					<!-- <li class="slot"><div><img src="/Public/Home/images/main/in-ssc.jpg" alt="#"><p class="lott-logo ssc"></p><p class="into-game"><a href="view/game/game.html">进入游戏</a></p></div></li> -->
					
				</ul>
			</div>
			<div class="download">
				<div class="title">
					<h3>下载中心</h3>
					<p>Download&nbsp;Center</p>
				</div>
				<ul class="ul-flex" style="height: 302px;">
					<li class="win"  style="display:none"><i></i><p>Windows版</p><a href="<?php echo $this->config['xzurl_Windows']?$this->config['xzurl_Windows']:'javascript:;'; ?>" target="_blank">立即下载</a></li>
					<li class="mac"><i></i><p>Mac版</p><a href="<?php echo $this->config['xzurl_Mac']?$this->config['xzurl_Mac']:'javascript:;'; ?>" target="_blank">立即下载</a></li>
					<li class="iph"><i></i><p>iPhone版</p><a href="<?php echo $this->config['xzurl_iPhone']?$this->config['xzurl_iPhone']:'javascript:;'; ?>" target="_blank">立即下载</a></li>
					<li class="and"><i></i><p>Android版</p><a href="<?php echo $this->config['xzurl_Android']?$this->config['xzurl_Android']:'javascript:;'; ?>" target="_blank">立即下载</a></li>
					<li class="wap"  style="display:none"><i></i><p>手机Wap版</p><a href="<?php echo $this->config['xzurl_Wap']?$this->config['xzurl_Wap']:'javascript:;'; ?>" target="_blank" style="display:none">立即下载</a></li>
				</ul>
			</div>
			<div class="servise">
				<div class="title">
					<h3>服务优势</h3>
					<p>Service&nbsp;Advantage</p>
				</div>
				<ul class="body ul-flex" style="height: 294px;">
					<li class="ctsd"><div><p>充提速度</p><span><strong>1</strong>分钟</span></div><h5>存取款方便快捷</h5><p class="txt">为玩家提供一系列安全、可靠、便捷
					的服务，充提最快1分钟即可到账</p></li>
					<li class="fhzq"><div><p>分红周期</p><span><strong>1</strong>天</span></div><h5>高额返点/返水</h5><p class="txt">本平台集合全球最流行10大在线娱
					乐平台，高效、高速的分红周期</p></li>
					<li class="yhfw"><div><p>银行服务</p><span><strong>34</strong>家</span></div><h5>便捷的银行服务</h5><p class="txt">支持国内32家主流银行，让您充值
					提现不存在任何烦恼</p></li>
				</ul>
				<div class="jindu">
					<div id="chart1" class="plan1 plan" data-percent="80">
	                </div>
	                <div id="chart2" class="plan2 plan" data-percent="70">
	                </div>
	                <div id="chart3" class="plan3 plan" data-percent="75">
	                </div>
                </div>
			</div>
			<div class="promotion">
				<div class="title">
					<h3>优惠活动</h3>
					<p>Promotions</p>
				</div>
				<div class="promo-banner overhd">
					<div class="ef-left efb-g30">
						<h5>全民英雄逆袭之战</h5>
						<p>活动时间：2016／06／02－2016／07／02
							playtech狂潮，一个新时代的崛起，狂送3重
							奖，共两行活动文字简介...</p>
					</div>
					<div class="ef-right efb-g70">
						<div class="promo-bg">
						<a href="javascript:;"><img src="<?php echo THEME_PATH; ?>images/index/main/pro.jpg" alt="#"></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
<!--<div id="home" class="container">
    <div class="row text-center home-title">
        <h1><?php echo $this->config['webName']; ?>为您提供专业游戏平台</h1>
    </div>
    <div class="row home-show-box">
        <div class="col-xs-3">
            <div class="home-head-small">
                <img src="<?php echo THEME_PATH; ?>images/index/index-1-1.png" alt="">
            </div>
            <div class="home-head">
                <img src="<?php echo THEME_PATH; ?>images/index/index-1.jpg" alt="">
            </div>
            <div class="go-in-box">
                <div class="go-in">
                    <a href="/game/index?id=1">
                        <label for="">进入游戏</label>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xs-3">
            <div class="home-head-small">
                <img src="<?php echo THEME_PATH; ?>images/index/index-2-2.png" alt="">
            </div>
            <div class="home-head">
                <img src="<?php echo THEME_PATH; ?>images/index/index-2.jpg" alt="">
            </div>
            <div class="go-in-box">
                <div class="go-in">
                    <a href="#">
                        <label for="">进入游戏</label>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xs-3">
            <div class="home-head-small">
                <img src="<?php echo THEME_PATH; ?>images/index/index-3-3.png" alt="">
            </div>
            <div class="home-head">
                <img src="<?php echo THEME_PATH; ?>images/index/index-3.jpg" alt="">
            </div>
            <div class="go-in-box">
                <div class="go-in">
                    <a href="#">
                        <label for="">进入游戏</label>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xs-3">
            <div class="home-head-small">
                <img src="<?php echo THEME_PATH; ?>images/index/index-4-4.png" alt="">
            </div>

            <div class="home-head">
                <img src="<?php echo THEME_PATH; ?>images/index/index-4.jpg" alt="">
            </div>
            <div class="go-in-box">
                <div class="go-in">
                    <a href="#">
                        <label for="">敬请期待</label>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>-->
<script type="text/javascript">
    seajs.use(["layer", "layerCss"], function () {
        $("img").attr("draggable", false);
        $(document).on('click', ".ajax-bet-info", function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $.get(url, function (ret) {
                layer.open({content: ret.msg});
            })
        });
        $("#home .home-show-box .col-xs-3").mouseenter(function () {
            $(this).find(".go-in a").addClass("on");
        });
        $("#home .home-show-box .col-xs-3").mouseleave(function () {
            $(this).find(".go-in a").removeClass("on");
        });
    });
    $(function () {

    });
</script>