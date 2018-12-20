<?php

?>

@extends('manager.layouts.base')

@section('title','角色管理')

@section('breadcrumb')
    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{url('manager')}}">首页</a>
            </li>
            <li>
                <a href="#">权限管理</a>
            </li>
            <li class="active">角色管理</li>
        </ul>
    </div>
@endsection

@section('css')
    <style>
        .btn {
            border: 0 !important;
        }

        .checkbox:first-child {
            margin-top: 0;
        }

        .box {
            height: 300px;
            overflow-y: auto;
        }

        .line {
            clear: both;
            border-bottom: 1px dotted #E2E2E2;
            margin-bottom: 15px;
        }

        .second {
            padding: 5px 0 10px 30px;
        }

        .third {
            padding: 5px 0 10px 30px;
        }
    </style>
@endsection

@section("js")
    <script>
        $(".create").click(function () {
            const modal = $("#myModal");
            modal.find('#myModalLabel').text('添加角色');
            modal.find('.btn-primary').text('确认添加');
            modal.find('form').attr('action', "{{ route('role.create') }}");
            modal.find('#name').val('');
            modal.find('#guard_name').val('');
            //modal.find('#description').val('');
            modal.modal('show');
        });

        $(".update").click(function () {
            const dom = $(this);
            const tr = dom.parent('td').parent('tr');
            console.log(tr);
            const modal = $("#myModal");
            modal.find('#myModalLabel').text('修改角色');
            modal.find('.btn-primary').text('确认修改');
            modal.find('form').attr('action', "{{ route('role.update') }}");
            modal.find('#id').val(dom.attr('data-id'));
            modal.find('#name').val(tr.find('td:eq(1)').text());
            modal.find('#guard_name').val(tr.find('td:eq(2)').text());
            modal.modal('show');
        });

        $(".delete").click(function () {
            const dom = $(this);
            const tr = dom.parent('td').parent('tr');
            let id = dom.attr('data-id');
            if (confirm('是否删除当前角色?')) {
                $.ajax({
                    url: "{{ route('role.delete') }}",
                    type: 'post',
                    data: {id: id, _token: "{{ csrf_token() }}"},
                    success: function (d) {
                        if (d.code === 200) {
                            tr.hide('slow');
                        } else {
                            alert(d.message);
                        }
                    }
                })
            }
        });

        $(".allot_user").click(function () {
            const dom = $(this);
            const modal = $("#myModalAllotUser");
            const tr = dom.parent('td').parent('tr');
            let id = dom.attr('data-id');
            modal.find('#id').val(id);
            {{--modal.find('form').attr('action', "{{ route('permission.allot.user') }}");--}}
            {{--modal.find('.form-control-static').text(tr.find('td:eq(1)').text());--}}
            {{--modal.find('#myModalLabel').text('分配用户');--}}
            {{--modal.find('.btn-primary').text('确认分配');--}}
            {{--modal.modal('show');--}}
            let inputCheckBox = $("input.test_user");

            $.ajax({
                url: "{{ route('role.AjaxGetUser') }}",
                type: 'post',
                data: {role: id, _token: "{{ csrf_token() }}"},
                success: function (d) {
                    if (d.code === 200) {
                        let arr = d.data;
                        inputCheckBox.each(function () {
                            let box = $(this);
                            for (let i = 0; i < arr.length; i++) {
                                if (arr[i] == box.val()) {
                                    box.attr('checked', true);
                                    box.prop('checked', true);
                                }
                            }
                        });

                        modal.find('form').attr('action', "{{ route('permission.allot.user') }}");
                        modal.find('.form-control-static').text(tr.find('td:eq(1)').text());
                        modal.find('#myModalLabel').text('分配用户');
                        modal.find('.btn-primary').text('确认分配');
                        modal.modal('show');

                    } else {
                        alert(d.message);
                    }
                }
            });
        });

        $(".allot_permission").click(function () {
            const dom = $(this);
            const modal = $("#myModalAllotPermission");
            const tr = dom.parent('td').parent('tr');
            let inputCheckBox = $("[name='permission[]']");
            let id = dom.attr('data-id');
            modal.find('#id').val(id);
            $.ajax({
                url: "{{ route('role.AjaxGetPermission') }}",
                type: 'post',
                data: {role: id, _token: "{{ csrf_token() }}"},
                success: function (d) {
                    if (d.code === 200) {
                        let arr = d.data;
                        inputCheckBox.each(function () {
                            let box = $(this);
                            for (let i = 0; i < arr.length; i++) {
                                if (arr[i] == box.val()) {
                                    box.attr('checked', true);
                                    box.prop('checked', true);
                                }
                            }
                        });
                        modal.find('form').attr('action', "{{ route('permission.allot.permission') }}");
                        modal.find('.form-control-static:eq(0)').text(tr.find('td:eq(2)').text());
                        modal.find('#myModalLabel').text('分配角色');
                        modal.find('.btn-primary').text('确认分配');
                        modal.modal('show');
                    } else {
                        alert(d.message);
                    }
                }
            });
        });

        $(".top").click(function () {
            const dom = $(this);
            const AllInput = dom.parent('.checkbox-inline').next('.second').find("[name='permission[]']");
            let isChecked = dom.is(":checked");
            AllInput.each(function () {
                let i = $(this);
                if (isChecked) {
                    i.attr("checked", true);
                    i.prop("checked", true);
                } else {
                    i.attr('checked', false);
                    i.prop('checked', false);
                }
            });
        });

        $(".middle").click(function () {
            const dom = $(this);
            const AllInput = dom.parent('.checkbox-inline').next('.third').find("[name='permission[]']");
            const middles = dom.parent('.checkbox-inline').parent('.second').find(".checkbox-inline input");
            const top = dom.parent('.checkbox-inline').parent('.second').parent(".checkbox").find(".checkbox-inline:eq(0) input");
            let number = 0;
            let isChecked = dom.is(":checked");
            AllInput.each(function () {
                let i = $(this);
                if (isChecked) {
                    i.attr("checked", true);
                    i.prop("checked", true);
                } else {
                    i.attr('checked', false);
                    i.prop('checked', false);
                }
            });
            middles.each(function () {
                let i = $(this);
                if (i.is(":checked")) {
                    number += 1;
                }
            });
            if (number > 0) {
                top.attr("checked", true);
                top.prop("checked", true);
            }
            if (number === 0) {
                top.attr("checked", false);
                top.prop("checked", false);
            }
        });

        $("[name='permission[]']").click(function () {
            const dom = $(this);
            const box = dom.parent('.checkbox-inline').parent('.third').find("[name='permission[]']");
            const top = dom.parent('.checkbox-inline').parent('.third').parent('.second').parent('.checkbox').find('.checkbox-inline:eq(0) input');
            const middle = dom.parent('.checkbox-inline').parent('.third').parent('.second').find('.checkbox-inline:eq(0) input');
            let number = 0;
            box.each(function () {
                let i = $(this);
                if (i.is(":checked")) {
                    number += 1;
                }
            });
            if (number > 0) {
                top.attr("checked", true);
                top.prop("checked", true);
                middle.attr("checked", true);
                middle.prop("checked", true);
            }
            if (number === 0) {
                middle.attr("checked", false);
                middle.prop("checked", false);
                let top_number = 0;
                top.parent('.checkbox-inline').next('.second').find("[name='permission[]']").each(function () {
                    let i = $(this);
                    if (i.is(":checked")) {
                        top_number += 1;
                    }
                });
                if (top_number > 0) {
                    top.attr("checked", true);
                    top.prop("checked", true);
                } else {
                    top.attr("checked", false);
                    top.prop("checked", false);
                }
            }
        });

    </script>
@endsection

@section("content")
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    <form method="post">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label text-right">角色名称</label>
                                <div class="col-sm-10">
                                    <input type="text" id="name" name="name" class="form-control" placeholder="请输入角色名称">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                                <label for="guard_name" class="col-sm-2 control-label text-right">守护名称</label>
                                <div class="col-sm-10">
                                    <input type="text" id="guard_name" name="guard_name" class="form-control"
                                           placeholder="请输入守护名称">
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary"
                            onclick="$('#myModal').find('form').submit();"></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalAllotUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    <form method="post">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label text-right">角色名称：</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">email@example.com</p>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                                <label for="user" class="col-sm-2 control-label text-right">用户：</label>
                                <div class="col-sm-10">
                                    @foreach($users as $user)
                                        <label><input type="checkbox" class="test_user" name="user[]" value="{{ $user['id'] }}" >{{ $user['name'] }}</label><br/>
                                    @endforeach
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary"
                            onclick="$('#myModalAllotUser').find('form').submit();"></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalAllotPermission" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    <form method="post">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label text-right">角色名称</label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">email@example.com</p>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="form-group">
                                <label for="permission" class="col-sm-2 control-label text-right">权限列表</label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="box">
                                            @foreach($permissions as $k => $permission)
                                                <div class="col-sm-12">
                                                    <div class="checkbox">
                                                        <label class="checkbox-inline">
                                                            <input type="checkbox" name="permission[]" class="top"
                                                                   value="{{ $permission->id }}"
                                                            > {{ $permission->name }}
                                                        </label>

                                                    </div>
                                                </div>
                                                <div class="line"></div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary"
                            onclick="$('#myModalAllotPermission').find('form').submit();"></button>
                </div>
            </div>
        </div>
    </div>
    <div class="page-header">
        <h1>角色列表</h1>
    </div>
    <div class="page-content">
        <div class="row">
            <div class="col-md-6 col-md-6 col-sm-12 col-xs-12">
                <div class="clearfix"
                     style="border-bottom: 1px dotted #E2E2E2;margin-bottom: 15px;padding-bottom: 15px;">
                    <p>操作</p>
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-primary create"><i class="fa fa-plus"></i> 添加</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-8">
                @if(session()->has("error"))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <strong>温馨提示：</strong> {{session()->get('error')}}
                    </div>
                @endif
                @if(session()->has("success"))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <strong>温馨提示：</strong> {{session()->get('success')}}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="table-header">
                    角色列表
                </div>
                <div>
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="center">#</th>
                            <th>角色名称</th>
                            <th>守护名称</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($roles as $key => $role)
                            <tr>
                                <td class="center">{{ $key }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->guard_name }}</td>
                                <td>
                                    <a href="javascript:void(0)" class="update" data-id="{{ $role->id }}" title="修改角色">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="allot_permission" data-id="{{ $role->id }}"
                                       title="分配权限">
                                        <i class="fa fa-cog"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="allot_user" data-id="{{ $role->id }}"
                                       title="分配用户">
                                        <i class="fa fa-user"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="delete" data-id="{{ $role->id }}" title="删除角色">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $roles->links() }}
                </div>
            </div>
            <div class="col-md-6 col-md-6 col-sm-12 col-xs-12">
                <div class="table-header">
                    授权用户列表
                </div>
                <div>
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="center">#</th>
                            <th>角色名称</th>
                            <th>用户列表</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($role_users as $key => $role_user)
                            <tr>
                                <td class="center">{{ $key }}</td>
                                <td>{{ $role_user['role_name'] }}</td>
                                <td>
                                    @foreach($role_user['users'] as $user)
                                        {{ $user['name'] }} <br/>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

