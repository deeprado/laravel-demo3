var w = $(window).width(), h = $(window).height();
$(function () {
    w = $(window).width(), h = $(window).height();

    if (w > 1024) {
        checkBrowser();
    }

    $(".tipBox").css("margin-top", parseInt($(window).height() - 320) / 2 + "px");
    $(window).resize(function () {
        $("#browser_tips").css("margin-top", parseInt($(window).height() - 320) / 2 + "px");
    });

});

$(window).resize(function () {
    w = $(window).width(), h = $(window).height();
    $("#canvas").width(w);
    $("#canvas").height(h);
});

//浏览器版本检测
function checkBrowser() {
    var strr = '<div class="loadmask_browser" id="loadmask_browser">' +
        '<div class="tipBox">' +
        '<h1>您的浏览器版本过低，本页面的显示效果可能有差异。</h1>' +
        '<h2>建议您升级到IE10+或更换以下浏览器:</h2>' +
        '<p><a class="oldbrowser__browserLink" title="Download Google Chrome" style="background-position: 0px 0px;" href="https://chrome.en.softonic.com/?ex=BB-682.0" target="_blank"></a>' +
        '<a class="oldbrowser__browserLink" title="Download Mozilla Firefox" style="background-position: -60px 0px;" href="http://www.firefox.com.cn/" target="_blank"></a>' +
        '<a class="oldbrowser__browserLink" title="Download Opera" style="background-position: -120px 0px;" href="http://www.opera.com/download" target="_blank"></a>' +
        '<a class="oldbrowser__browserLink" title="Download Safari" style="background-position: -180px 0px;" href="https://www.apple.com/safari/" target="_blank"></a>' +
        '<a class="oldbrowser__browserLink" title="Download Internet Explorer" style="background-position: -240px 0px;" href="https://support.microsoft.com/zh-cn/help/17621/internet-explorer-downloads" target="_blank"></a>' +
        '</p>' +
        '<button class="btn_browser" onclick="browser_btn()">确定</button>' +
        '</div>' +
        '</div>';
    var userAgent = navigator.userAgent,
        rMsie = /(msie\s|trident.*rv:)([\w.]+)/,
        rFirefox = /(firefox)\/([\w.]+)/,
        //    rOpera = /(opera).+version\/([\w.]+)/,
        rChrome = /(chrome)\/([\w.]+)/,
        rSafari = /version\/([\w.]+).*(safari)/;
    var browser;
    var version;
    var ua = userAgent.toLowerCase();

    function uaMatch(ua) {
        var match = rMsie.exec(ua);
        if (match != null) {
            return {browser: "IE", version: match[2] || "0"};
        }
        var match = rFirefox.exec(ua);
        if (match != null) {
            return {browser: match[1] || "", version: match[2] || "0"};
        }
        var match = rChrome.exec(ua);
        if (match != null) {
            return {browser: match[1] || "", version: match[2] || "0"};
        }
        var match = rSafari.exec(ua);
        if (match != null) {
            return {browser: match[2] || "", version: match[1] || "0"};
        }
        if (match != null) {
            return {browser: "", version: "0"};
        }
    }

    var browserMatch = uaMatch(userAgent.toLowerCase());
    if (browserMatch.browser) {
        browser = browserMatch.browser;
        version = browserMatch.version;
    }
    if (browser == "IE" && version != 10.0 && version != 11.0) {
        //$("#loadmask_browser").css("display", "block");
        $('body').append(strr);
    }
    //alert(browser + version)
    //if (browser != "IE" && browser != "firefox" && browser != "chrome" && browser != "Safari") {
    //    return "对不起，本网站仅支持IE、Firefox、Chrome、Safari，请重新选择浏览器！"
    //} else if (browser == "IE" && version != 8.0 && version != 9.0 && version != 10.0 && version != 11.0) {
    //    return "对不起，本网站仅支持IE8以上的浏览器版本，请升级浏览器版本或选择其他浏览器！"
    //} else {
    //    return 'ok'
    //}
}

function browser_btn() {
    $("#loadmask_browser").remove();
}


//page4  扑克牌效果
$(document).ready(function () {
    'use strict';
    var $el = $('#card-ul'),
        sectionFeature = $('#section-feature'),
        baraja = $el.baraja();

    if ($(window).width() > 540) {
        sectionFeature.appear(function () {
            baraja.fan({
                speed: 1500,
                easing: 'ease-out',
                range: 75,
                direction: 'right',
                origin: {x: 50, y: 200},
                center: true
            });
        });
        $('#feature-expand').click(function () {
            baraja.fan({
                speed: 500,
                easing: 'ease-out',
                range: 75,
                direction: 'right',
                origin: {x: 50, y: 200},
                center: true
            });
        });
    } else {
        sectionFeature.appear(function () {
            baraja.fan({
                speed: 1500,
                easing: 'ease-out',
                range: 40,
                direction: 'left',
                origin: {x: 200, y: 50},
                center: true
            });
        });
        $('#feature-expand').click(function () {
            baraja.fan({
                speed: 500,
                easing: 'ease-out',
                range: 40,
                direction: 'left',
                origin: {x: 200, y: 50},
                center: true
            });
        });
    }

    // Feature navigation
    $('#feature-prev').on('click', function (event) {
        baraja.previous();
    });

    $('#feature-next').on('click', function (event) {
        baraja.next();
    });

    // close Features
    $('#feature-close').on('click', function (event) {
        baraja.close();
    });
});

// 回到顶部
$(function () {
    $(".rocket").click(function () {
        $('#dowebok').fullpage.moveTo(1);
    });
    // setTimeout(() => {
    //     $("#star_wp").addClass("show");
    // }, 1000);    
    setTimeout(function () {
        $("#star_wp").addClass("show");
    }, 1000);
});

