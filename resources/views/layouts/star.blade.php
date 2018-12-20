<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - 管理后台</title>
    <script src="http://libs.baidu.com/jquery/1.9.0/jquery.js"></script>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <style>
        * {
            touch-action: pan-y;
        }
    </style>
    @yield('css')
</head>
<body>
<canvas id="canvas" width="100%" height="900"></canvas>

@yield('content')
<div class="star_wp scale_box" id="star_wp">
    <span class="star_box layer"></span>
</div>
<script src="https://xsq-static.oss-cn-beijing.aliyuncs.com/assets/plugins/auth/js/canvas.js"></script>
{{--<script src="/assets/plugins/auth/js/canvas.js"></script>--}}
<!--[if IE 6]>
<script type="text/javascript" src="http://static.webgame.kanimg.com/com/DD_PNG_min.js"></script>
<script type="text/javascript">
    var links = document.getElementsByTagName("a");
    for (var i = 0, l = links.length; i < l; i++) {
        links[i].setAttribute("hideFocus", true);
    }
</script>
<![endif]-->
@yield('js')
</body>
</html>

