<?php

?>

@extends('manager.layouts.base')

@section('title','权限列表')

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
            <li class="active">权限列表</li>
        </ul>
    </div>
@endsection

@section('css')
    <style>
        .btn {
            border: 0 !important;
        }
    </style>
@endsection

@section("js")
    <script>
        $(".create").click(function () {
            const modal = $("#myModal");
            modal.find('#myModalLabel').text('添加权限');
            modal.find('.btn-primary').text('确认添加');
            modal.find('form').attr('action', "{{ route('permission.create') }}");
            modal.find('#id').val('');
            modal.find('#pid').val(0);
            modal.find('#name').val('');
            modal.find('#guard_name').val('');
            modal.find('#description').val('');
            modal.modal('show');
        });

        $(".update").click(function () {
            const dom = $(this);
            const tr = dom.parent('td').parent('tr');
            console.log(tr);
            const modal = $("#myModal");
            modal.find('#myModalLabel').text('修改权限');
            modal.find('.btn-primary').text('确认修改');
            modal.find('form').attr('action', "{{ route('permission.update') }}");
            modal.find('#id').val(dom.attr('data-id'));
            modal.find('#pid').val(dom.attr('data-pid'));
            modal.find("#pid option").each(function () {
                let i = $(this);
                i.attr('disable','');
                // if(){
                //
                // }
            });
            modal.find('#name').val(dom.attr('data-name'));
            modal.find('#guard_name').val(tr.find('td:eq(2)').text());
            modal.find('#description').val(tr.find('td:eq(4)').text());
            modal.modal('show');
        });

        $(".delete").click(function () {
            const dom = $(this);
            const tr = dom.parent('td').parent('tr');
            let id = dom.attr('data-id');
            if (confirm('是否删除当前权限?')) {
                $.ajax({
                    url: "{{ route('permission.delete') }}",
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

            let inputCheckBox = $("input.test_user");

            $.ajax({
                url: "{{ route('permission.AjaxGetUser') }}",
                type: 'post',
                data: {permission: id, _token: "{{ csrf_token() }}"},
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
                                <label for="name" class="col-sm-2 control-label text-right">权限名称</label>
                                <div class="col-sm-10">
                                    <input type="text" id="name" name="name" class="form-control" placeholder="请输入权限名称">
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
    <div class="page-header">
        <h1>权限列表</h1>
    </div>
    <div class="page-content">
        <div class="row">
            <div class="col-xs-12">
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
                <div class="clearfix"
                     style="border-bottom: 1px dotted #E2E2E2;margin-bottom: 15px;padding-bottom: 15px;">
                    <p>操作</p>
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-primary create"><i class="fa fa-plus"></i> 添加</button>
                        </div>
                    </div>
                </div>
                <div class="table-header">
                    权限列表
                </div>
                <div>
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="center">#</th>
                            <th>权限名称</th>
                            <th>守护名称</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($permissions as $key => $permission)
                            <tr>
                                <td class="center">{{ $key }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->guard_name }}</td>
                                <td>
                                    <a href="javascript:void(0)" class="update" data-id="{{ $permission->id }}"
                                       data-name="{{ $permission->name }}" data-pid="{{ $permission->pid }}"><i
                                                class="fa fa-edit"></i></a>
                                    <a href="javascript:void(0)" class="allot_user" data-id="{{ $permission->id }}"
                                       title="分配用户">
                                        <i class="fa fa-user"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="delete" data-id="{{ $permission->id }}"><i
                                                class="fa fa-trash"></i></a>
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

