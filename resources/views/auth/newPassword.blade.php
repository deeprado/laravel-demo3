@extends('layouts.star')

@section("title",'输入新密码')
@section('css')
    <link rel="stylesheet" href="https://xsq-static.oss-cn-beijing.aliyuncs.com/assets/plugins/auth/css/css.css">
    <script src="{{ asset('assets/plugins/auth/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/plugins/auth/js/login.js') }}"></script>
    <style>
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
                <div class="re-title">输入新密码</div>
                @if ($errors->any())
                    <div class="error">
                            <span class="invalid-feedback" role="alert">
                                 <strong>{{ $errors->first() }}</strong>
                            </span>
                    </div>
                @endif
                <form method="post">
                    @csrf
                    <input type="password" class="re-input" id="password" name="password" value="" placeholder="请输入新密码">
                    <input type="password" class="re-input" name="password_confirmation" value="" placeholder="请输入确认密码">
                    <button type="submit" class="submitBtn">确认</button>
                </form>
            </div>
        </div>
    </div>
@endsection
