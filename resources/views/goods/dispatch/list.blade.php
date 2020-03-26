@extends('layouts.base')

@section('content')
@section('title', trans('配送模板列表'))
<div class="w1200 m0a">

<div class="rightlist">
    	<!-- 新增加右侧顶部三级菜单 -->
<div class="right-titpos">
	<ul class="add-snav">
		<li class="active"><a href="#">配送方式</a></li>
	</ul>
</div>
<!-- 新增加右侧顶部三级菜单结束 -->
<form action="{{ yzWebUrl('goods.dispatch.sort') }}" method="post">
<div class="main panel panel-default">
    <div class="panel-body table-responsive">
        <table class="table table-hover">
            <thead class="navbar-inner">
                <tr>
                    <th style="width:50px;">ID</th>
                    <th style="width:80px;">显示顺序</th>
                    <th>配送方式名称</th>
                    <th>计费方式</th>
                    <th>首重(首件)价格</th>
                    <th>续重(续件)价格</th>
                    <th>状态</th>
                    <th>默认快递</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ( $list['data'] as  $dispatch )
                <tr>
                    <td>{{ $dispatch['id'] }}</td>
                    <td>
                        <input type="text" class="form-control" name="display_order[{{ $dispatch['id'] }}]" value="{{ $dispatch['display_order'] }}">

                    </td>
                    <td>{{ $dispatch['dispatch_name'] }}</td>
                    @if ( $dispatch['calculate_type'] == 0 )
                    <td>按重量计费</td>
                    <td>{{ $dispatch['first_weight_price'] }}</td>
                    <td>{{ $dispatch['another_weight_price'] }}</td>
                    @else
                    <td>按件计费</td>
                    <td>{{ $dispatch['first_piece_price'] }}</td>
                    <td>{{ $dispatch['another_piece_price'] }}</td>
                    @endif

                    <td><label class='label  label-default @if ( $dispatch["enabled"] == 1 ) label-info @endif' >@if ( $dispatch['enabled'] == 1 ) 显示 @else 隐藏 @endif</label></td>
                    <td><label class='label  label-default @if ( $dispatch["is_default"] == 1 ) label-info @endif' >@if ( $dispatch['is_default'] == 1 ) 是 @else 否 @endif</label></td>
                    <td style="text-align:left;">
                         <a href="{{ yzWebUrl('goods.dispatch.edit', ['id'=>$dispatch['id']]) }}" class="btn btn-default btn-sm" title="修改"><i class="fa fa-pencil"></i></a>
                         <a href="{{ yzWebUrl('goods.dispatch.delete', ['id'=>$dispatch['id']]) }}" class="btn btn-default btn-sm" onclick="return confirm('确认删除此配送方式?')" title="删除"><i class="fa fa-times"></i></a>
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
        {!! $pager !!}
        
    </div>

    <div class="panel-footer">
        <a class="btn btn-info " href="{{ yzWebUrl('goods.dispatch.add') }}"><i class="fa fa-plus"></i> 添加配送方式</a>
        <input name="submit" type="submit" class="btn btn-default back" value="提交排序">
    </div>
</div>
</form>
</div>
</div>
<script>
    require(['bootstrap'], function ($) {
        $('.btn').hover(function () {
            $(this).tooltip('show');
        }, function () {
            $(this).tooltip('hide');
        });
    });
</script>

@endsection

