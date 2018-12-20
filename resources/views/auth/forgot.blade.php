@extends('layouts.star')

@section("title",'找回密码')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/auth/css/css.css') }}">
    <script src="{{ asset('assets/plugins/auth/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/plugins/auth/js/login.js') }}"></script>
    <script src="{{ asset('js/gt.js') }}"></script>
    <style>
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

        .show {
            display: block;
            /*width: 100%;*/
            /*height: 40px;*/
            /*background: white;*/
            /*border-radius: 2px;*/
            /*border:none;*/
            /*text-align: left;*/
            /*line-height: 40px;*/
            color: red;
            /*font-weight: 400;*/
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
            margin-left: 10%;
        }

        strong {
            font-weight: 800;
        }
        .submitBtn {
            width: 86%;
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
                } else {
                    let telephone = $("#telephone");
                    let code = $("#code");
                    if (telephone.val() === '' || telephone.val().length !== 11) {
                        alert("手机号码长度不符");
                        return false;
                    }

                    if (code.val() === '' || code.val().length !== 4) {
                        alert("验证码长度不符");
                        return false;
                    }

                    $.ajax({
                        url: "{{ route('send.check') }}",
                        type: "post",
                        data: {
                            _token: "{{ csrf_token() }}",
                            telephone: telephone.val(),
                            code: code.val()
                        },
                        success: function (d) {
                            if(d.code === 400){
                                alert(d.message);
                                return false;
                            }
                        }
                    });
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
                console.log(data);
                initGeetest({
                    gt: data.gt,
                    challenge: data.challenge,
                    new_captcha: data.new_captcha,
                    product: "embed",
                    offline: !data.success
                }, handlerEmbed);
            }
        });

        var countdown = 60;

        function settime(obj) {
            if (countdown == 0) {
                obj.attr("disabled", false);
                obj.text("发送验证码");
                countdown = 60;
                return;
            } else {
                obj.attr("disabled", true);
                obj.text("重新发送(" + countdown + ")");
                countdown--;
            }
            setTimeout(function () {
                    settime(obj)
                }
                , 1000)
        }

        $("#send").click(function () {
            let telephone = $("#telephone").val();
            if (telephone == '') {
                alert('手机号不能为空');
                return false;
            }
            $.ajax({
                url: "{{ route('send.verify') }}",
                type: "post",
                data: {
                    telephone: telephone,
                    _token: "{{ csrf_token() }}"
                },
                success: function (d) {
                    console.log(d);
                    if (d.code !== 200) {
                        alert(d.message);
                    }
                    if (d.code === 200) {
                        settime($("#send"));
                    }
                }
            });
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
                <div class="re-title">忘记密码</div>
                @if ($errors->any())
                    <div class="error">
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $errors->first() }}</strong>
                            </span>
                    </div>
                @endif
                <form method="POST" action="{{ route('auth.forgot') }}">
                    @csrf
                    <input type="text" class="re-input" id="telephone" name="telephone" value="" placeholder="请输入手机号">
                    @if ($errors->has('phone'))
                        <div class="error">
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        </div>
                    @endif
                    <div class="re-item">
                        <div id="embed-captcha"></div>
                        <p id="wait" class="show">&nbsp;&nbsp;&nbsp;&nbsp;正在加载验证码......</p>
                        <p id="notice" class="hide">请先完成验证</p>
                    </div>
                    <div class="re-item">
                        <input type="text" class="re-input re-co-input" id="code" name="code" placeholder="请输入验证码">
                        <a class="re-btn2" id="send">获取验证码</a>
                    </div>
                    <button type="submit" class="submitBtn" id="embed-submit">下一步</button>
                </form>
            </div>
        </div>
    </div>
@endsection
