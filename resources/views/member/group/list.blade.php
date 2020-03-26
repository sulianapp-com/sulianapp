@extends('layouts.base')

@section('content')

    <div class="rightlist">

        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">会员分组</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class='panel panel-default'>
            <div class='panel-body'>
                <table class="table">
                    <thead>
                        <tr>
                            <th>分组名称</th>
                            <th>会员数</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupList as $list)
                        <tr>
                            <td>{{ $list->group_name }}</td>
                            <td>
                                {{ $list->member->count() }}
                            </td>
                            <td>
                                <a title="查看" class='btn btn-default' href="{{ yzWebUrl('member.member.index', array('search' => ['groupid' => $list->id])) }}">
                                    <i class='fa fa-users'></i></a>
                                <a title="编辑" class='btn btn-default' href="{{ yzWebUrl('member.member-group.update', array('group_id' => $list->id)) }}">
                                    <i class='fa fa-edit'></i></a>
                                <a title="删除" class='btn btn-default' href="{{ yzWebUrl('member.member-group.destroy', array('group_id' => $list->id)) }}" onclick="return confirm('删除此分组，所属该分组会员的分组降低为默认分组，确认删除？');return false;">
                                    <i class='fa fa-remove'></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {!! $pager !!}
            </div>
            <div class='panel-footer'>
                <a class='btn btn-info' href="{{ yzWebUrl('member.member-group.store') }}"><i class="fa fa-plus"></i>
                    添加新分组</a>
            </div>
        </div>
    </div>


@endsection