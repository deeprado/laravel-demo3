@extends('layouts.star')

@section("title",'登录')
@section('css')
    <link rel="stylesheet" href="https://xsq-static.oss-cn-beijing.aliyuncs.com/assets/plugins/auth/css/css.css">
    <script src="{{ asset('assets/plugins/auth/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/plugins/auth/js/login.js') }}"></script>
    <script src="{{ asset('js/gt.js') }}"></script>
    <style>

        #embed-captcha{
            margin: 0 auto;
            min-height: 14px;
        }

        #embed-captcha .geetest_holder {
            width: 85.6% !important;
            background-color: #fff;
            font-size: 14px;
            color: #a0a0a0;
            border: none;
            border-radius: 2px;
            margin-top: 20px;
            margin-left: 7.25%;
        }

        #wait{
            height: 40px;
            margin-left: 7.25%;
            width: 85.6% !important;
            line-height: 40px;
            text-align: left;
            background-color: white;
        }

        .show {
            display: block;
            color: red;
        }

        .hide {
            display: none;
        }

        #notice {
            color: red;
            text-align: left;
            margin-top: 10px;
            margin-left: 16%;
        }

        .error {
            display: block;
            color: red;
            text-align: left;
            margin-top: 10px;
            margin-left: 16%;
        }

        strong {
            font-weight: 800;
        }
    </style>
@endsection

@section("js")
    <script>
        var handlerEmbed = function (captchaObj) {
            $("#embed-submit").click(function (e) {
                var validate = captchaObj.getValidate();
                if (!validate) {
                    $("#notice")[0].className = "show";
                    setTimeout(function () {
                        $("#notice")[0].className = "hide";
                    }, 2000);
                    e.preventDefault();
                }
            });
            captchaObj.appendTo("#embed-captcha");
            captchaObj.onReady(function () {
                $("#wait")[0].className = "hide";
            });
        };

        $.ajax({
            url: "{{ route('gee.init') }}" + '?t=' + (new Date()).getTime(),
            type: "get",
            dataType: "json",
            success: function (data) {
                initGeetest({
                    gt: data.gt,
                    challenge: data.challenge,
                    new_captcha: data.new_captcha,
                    product: "embed",
                    offline: !data.success
                }, handlerEmbed);
            }
        });
    </script>
@endsection

@section('content')
    <div class="register-wrapper" style="z-index:99;">
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
        <div class="registerBox">
            <div class="register-con">
                <div class="re-title">用户登录</div>
                <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                    @csrf
                    <div class="login-item">
                    <span class="login-icon">
                        <img src="https://xsq-static.oss-cn-beijing.aliyuncs.com/assets/plugins/auth/img/telIcon.png">
                        <span class="login-line"></span>
                    </span>
                        <input type="text" class="login-input" id="phone" name="phone" value="{{ old('phone') }}"
                               placeholder="请输入手机号">
                        @if ($errors->has('phone'))
                            <div class="error">
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                            </div>
                        @endif
                    </div>
                    <div class="login-item">
                    <span class="login-icon">
                        <img src="https://xsq-static.oss-cn-beijing.aliyuncs.com/assets/plugins/auth/img/codeIcon.png">
                        <span class="login-line"></span>
                    </span>
                        <input type="password" class="login-input" id="password" name="password" placeholder="请输入密码">
                    </div>
                    {{--<div class="login-item">--}}
                    {{--<div id="embed-captcha"></div>--}}
                    {{--<p id="wait" class="show">&nbsp;&nbsp;&nbsp;&nbsp;正在加载验证码......</p>--}}
                    {{--<p id="notice" class="hide">请先完成验证</p>--}}
                    {{--</div>--}}

                    <div class="login-item">
                        <div id="embed-captcha"></div>
                        <p id="wait" class="show">&nbsp;&nbsp;&nbsp;&nbsp;正在加载验证码......</p>
                        <p id="notice" class="hide">请先完成验证</p>
                    </div>


                    <button type="submit" class="submitBtn login-btn" id="embed-submit">登录</button>
                </form>
                <div class="login-item login-a">
                    {{--<a href="javascript:void(0)">免费注册</a>--}}
                    <a href="{{ route("auth.forgot") }}">忘记密码</a>
                </div>
            </div>
        </div>
    </div>
@endsection
