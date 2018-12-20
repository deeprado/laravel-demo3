<?php
function menu_father($controller = '')
{
    $url = explode('/', request()->route()->uri);
    $action = !isset($url[1]) ? null : $url[1];
    return $action == $controller;
}

function menu_child($route = '')
{
    return request()->route()->getName() == $route;
}

?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="utf-8"/>
    <title>@yield('title') - 销售圈CRM管理后台</title>

    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link rel="shortcut icon" href="/favicon.ico" />
    <!-- bootstrap & fontawesome -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/font-awesome/4.5.0/css/font-awesome.min.css')}}"/>

    <!-- page specific plugin styles -->

    <!-- text fonts -->
    <link rel="stylesheet" href="{{asset('assets/css/fonts.googleapis.com.css')}}"/>

    <!-- ace styles -->
    <link rel="stylesheet" href="{{asset('assets/css/ace.min.css')}}" class="ace-main-stylesheet" id="main-ace-style"/>

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{asset('assets/css/ace-part2.min.css')}}" class="ace-main-stylesheet"/>
    <![endif]-->
    <link rel="stylesheet" href="{{asset('assets/css/ace-skins.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/ace-rtl.min.css')}}"/>

    <!--[if lte IE 9]>
    <link rel="stylesheet" href="{{asset('assets/css/ace-ie.min.css')}}"/>
    <![endif]-->

    <!-- inline styles related to this page -->
@yield('css')
<!-- ace settings handler -->
    <script src="{{asset('assets/js/ace-extra.min.js')}}"></script>

    <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

    <!--[if lte IE 8]>
    <script src="{{asset('assets/js/html5shiv.min.js')}}"></script>
    <script src="{{asset('assets/js/respond.min.js')}}"></script>
    <![endif]-->
    <style>
        .handbook{
            background-color: #438EB9 !important;
        }
        .handbook:hover{
            background-color: #2E6589 !important;
        }
    </style>
    @yield('head')
</head>

<body class="no-skin">
<div id="navbar" class="navbar navbar-default          ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>
        </button>

        <div class="navbar-header pull-left">
            <a href="{{route('manager')}}" class="navbar-brand">
                <small>
                    <img src="/image/logo.png" alt="">
                    <span>销售圈CRM管理后台</span>
                </small>
            </a>
        </div>

        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <li class="nav-item">
                    <a href=" " class="handbook">
                        <i class="ace-icon fa fa-download"></i>
                        操作手册
                    </a>
                </li>
                <li class="light-blue dropdown-modal" style="border: 0;">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <img class="nav-user-photo" src="{{ Auth::user()->avatar }}"
                             alt="Jason's Photo"/>
                        <span class="user-info">
									<small>欢迎使用,</small>
                            {{ Auth::user()->name }}
								</span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>

                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        {{--<li>--}}
                            {{--<a href="#">--}}
                                {{--<i class="ace-icon fa fa-cog"></i>--}}
                                {{--系统设置--}}
                            {{--</a>--}}
                        {{--</li>--}}

                        {{--<li>--}}
                            {{--<a href="profile.html">--}}
                                {{--<i class="ace-icon fa fa-user"></i>--}}
                                {{--个人资料--}}
                            {{--</a>--}}
                        {{--</li>--}}

                        {{--<li class="divider"></li>--}}
                        <li>
                            <a href=" " class="dropdown-item">
                                <i class="ace-icon fa fa-key"></i>
                                修改密码
                            </a>
                        </li>
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                <i class="ace-icon fa fa-power-off"></i>
                                {{ __('退出登录') }}
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div><!-- /.navbar-container -->
</div>

<div class="main-container ace-save-state" id="main-container">
    <script type="text/javascript">
        try {
            ace.settings.loadState('main-container')
        } catch (e) {
        }
    </script>

    <div id="sidebar" class="sidebar                  responsive                    ace-save-state">
        <script type="text/javascript">
            try {
                ace.settings.loadState('sidebar')
            } catch (e) {
            }
        </script>

        <div class="sidebar-shortcuts" id="sidebar-shortcuts">
            <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
                <button class="btn btn-success">
                    <a href="{{ url("/manager/chart/person") }}" style="color:#fff"><i class="ace-icon fa fa-signal"></i></a>
                </button>

                <button class="btn btn-info">
                    <a href=" " style="color:#fff"><i class="ace-icon fa fa-desktop"></i></a>
                </button>

                <button class="btn btn-warning">
                    <a href=" " style="color:#fff"><i class="ace-icon fa fa-user"></i></a>
                </button>

                <button class="btn btn-danger">
                    <a href="" style="color:#fff"><i class="ace-icon fa fa-cogs"></i></a>
                </button>
            </div>

            <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
                <span class="btn btn-success"></span>

                <span class="btn btn-info"></span>

                <span class="btn btn-warning"></span>

                <span class="btn btn-danger"></span>
            </div>
        </div><!-- /.sidebar-shortcuts -->

        <ul class="nav nav-list">
            <li class="@if(request()->route()->uri === '/') active @endif">
                <a href="{{route('manager')}}">
                    <i class="menu-icon fa fa-home"></i>
                    <span class="menu-text"> 首页 </span>
                </a>

                <b class="arrow"></b>
            </li>
            <li class="{{ menu_father('base_info') ? 'active open' :''}}">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-bars"></i>
                    <span class="menu-text">基本信息</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <ul class="submenu">
                    <li class="{{ menu_child('MainPage') ? 'active' : ''}}">
                        <a href="{{url('manager/main_page')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            基本配置
                        </a>
                    </li>
                    <li class="{{ menu_child('logo') ? 'active' : ''}}">
                        <a href=" ">
                            <i class="menu-icon fa fa-caret-right"></i>
                            企业LOGO
                        </a>
                    </li>
                    <li class="{{ menu_child('PromotionPage') ? 'active' : ''}}">
                        <a href="{{url('manager/promotion_page')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            宣传页列表
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ menu_father('customer') ? 'active open' :''}}">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-cubes"></i>
                    <span class="menu-text">客户管理</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <ul class="submenu">
                    <li class="{{ menu_child('Customer') ? 'active' : ''}}">
                        <a href="{{url('manager/customer')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            客户列表
                        </a>
                    </li>
                </ul>
                <ul class="submenu">
                    <li class="{{ menu_child('CustomerContact') ? 'active' : ''}}">
                        <a href="{{url('manager/customer_contact')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            客户联系人列表
                        </a>
                    </li>
                </ul>
                <ul class="submenu">
                    <li class="{{ menu_child('Customer') ? 'active' : ''}}">
                        <a href="{{url('manager/customer/shared')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            公海客户
                        </a>
                    </li>
                </ul>
                <ul class="submenu">
                    <li class="{{ menu_child('Customer') ? 'active' : ''}}">
                        <a href="{{url('manager/shared_record')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            共享记录
                        </a>
                    </li>
                </ul>
                <ul class="submenu">
                    <li class="{{ menu_child('SearchHistory') ? 'active' : ''}}">
                        <a href="{{url('manager/search_history')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            搜索记录
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ menu_father('notice') || menu_father('notify') ? 'active open' :''}}">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-bullhorn"></i>
                    <span class="menu-text">消息管理</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <ul class="submenu">
                    <li class="{{ menu_child('Notice') ? 'active' : ''}}">
                        <a href="{{url('manager/notice')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            公告管理
                        </a>
                    </li>
                    <li class="{{ menu_child('Notify') ? 'active' : ''}}">
                        <a href="{{url('manager/notify')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            通知管理
                        </a>
                    </li>
                    {{--
                    <li class="{{ menu_child('SharedRecord') ? 'active' : ''}}">
                        <a href="{{url('manager/shared_record/push_records')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            共享消息管理
                        </a>
                    </li>
                    --}}
                </ul>

            </li>
            <li class="">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-cart-plus"></i>
                    <span class="menu-text">订单管理</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <ul class="submenu">
                    <li class="">
                        <a href="{{url('manager/order')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            成单管理
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('manager/order/check')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            回款审核
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('manager/order/orderDone')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            完成审核
                        </a>
                    </li>
                    <!--
                    <li class="">
                        <a href="{{--url('manager/corporation_package')--}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            套餐管理
                        </a>
                    </li>
                    -->
                </ul>

            </li>
            <li class="">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-bar-chart"></i>
                    <span class="menu-text">数据报表</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <ul class="submenu">
                    <li class="">
                        <a href="{{url('manager/chart/person')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            业绩情况统计
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('manager/visit/visitChart')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            拜访情况统计
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('manager/goal')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            目标完成统计
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('manager/goal/stage')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            客户数据统计
                        </a>
                    </li>
                </ul>
            </li>

            <li class="">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-user-secret"></i>
                    <span class="menu-text">团队管理</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <ul class="submenu">
                    <li class="">
                        <a href="{{url('manager/post')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            职位管理
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('manager/department')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            部门管理
                        </a>
                    </li>
                </ul>
            </li>

            <li class="">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-cogs"></i>
                    <span class="menu-text">配置管理</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <ul class="submenu">
                    <li class="">
                        <a href="{{url('manager/visit')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            拜访类型
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('manager/visit/mind')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            拜访提醒
                        </a>
                    </li>
                    {{--<li class="">--}}
                        {{--<a href="{{url('manager/order/stage')}}">--}}
                            {{--<i class="menu-icon fa fa-caret-right"></i>--}}
                            {{--销售阶段--}}
                        {{--</a>--}}
                    {{--</li>--}}
                    <li class="">
                        <a href="{{url('manager/order/stageResult')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            销售结果
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('manager/order/recMind')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            追款提醒
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('manager/order/endMind')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            到期提醒
                        </a>
                    </li>
                    <li class="">
                        <a href="{{url('manager/config/oblivion')}}">
                            <i class="menu-icon fa fa-caret-right"></i>
                            遗忘提醒
                        </a>
                    </li>
                    <li class="@if(menu_child('configExpense')) active @endif">
                        <a href="">
                            <i class="menu-icon fa fa-caret-right"></i>费用类型
                        </a>
                    </li>

                    <li class="@if(menu_child('configLeave')) active @endif">
                        <a href="">
                            <i class="menu-icon fa fa-caret-right"></i>请假类型
                        </a>
                    </li>
                    <li class="@if(menu_child('customerConfig')) active @endif">
                        <a href="">
                            <i class="menu-icon fa fa-caret-right"></i>客户线索设置
                        </a>
                    </li>
                    <li class="@if(menu_child('customerConfig')) active @endif">
                        <a href="">
                            <i class="menu-icon fa fa-caret-right"></i>客户回收设置
                        </a>
                    </li>
                    <li class="{{ menu_child('TemplateAccess') ? 'active' : ''}}">
                        <a href="">
                            <i class="menu-icon fa fa-caret-right"></i>
                            消息触达模板配置
                        </a>
                    </li>
                </ul>

            </li>
            <li class="@if(in_array(isset(explode('/', request()->route()->uri)[1])
                    ? explode('/', request()->route()->uri)[1] : null,[
                    'leave','out','ask','expense','evection'
                    ])) active open @endif">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-desktop"></i>
                    <span class="menu-text">工作台</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    <li class="@if(menu_father('out')) active open @endif">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            <span class="menu-text"> 外出管理 </span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="@if(menu_child('outUnChecked')) active @endif">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i> 待审核
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="@if(menu_child('outIndex')) active @endif">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i> 外出列表
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>
                    <li class="@if(menu_father('leave')) active open @endif">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            <span class="menu-text"> 请假管理 </span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="@if(menu_child('leaveUnChecked')) active @endif">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i> 待审核
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="@if(menu_child('leaveIndex')) active @endif">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i> 请假列表
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>
                    <li class="@if(menu_father('ask')) active open @endif">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            <span class="menu-text"> 请示管理 </span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="@if(menu_child('askUnChecked')) active @endif">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i> 待审核
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="@if(menu_child('askIndex')) active @endif">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i> 请示列表
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>
                    <li class="@if(menu_father('expense')) active open @endif">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            <span class="menu-text"> 报销管理 </span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="@if(menu_child('expenseUnChecked')) active @endif">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i> 待审核
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="@if(menu_child('expenseIndex')) active @endif">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i> 报销列表
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>
                    <li class="@if(menu_father('evection')) active open @endif">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-caret-right"></i>
                            <span class="menu-text"> 出差管理 </span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <b class="arrow"></b>
                        <ul class="submenu">
                            <li class="@if(menu_child('evectionUnChecked')) active @endif">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i> 待审核
                                </a>
                                <b class="arrow"></b>
                            </li>
                            <li class="@if(menu_child('evectionIndex')) active @endif">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i> 出差列表
                                </a>
                                <b class="arrow"></b>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ menu_father('report') ? 'active open' :''}}">
                        <a href="#" class="dropdown-toggle">
                            <i class="menu-icon fa fa-calendar"></i>
                            <span class="menu-text">汇报管理</span>
                            <b class="arrow fa fa-angle-down"></b>
                        </a>
                        <ul class="submenu">
                            <li class="{{ menu_child('Report') ? 'active' : ''}}">
                                <a href=" ">
                                    <i class="menu-icon fa fa-caret-right"></i>
                                    汇报批注
                                </a>
                            </li>
                        </ul>

                    </li>
                </ul>
            </li>


            <li class="@if(menu_father('sign')) active open @endif">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-calendar-check-o"></i>
                    <span class="menu-text"> 考勤管理 </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    <li class="@if(menu_child('signIndex')) active @endif">
                        <a href=" ">
                            <i class="menu-icon fa fa-caret-right"></i> 考勤数据列表
                        </a>
                        <b class="arrow"></b>
                    </li>

                </ul>
            </li>

            <li class="@if(menu_father('user')) active open @endif">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-user"></i>
                    <span class="menu-text"> 员工管理 </span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    <li class="@if(menu_child('userUnChecked')) active @endif">
                        <a href=" ">
                            <i class="menu-icon fa fa-caret-right"></i> 待审核
                        </a>
                        <b class="arrow"></b>
                    </li>

                </ul>
            </li>

            <li class="@if(menu_father('role')) active open @endif">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-user"></i>
                    <span class="menu-text">权限管理</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    <li class="@if(menu_child('index')) active @endif">
                        <a href="{{ route('role') }}">
                            <i class="menu-icon fa fa-caret-right"></i> 角色列表
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('permission') }}">
                            <i class="menu-icon fa fa-caret-right"></i> 权限列表
                        </a>
                    </li>
                </ul>
            </li>
            <li class="@if(menu_father('security')) active open @endif">
                <a href="#" class="dropdown-toggle">
                    <i class="menu-icon fa fa-key"></i>
                    <span class="menu-text">安全中心</span>
                    <b class="arrow fa fa-angle-down"></b>
                </a>
                <b class="arrow"></b>
                <ul class="submenu">
                    <li class="@if(menu_child('password')) active @endif">
                        <a href="{{ route('security.password') }}">
                            <i class="menu-icon fa fa-caret-right"></i> 修改密码
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
            <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state"
               data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
        </div>
    </div>

    <div class="main-content">
        <div class="main-content-inner">
            @yield('breadcrumb')
            <div class="page-content">
                <div class="ace-settings-container" id="ace-settings-container">
                    <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                        <i class="ace-icon fa fa-cog bigger-130"></i>
                    </div>

                    <div class="ace-settings-box clearfix" id="ace-settings-box">
                        <div class="pull-left width-50">
                            <div class="ace-settings-item">
                                <div class="pull-left">
                                    <select id="skin-colorpicker" class="hide">
                                        <option data-skin="no-skin" value="#438EB9">#438EB9</option>
                                        <option data-skin="skin-1" value="#222A2D">#222A2D</option>
                                        <option data-skin="skin-2" value="#C6487E">#C6487E</option>
                                        <option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
                                    </select>
                                </div>
                                <span>&nbsp; 选择皮肤</span>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state"
                                       id="ace-settings-navbar" autocomplete="off"/>
                                <label class="lbl" for="ace-settings-navbar"> 固定导航栏</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state"
                                       id="ace-settings-sidebar" autocomplete="off"/>
                                <label class="lbl" for="ace-settings-sidebar"> 固定边栏</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state"
                                       id="ace-settings-breadcrumbs" autocomplete="off"/>
                                <label class="lbl" for="ace-settings-breadcrumbs"> 固定面包屑</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl"
                                       autocomplete="off"/>
                                <label class="lbl" for="ace-settings-rtl"> 从右到左</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2 ace-save-state"
                                       id="ace-settings-add-container" autocomplete="off"/>
                                <label class="lbl" for="ace-settings-add-container">
                                    窄屏
                                </label>
                            </div>
                        </div><!-- /.pull-left -->

                        <div class="pull-left width-50">
                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover"
                                       autocomplete="off"/>
                                <label class="lbl" for="ace-settings-hover"> 悬停子菜单</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact"
                                       autocomplete="off"/>
                                <label class="lbl" for="ace-settings-compact"> 紧凑侧边栏</label>
                            </div>

                            <div class="ace-settings-item">
                                <input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight"
                                       autocomplete="off"/>
                                <label class="lbl" for="ace-settings-highlight"> 显示激活菜单</label>
                            </div>
                        </div><!-- /.pull-left -->
                    </div><!-- /.ace-settings-box -->
                </div><!-- /.ace-settings-container -->

                <div class="row">
                    <div class="col-xs-12">
                        @if(Session::get('code') == 200)
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert">
                                    <i class="ace-icon fa fa-times"></i>
                                </button>

                                <strong>
                                    <i class="ace-icon fa fa-check"></i>
                                    {{Session::get('message')}}
                                </strong>
                                <br>
                            </div>
                        @elseif(Session::get('code') == 400)
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert">
                                    <i class="ace-icon fa fa-times"></i>
                                </button>

                                <strong>
                                    <i class="ace-icon fa fa-times"></i>
                                    {{Session::get('message')}}
                                </strong>
                                <br>
                            </div>
                    @endif
                    <!-- PAGE CONTENT BEGINS -->
                    @yield('content')
                    <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->

    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content">
                <span class="bigger-120">
                    <span class="blue bolder">黑龙江龙采科技集团</span>
                </span>
            </div>
        </div>
    </div>

    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
    </a>
</div><!-- /.main-container -->

<!-- basic scripts -->

<!--[if !IE]> -->
<script src="{{asset('assets/js/jquery-2.1.4.min.js')}}"></script>

<!-- <![endif]-->

<!--[if IE]>
<script src="{{asset('assets/js/jquery-1.11.3.min.js')}}"></script>
<![endif]-->
<script type="text/javascript">
    if ('ontouchstart' in document.documentElement) document.write("<script src='{{asset("/assets/js/jquery.mobile.custom.min.js")}}'>" + "<" + "/script>");
</script>
<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>

<!-- page specific plugin scripts -->

<!-- ace scripts -->
<script src="{{asset('assets/js/ace-elements.min.js')}}"></script>
<script src="{{asset('assets/js/ace.min.js')}}"></script>
<script>
    // var pathname = window.location.pathname + window.location.search;
    var pathname = "{{ request()->url() }}";

    $(".nav li a").each(function() {

        var href = $(this).attr("href");


        if(pathname ==  href){

            $(this).parents("ul").parent("li").addClass("active open");

            $(this).parent("li").addClass("active");

        }

    });
</script>
<!-- inline scripts related to this page -->
@yield('js')


</body>
</html>
