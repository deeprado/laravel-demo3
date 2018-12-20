@extends("layouts.star")
@section("title","欢迎")
@section("css")
    <link rel="stylesheet"
          href="{{ asset('assets/plugins/auth/css/jquery.fullPage.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/auth/css/jquery.style4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/auth/css/baraja.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/auth/css/css.css') }}">
    <script src="{{ asset('assets/plugins/auth/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/plugins/auth/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/auth/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/auth/js/jquery.fullPage.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/auth/js/jquery.js') }}"></script>
    <script>
        $(function () {
            $('#dowebok').fullpage({
                sectionsColor: ['transparent', 'transparent', 'transparent', 'transparent'],
                navigation: false,
                css3: true,
                scrollOverflow: true,
                // navigationPosition:'right',
                // navigationColor:'#fff',
                afterLoad: function (anchorLink, index) {
                    $(".pageBody" + index).addClass('selected');
                    if (index == 1) {
                        $(".head").addClass('selected');
                    }
                },
                afterRender: function () {
                    $(".section2").css("opacity", "1");
                    $(".section3").css("opacity", "1");
                    $(".section4").css("opacity", "1");
                    $(".section5").css("opacity", "1");
                    $(".section6").css("opacity", "1");
                    setTimeout(function () {
                        $(".pageBody1").addClass('selected');
                    }, 4000);
                },
                onLeave: function (index) {
                    $(".pageBody" + index).removeClass('selected');
                }
            });

            function autoScrolling() {
                var $ww = $(window).width();
                if ($ww < 650) {
                    //$.fn.fullpage.setAutoScrolling(false);
                    $("#dowebok").fullpage({
                        scrollOverflow: true
                    });
                } else {
                    //$.fn.fullpage.setAutoScrolling(true);
                    // $("#dowebok").fullpage({
                    //         scrollOverflow: false
                    //  });
                }
            }

        });
    </script>
    <style>
        body {
            background-color: #0e0e0e;
        }
    </style>
@endsection

@section("js")
    <script src="{{ asset('assets/plugins/auth/js/home-1.js') }}"></script>
    <script src="{{ asset('assets/plugins/auth/js/modernizr.js') }}"></script>
    <script src="{{ asset('assets/plugins/auth/js/jquery.baraja.js') }}"></script>
    <script src="{{ asset('assets/plugins/auth/js/jquery.appear.js') }}"></script>

@endsection

@section("content")
    <div id="dowebok" style="z-index:99;">
        <div class="section section1">
            <div class="wrap" id="wrap">
                <div class="wrapper">
                    <div class="header">
                        <div class="head clearfix main">
                            <div class="logo-box">
                                <a href="{{ route('welcome') }}" class="logo-link">
                                    <img src="{{ asset('assets/plugins/auth/img/logo.png') }}"
                                         alt="">
                                    <span></span>
                                </a>
                            </div>
                            <div class="nav_box" id="nav_box">
                                <div class="nav-2">
                                    @guest
                                        {{--<a href="javascript:void(0);" class="re-lo">注册</a>--}}
                                        <a href="{{ route('login') }}" class="nav-a">登录</a>
                                        <span class="nav-span">|</span>

                                    @else
                                        <a href="{{ route('manager') }}">进入管理中心</a>
                                        <span class="nav-span">|</span>
                                    @endguest
                                    <a href="javascript:;" class="nav-a">视频介绍</a>

                                </div>
                                <span class="ic_line" style="display: none; left: 176px; width: 64px;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="pageBody1 pageBody">
                        <h1>企业信息查询</h1>
                        <div class="page1-searchBox">
                            <input type="text" placeholder="请输入公司名称、人名、品牌名称等关键词"/>
                            <a></a>
                        </div>
                    </div>
                    <div class="star_wp scale_box" id="star_wp">
                        <span class="star_bg layer" data-depth="0.8"></span>
                        <span class="star_box layer" data-depth="1.00"></span>
                    </div>

                </div>
            </div>
        </div>

        <div class="section section5">
            <div class="pageBody5 pageBody main clearfix" id="page5">
                <div class="clearfix">
                    <div class="page5-left">
                        <img src="{{ asset('assets/plugins/auth/img/code.png') }}">
                    </div>
                    <div class="page5-right">
                        <h1>CONTACT US</h1>
                        <h2>联系我们<span>尊敬的客户，我们24小时竭诚为您服务</span></h2>
                        <ul>
                            <li>
                                <img src="{{ asset('assets/plugins/auth/img/message.png') }}">
                                <span>业务咨询：</span>
                            </li>
                            <li>
                                <img src="{{ asset('assets/plugins/auth/img/code.png') }}
                                        https://xsq-static.oss-cn-beijing.aliyuncs.com/assets/plugins/auth/img/www.png">
                                <span>官方网址：www.xxx.com</span>
                            </li>
                            <li>
                                <img src="{{ asset('assets/plugins/auth/img/mail.png') }}">
                                <span>service@xxxx.com</span>
                            </li>
                            <li>
                                <img src="{{ asset('assets/plugins/auth/img/address.png') }}">
                                <span>哈尔滨市道里区</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="footer main">
                    <a class="rocket"><img
                                src="https://xsq-static.oss-cn-beijing.aliyuncs.com/assets/plugins/auth/img/rocket.png"></a>
                    <div class="ban"><span class="footer-1">备案号： </span><span class="footer-2">版权所有：</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection