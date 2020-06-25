<?php
namespace tcwei\smallTools;
/**
 * Class ArticleImgLazyload 使文章中的图片进行懒加载，看不到的图片不加载，节省带宽
 * @package tcwei\smallTools 使用 composer 命令安装：composer require tcwei/imglazyload
 */
class ArticleImgLazyload{

    /**
     * @var 图片前缀，APP环境下需要使用，如果是网站读取可忽略该属性
     */
    public $imgPrefix = null;
    /**
     * @var int，毫秒单位，是否延迟加载图片，如当图片出现在视野内时多少毫秒后进行加载图片，默认是0：立即加载
     */
    public $timeLazyload = 0;

    /**
     * @var string：URL路径，未加载完成图片时，默认显示的图片；
     */
    public $defaultImg = 'http://image2.sina.com.cn/blog/tmpl/v3/images/default_s_bmiddle.gif';

    /**
     * @var int，单位PX即将可见时便进行触发预加载，timeLazyload毫秒后进行加载图片，默认是距离可见区域100PX内就触发
     */
    public $distance = 100;

    /**
     * @var string，不参与懒加载的图片地址关键词，例如传入baidu，即代表图片地址含有baidu的都不会进行懒加载，如一些编辑器的表情图
     */
    public $blacklist = '';

    public function getNewContent($content){

        $timeLazyload = $this->timeLazyload;
        $defaultImg = $this->defaultImg;
        $distance = (int)($this->distance);
        $blacklist = $this->blacklist;
        $imgPrefix = $this->imgPrefix;
        if($this->blacklist) {
            $content = preg_replace("/<img(.*)src=('|\")(.*$blacklist.*)('|\")/isU", '<img $1 srcno="$3"', $content);
        };
        $newContent = preg_replace("/<img(.*)src=('|\")(.*)('|\")/isU", '<img $1 class="tcweiLazyload" data-original="$3" src="'.$defaultImg.'"', $content);
        if($this->blacklist) {
            $newContent = preg_replace('/<img(.*)srcno="(.*)"/isU', '<img $1 src="$2"', $newContent);
        };
        if($imgPrefix != null){
            $newContent = preg_replace('/<img(.*)data-original="([^(http)])(.*)(")/isU','<img $1 data-original="'.$imgPrefix.'$2$3$4', $newContent);
        }
        //写JS代码
        $newContent .= '
              <script>
                if(typeof IntersectionObserver === "function"){
                    //监听支持APP模式的webview
                    var tcweiObserver = new IntersectionObserver(function(changes){
                        for(var i=0;i<changes.length;i++){
                                if(changes[i].intersectionRatio>0){
                                    (function(changesA, thisi){
                                        setTimeout(function(){
                                            var dataOriginalSrc = changesA[thisi].target.getAttribute("data-original");
                                            changesA[thisi].target.setAttribute("src", dataOriginalSrc);
                                            tcweiObserver.unobserve(changesA[thisi].target)
                                        }, '.$timeLazyload.');
                                    })(changes, i);
                                };
                        }
                    },  {threshold: [0, 0.5, 1],rootMargin: "'.$distance.'px 0px"}
                    );
                    var tcweiLazyloadListItems = document.querySelectorAll(".tcweiLazyload");
                    tcweiLazyloadListItems.forEach(function(item){tcweiObserver.observe(item);});
                }else{
                    //浏览器基本已支持，若不支持的浏览器不考虑太多，浏览器内核过旧，使用JQ处理恢复正常图片路径，若无JQ则放弃
                    var tcweiLazyloadClock = true; //节流锁，已取消节流
                    var oneLoadImgClock = true;
                    try {
                        function notIntersectionObserver() {tcweiLazyloadStart();if(oneLoadImgClock){oneLoadImgClock=false;setTimeout(function(){tcweiLazyloadStart();},100);}}
                        function tcweiLazyloadStart(){
                            try{$(".tcweiLazyload").not("[data-isLoading]").each(function () {if (isShow($(this))) {loadImg($(this));}})}catch (e) {setTimeout(function(){notIntersectionObserver();},200);}
                        }
                        function isShow(thisNode){return thisNode.offset().top <= $(window).height()+$(window).scrollTop();}
                        function loadImg(thisImg){thisImg.attr("src", thisImg.attr("data-original"));thisImg.attr("data-isLoading",1);}
                        $(window).on("scroll",function(){
                            if(tcweiLazyloadClock){tcweiLazyloadClock = false;tcweiLazyloadStart();tcweiLazyloadClock = true;/*由于此处代码针对低版本ie，加时间锁会出现图片不支持加载的BUG，这里不节流*/} ;
                        });
                        notIntersectionObserver();
                    } catch (e) {setTimeout(function(){notIntersectionObserver();},200);console.log("无JQ环境，每等待200毫秒后继续尝试执行");}
                }
            </script>
        ';
        return $newContent;
    }
}



