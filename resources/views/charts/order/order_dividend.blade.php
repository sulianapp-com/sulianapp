@extends('layouts.base')

@section('content')
@section('title', trans('订单分润'))

<link href="{{static_url('yunshop/css/order.css')}}" media="all" rel="stylesheet" type="text/css"/>
<div class="w1200 m0a">
    <script type="text/javascript" src="{{static_url('js/dist/jquery.gcjs.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/jquery.form.js')}}"></script>
    <script type="text/javascript" src="{{static_url('js/dist/tooltipbox.js')}}"></script>

    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="card">
                    <div class="card-header card-header-icon" data-background-color="rose">
                        <i class="fa fa-bars" style="font-size: 24px;" aria-hidden="true"></i>
                    </div>
                    <div class="card-content">
                        <h4 class="card-title">订单分润</h4>
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
                            <div class="form-group col-xs-12 col-sm-4">
                                <input type="text" class="form-control"  name="search[order_sn]" value="{{$search['order_sn']?$search['order_sn']:''}}" placeholder="订单号查询"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4">
                                <input type="text" class="form-control"  name="search[shop_name]" value="{{$search['shop_name']?$search['shop_name']:''}}" placeholder="店铺名称查询"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4" style="padding-bottom: 15px">
                                <input type="hidden" id="province_id" value="{{ $search['province_id']?:0 }}"/>
                                <input type="hidden" id="city_id" value="{{ $search['city_id']?:0 }}"/>
                                <input type="hidden" id="district_id" value="{{ $search['district_id']?:0 }}"/>
                                <input type="hidden" id="street_id" value="{{ $search['street_id']?:0 }}"/>
                                {!! app\common\helpers\AddressHelper::tplLinkedAddress(['search[province_id]','search[city_id]','search[district_id]','search[street_id]'], [])!!}
                            </div>
                            {{--<br><br><br>--}}
                            <div class="form-group col-xs-12 col-sm-3">
                                <input type="text" class="form-control"  name="search[member]" value="{{$search['member']?$search['member']:''}}" placeholder="购买者：会员ID/昵称/姓名/手机"/>
                            </div>
                            <div class="form-group col-xs-12 col-sm-3">
                                <input type="text" class="form-control"  name="search[recommend]" value="{{$search['recommend']?$search['recommend']:''}}" placeholder="推荐者：会员ID/昵称/姓名/手机"/>
                            </div>
                            <div class='form-group col-xs-12 col-sm-3'>
                                <select name="search[status]" class="form-control">
                                    <option value="" >
                                        订单状态
                                    </option>
                                    <option value="0"
                                            @if($search['status'] == '0')  selected="selected"@endif>
                                        未完成
                                    </option>
                                    <option value="3"
                                            @if($search['status'] == '3')  selected="selected"@endif>
                                        已完成
                                    </option>
                                    <option value="-2"
                                            @if($search['status'] == '-2')  selected="selected"@endif>
                                        已退款
                                    </option>
                                </select>
                            </div>
                            <div class='form-group col-xs-12 col-sm-3'>
                                <select name="search[statistics]" class="form-control">
                                    <option value="">是否统计</option>
                                    <option value="1" @if($search['statistics'] == '1') selected @endif>是</option>
                                    <option value="0" @if($search['statistics'] == '0') selected @endif>否</option>
                                </select>
                            </div>
                            <div class='form-group col-xs-12 col-sm-6'>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="search[is_time]" value="1"
                                               @if($search['is_time'] == '1')checked="checked"@endif>
                                    </span>
                                    {!!app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                                                                            'starttime'=>$search['time']['start'] ?: date('Y-m-d H:i:s'),
                                                                            'endtime'=>$search['time']['end'] ?: date('Y-m-d H:i:s'),
                                                                            'start'=>0,
                                                                            'end'=>0
                                                                            ], true)!!}
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-4">
                                <button class="btn btn-success" id="search"><i class="fa fa-search"></i> 搜索</button>
                                <button type="submit" name="export" value="1" id="export" class="btn btn-default">导出 Excel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <table class='table' style='float:left;margin-bottom:0;table-layout: fixed;line-height: 40px;height: 40px'>
                <tr class='trhead'>
                    <td colspan='8' style="text-align: left;">
                        <div id="statistics" @if($search['statistics'] != 1) hidden="hidden" @endif>
                            <p>数量: <span id="total">{{ $total['count'] ?: '0' }}</span>&nbsp;&nbsp;&nbsp;订单总金额: <span id="total">{{ $total['price'] ?: '0.00' }}元</span>&nbsp;&nbsp;&nbsp;订单成本总额: <span id="total">{{ sprintf("%.2f",$total['cost_price'] + $total['dispatch_price']) }}</span></p>
                            <p>分销佣金: <span id="total">{{ $total['commission'] ?: '0.00' }}元</span>&nbsp;&nbsp;&nbsp;经销商提成: <span id="total">{{ $total['team_dividend'] ?: '0.00' }}元</span>&nbsp;&nbsp;&nbsp;区域分红: <span id="total">{{ $total['area_dividend'] ?: '0.00' }}元</span>&nbsp;&nbsp;&nbsp;微店分红: <span id="total">{{ $total['micro_shop'] ?: '0.00' }}元</span></p>
                            <p>招商员分红: <span id="total">{{ $total['merchant'] ?: '0.00' }}元</span>&nbsp;&nbsp;&nbsp;招商中心分红: <span id="total">{{ $total['merchant_center'] ?: '0.00' }}元</span>&nbsp;&nbsp;&nbsp;积分奖励: <span id="total">{{ $total['point'] ?: '0.00' }}</span>&nbsp;&nbsp;&nbsp;爱心值奖励: <span id="total">{{ $total['love'] ?: '0.00' }}</span></p>
                            <p>预计总利润: <span id="total">{{ sprintf("%.2f",$total['price'] - ($total['cost_price'] + $total['dispatch_price'] + $total['commission'] + $total['team_dividend'] + $total['merchant'] + $total['merchant_center'] + $total['micro_shop'] + $total['area_dividend'])) }}元</span></p>
                        </div>
                        <p>1、订单成本：平台订单为商品成本+运费，供应商、门店订单为供应商、门店结算金额；</p>
                        <p>2、分销佣金、经销商提成、区域分红、微店分红、招商员分红、招商中心分红为该订单在这种方式的总分红金额求和。</p>
                        <p>3、预计利润=订单金额-订单成本-分销佣金-经销商提成-区域分红-微店分红-招商员分红-招商中心分红</p>
                        <p>4、状态：未完成为已支付但未完成的订单，已完成订单完成的状态，已退款的时候更新状态。</p>
                    </td>
                </tr>
            </table>

            <div class=" order-info">
                <div class="table-responsive">
                    <table class='table order-title table-hover table-striped'>
                        <thead>
                        <tr>
                            <th class="col-md-4 text-center" style="white-space: pre-wrap;">时间<br>订单号</th>
                            <th class="col-md-4 text-center">订单区域</th>
                            <th class="col-md-2 text-center" style="white-space: pre-wrap;">购买者<br>推荐者</th>
                            <th class="col-md-4 text-center">店铺</th>
                            <th class="col-md-2 text-center">订单金额<br>订单成本</th>
                            <th class="col-md-2 text-center">分销佣金<br>经销商提成</th>
                            <th class="col-md-2 text-center">区域分红<br>微店分红</th>
                            <th class="col-md-3 text-center">招商员分红<br>招商中心分红</th>
                            <th class="col-md-2 text-center">积分奖励<br>爱心值奖励</th>
                            <th class="col-md-2 text-center">预计利润</th>
                            <th class="col-md-2 text-center">状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list as $row)
                            <tr style="height: 40px; text-align: center">
                                <td>{{$row['created_at']}}<br>{{ $row['order_sn'] }}</td>
                                <td style="word-wrap:break-word; white-space: pre-wrap">{{$row['address']}}</td>
                                <td >{{$row['buy_name']}}<br>{{ $row['parent_name']}}</td>
                                <td>@if($row['plugin_id'] == 1)供应商：{{$row['shop_name']}}
                                    @elseif($row['plugin_id'] == 31)收银台：{{ $row['shop_name'] }}
                                    @elseif($row['plugin_id'] == 32)门店：{{ $row['shop_name'] }}
                                    @else {{ $row['shop_name'] }}
                                    @endif</td>
                                <td>{{$row['price']}}<br>{{ sprintf("%.2f",$row['cost_price'] + $row['dispatch_price']) }}</td>
                                <td>{{$row['commission'] ?: '0.00'}}<br>{{ $row['team_dividend'] ?: '0.00' }}</td>
                                <td>{{$row['area_dividend'] ?: '0.00'}}<br>{{ $row['micro_shop'] ?: '0.00' }}</td>
                                <td>{{$row['merchant'] ?: '0.00'}}<br>{{ $row['merchant_center'] ?: '0.00' }}</td>
                                <td>{{$row['point'] ?: '0.00'}}<br>{{ $row['love'] ?: '0.00' }}</td>
                                <td>{{sprintf("%.2f",$row['price'] - ($row['cost_price'] + $row['dispatch_price'] + $row['commission'] + $row['team_dividend']+ $row['area_dividend']+ $row['micro_shop'] + $row['merchant'] + $row['merchant_center']))}}</td>
                                <td>
                                    @if($row['status'] == '3')
                                        已完成
                                    @elseif($row['status'] == '-1')
                                        已取消
                                    @elseif($row['status'] == '-2')
                                        已退款
                                    @else
                                        未完成
                                    @endif
                                </td>
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
<script type="text/javascript" src="{{static_url('js/area/cascade_street.js')}}"></script>
<script language='javascript'>
    var province_id = $('#province_id').val();
    var city_id = $('#city_id').val();
    var district_id = $('#district_id').val();
    var street_id = $('#street_id').val();
    cascdeInit(province_id, city_id, district_id, street_id);
</script>
<script>
    $(function () {
        $('#export').click(function () {
            $('#form1').attr('action', '{!! yzWebUrl('charts.order.order-dividend.export') !!}');
            $('#form1').submit();
        });
    });
</script>
@endsection
