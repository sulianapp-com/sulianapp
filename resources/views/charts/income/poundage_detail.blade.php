@extends('layouts.base')

@section('content')
@section('title', trans('订单收益统计'))

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="w1200 m0a">
    {{--<script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>--}}
    {{--<script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>--}}
    {{--<script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>--}}

    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-default">
            <div class="panel-heading">
                统计明细
            </div>
            <div class="panel-body">
                <div class="card">
                    <div class="card-content">
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
                            <div class="form-group col-xs-12 col-sm-4">
                                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">类型：</label>
                                <div class="col-xs-12 col-sm-8 col-lg-9">
                                    <select name='search[type]' class='form-control'>
                                        <option value='' @if($search['type']=='') selected @endif>全部</option>
                                        @foreach($types as $type)
                                            <option value='{{$type['class']}}'
                                                    @if($search['type']==$type['class']) selected @endif>{{$type['title']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4">
                                <button class="btn btn-success" id="search"><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class=" order-info">
                <div class="table-responsive">
                    <table class='table order-title table-hover table-striped'>
                        <thead>
                        <tr>
                            <th class="col-md-2 text-center" style='width:80px;'>收入类型</th>
                            <th class="col-md-2 text-center" style="white-space: pre-wrap;">手续费</th>
                            <th class="col-md-2 text-center">劳务税</th>
                            <th class="col-md-2 text-center">总计</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $key => $row)
                            <tr style="height: 40px; text-align: center">
                                <td>{{ $row['type_name']}}</td>
                                <td>{{ $row['actual_poundage'] ?: '0.00' }}</td>
                                <td>{{ $row['actual_servicetax'] ?: '0.00' }}</td>
                                <td>{{ sprintf("%01.2f",($row['actual_poundage'] + $row['actual_servicetax'])) ?: '0.00' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @include('order.modals')
            <div id="pager">{!! $pager !!}</div>
        </div>
    </div>
</div>
@endsection
