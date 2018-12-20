@extends('manager.layouts.base')

@section('title','首页')

@section('breadcrumb')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{url('manager')}}">首页</a>
            </li>
            <li class="active">管理首页</li>
        </ul>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plug-in/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('plug-in/echarts/theme/shine.js') }}"></script>
    <script src="{{ asset('plug-in/echarts/theme/walden.js') }}"></script>
    <script src="{{ asset('plug-in/echarts/theme/chalk.js') }}"></script>

@endsection

@section('css')
    <style>
        .infobox {
            width: 100%;
            padding: 10px 15px;
        }
        .infobox > .infobox-data {
            padding-left: 15px;
        }
        hr {
            border-bottom: 1px dotted #E2E2E2;
            border-top: 0;
            padding: 0 15px;
            clear: both;
        }
        .infobox-badge a{
            display: block;
            position: absolute;
            right:0;
            top:0;
            padding: 2.5px 5px;
            text-decoration: none;
        }
        .infobox-red .infobox-badge a{
            background-color: #D53F40;
            color:white;
        }
        .infobox-orange .infobox-badge a{
            background-color: #E8B110;
            color:white;
        }
        .infobox-orange2 .infobox-badge a{
            background-color: #F79263;
            color:white;
        }
        .infobox-blue .infobox-badge a{
            background-color: #6FB3E0;
            color:white;
        }
        .infobox-green .infobox-badge a{
            background-color: #9ABC32;
            color:white;
        }
        .infobox-pink .infobox-badge a{
            background-color: #CB6FD7;
            color:white;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1>管理首页</h1>
    </div>
    <div class="page-content">

        <div class="row">
            <div class="col-sm-2">
                <div class="infobox infobox-red">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-rmb"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">  元</span>
                        <div class="infobox-content">已完成订单总金额</div>
                    </div>
                    <span class="infobox-badge"><a href=" ">2018</a></span>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="infobox infobox-orange">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-rmb"></i>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-data-number">  元</span>
                        <div class="infobox-content">已回款总金额</div>
                    </div>
                    <span class="infobox-badge"><a href=" ">2018</a></span>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="infobox infobox-orange2">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-rmb"></i>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-data-number">  元</span>
                        <div class="infobox-content">待回款总金额</div>
                    </div>
                    <span class="infobox-badge"><a href=" ">2018</a></span>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="infobox infobox-blue">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-shopping-cart"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">  单</span>
                        <div class="infobox-content">订单总数</div>
                    </div>
                    <span class="infobox-badge"><a href=" ">ALL</a></span>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="infobox infobox-green">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-users"></i>
                    </div>
                    <div class="infobox-data">
                        <span class="infobox-data-number">  家</span>
                        <div class="infobox-content">客户总数</div>
                    </div>
                    <span class="infobox-badge"><a href=" ">ALL</a></span>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="infobox infobox-pink">
                    <div class="infobox-icon">
                        <i class="ace-icon fa fa-user"></i>
                    </div>

                    <div class="infobox-data">
                        <span class="infobox-data-number">  人</span>
                        <div class="infobox-content">员工总数</div>
                    </div>
                    <span class="infobox-badge"><a href=" ">查看所有</a></span>
                </div>
            </div>
        </div>
        <hr>
        <div class="row" style="border-bottom: 1px dotted #E2E2E2;padding-bottom: 15px;">
            <div class="col-md-9">
                <div id="left" style="width: 100%; height: 400px;"></div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12"
                         style="border-bottom: 1px dotted #E2E2E2;border-left:1px dotted #E2E2E2;">
                        <div id="right-2" style="width: 100%;height:200px;"></div>
                    </div>
                    <div class="col-md-12" style="border-left:1px dotted #E2E2E2;padding-top: 15px;">
                        <div id="right-3" style="width: 100%;height:200px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection