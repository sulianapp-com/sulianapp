@extends('layouts.base')

@section('title','菜单列表')

@section('pageHeader','菜单列表header')

@section('pageDesc','菜单列表desc')

@section('content')


<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">菜单管理</a></li>
    </ul>
</div>
<div class='panel panel-default'>

    <div class='panel-body'>

        <table class="table">
            <thead>
            <tr>
                <th>名称</th>
                <th>标识</th>
                <th>菜单
                 权限
                 状态</th>
                <th> <span class="pull-right">操作</span></th>
            </tr>
            </thead>
            <tbody>
           @foreach($menuList as $row)
            <tr>
                <td>{!! $row['spacer'] !!}{{$row['name']}}</td>
                <td>{{$row['item']}}</td>
                <td>
                    <span class='label label-{{$row['menu'] ? 'success':'default'}}'>{{$row['menu'] ? '启用':'禁用'}}</span>

                    <span class='label label-{{$row['permit'] ? 'success':'default'}}'>{{$row['permit'] ? '启用':'禁用'}}</span>

                    <span class='label label-{{$row['status'] ? 'success':'default'}}'>{{$row['status'] ? '启用':'禁用'}}</span>
                </td>
                <td>
                   <span class="pull-right">
                   <a class='btn btn-default btn-sm' href="{{yzWebUrl('menu.add', array('parent_id'=>$row['id']))}}" title='添加子分类' ><i class="fa fa-plus"></i></a>

                     <a class='btn btn-default btn-sm' href="{{yzWebUrl('menu.edit', array('id' => $row['id']))}}" title="编辑" ><i class="fa fa-edit"></i></a>
                    <a class='btn btn-default btn-sm' href="{{yzWebUrl('menu.del', array('id' => $row['id']))}}" title='删除' onclick="return confirm('确认删除此菜单吗？');return false;"><i class="fa fa-remove"></i></a>
                </span>
            </tr>
            @endforeach
            </tbody>
        </table>

    </div>
    <div class='panel-footer'>
        <a class='btn btn-info' href="{{yzWebUrl('menu.add')}}"><i class="fa fa-plus"></i> 添加新菜单</a>
        @if(request()->getHost() != 'test.yunzshop.com' && env('APP_ENV') != 'production')
        <a class='btn btn-success back' href="{{yzWebUrl('menu.get-remote-update')}}"><i class="fa fa-flash"></i> 更新远程菜单</a>
            @endif
    </div>
</div>
    @endsection