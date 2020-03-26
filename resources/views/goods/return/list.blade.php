@extends('layouts.base')

@section('content')
@section('title', trans('退货地址列表'))
<div class="w1200 m0a">

    <div class="rightlist">
    	<!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">退货地址</a></li>
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
                                <th class="col-md-2">退货地址名称</th>
                                <th class="col-md-2">联系人</th>
                                <th class="col-md-3">联系方式</th>
                                <th class="col-md-10">地址</th>
                                <th class="col-md-2">默认退货地址</th>
                                <th class="col-md-2">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( $list['data'] as  $address )
                            <tr>
                                <td>{{ $address['id'] }}</td>
                                <td>{{ $address['address_name'] }}</td>
                                <td>{{ $address['contact'] }}</td>
                                <td>{{ $address['mobile'] }}</td>
                                <td>{{ $address['province_name'] }}{{ $address['city_name'] }}{{ $address['district_name'] }}{{ $address['street_name'] }}{{ $address['address'] }}</td>
                                <td><label class='label @if ( $address["is_default"] == 1 ) label-info @else label-default @endif' >@if ( $address['is_default'] == 1 ) 是 @else 否 @endif</label></td>
                                <td style="text-align:left;">
                                     <a href="{{ yzWebUrl('goods.return-address.edit', ['id'=>$address['id']]) }}" class="btn btn-default btn-sm" title="修改"><i class="fa fa-pencil"></i></a>
                                     <a href="{{ yzWebUrl('goods.return-address.delete', ['id'=>$address['id']]) }}" class="btn btn-default btn-sm" onclick="return confirm('确认删除此配送方式?')" title="删除"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $pager !!}

                </div>
                <div class="panel-footer">
                    <a class="btn btn-info " href="{{ yzWebUrl('goods.return-address.add') }}"><i class="fa fa-plus"></i> 添加退货地址</a>
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

