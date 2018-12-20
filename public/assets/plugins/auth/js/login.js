var w = $(window).width(), h = $(window).height();
var canvas, context;


$(window).resize(function () {
    w = $(window).width(), h = $(window).height();
    $("#canvas").width(w);
    $("#canvas").height(h);
    var toTop = 0, a = 0, b = 0, c = 0;
    b = $(".header").height();
    c = $(".registerBox").height();
    a = h - b - 95;
    if (a - c > 40) {
        toTop = (a - c) / 2 - 5;
    } else {
        toTop = 25;
    }
    $(".registerBox").css("margin-top", toTop + "px");
});

// 回到顶部
$(function () {
    // setTimeout(() => {
    //     $("#star_wp").addClass("show");
    // }, 1000);
    setTimeout(function(){
        $("#star_wp").addClass("show");
    },1000);
});

//定位
$(function() {
    //console.log($(".registerBox").height());
    var toTop=0, a=0, b=0, c=0;
    b = $(".header").height();
    c = $(".registerBox").height();
    a = h - b - 95;
    if (a - c > 40) {
        toTop = (a - c) / 2-5;
    } else {
        toTop = 25;
    }
    $(".registerBox").css("margin-top",toTop+"px");
});

