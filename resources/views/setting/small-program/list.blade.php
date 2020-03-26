@extends('layouts.base')
@section('title', '自定义模板管理')
@section('content')
    <div class="right-titpos">
        <ul class="add-snav">
            {{--<li class="active"><a href="#">自定义模板管理<span style="color:red;">[{{$list->total()}}个]</span></a></li>--}}
        </ul>
    </div>
<div class="page-header">
    <div class='panel panel-default'>
    <form action="" method="get" class="form-horizontal form-search" role="form1">
        <input type="hidden" name="c" value="site"/>
        <input type="hidden" name="a" value="entry"/>
        <input type="hidden" name="m" value="yun_shop"/>
        <input type="hidden" name="do" value="temp" id="form_do"/>
        <input type="hidden" name="route" value="setting.diy-temp.index" id="route" />
        <div class="page-toolbar">
            <span class=''>
                 <a class='btn btn-info btn-sm' href="{!! yzWebUrl('setting.small-program.notice') !!}"><i class="fa fa-plus-square"></i> 消息通知</a>
             </span>
        </div>
    </form>

    <form action="" method="post">
        @if (count($list) > 0)
        <table class="table table-responsive table-hover">
            <thead>
            <tr>
                <th >模板名称</th>
                <th style="width: 100px;">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
            <tr>
                <td>{{$row['title']}}</td>
                <td>
                    <a href="{!! yzWebUrl('setting.small-program.edit', array('id' => $row['id'])) !!}">修改</a>

                    <a href="{!! yzWebUrl('setting.small-program.del', array('id' => $row['id'])) !!}"
                       onclick="return confirm('此操作不可逆,确认要删除?');return false;">删除</a>
                </td>
            </tr>

            @endforeach

            </tbody>
            <tfoot>
            <tr>
                <td colspan="2" style="text-align: right">
                    {{--{!! $pager !!}--}}
                </td>
            </tr>
            </tfoot>
        </table>
        @else
        <div class='panel panel-default'>
            <div class='panel-body' style='text-align: center;padding:30px;'>
                暂时没有任何群发模板!
            </div>
        </div>
        @endif
    </form>
</div>
@endsection