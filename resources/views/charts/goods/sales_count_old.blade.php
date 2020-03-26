@extends('layouts.base')
@section('content')

    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>

    <div class="w1200 m0a">
        <div class="rightlist" id="member-blade">

            @include('layouts.tabs')

            <div class="panel panel-default">
                <div class="panel-body">
                    <form action="" method="post" class="form-horizontal" role="form" id="form1">

                        <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                            <div class="">
                                <div class='input-group'>

                                    <div class='form-input'>
                                        <p class="input-group-addon" >会员ID</p>
                                        <input class="form-control price" style="width: 135px;" type="text" name="search[member_id]" value="{{ $search['member_id'] or ''}}">
                                    </div>

                                    <div class='form-input'>
                                        <p class="input-group-addon" >会员信息</p>
                                        <input class="form-control price" style="width: 135px;" type="text" name="search[member_info]" value="{{ $search['member_info'] or ''}}">
                                    </div>

                                </div>
                            </div>
                        </div>


                        <div class="form-group col-sm-1 col-lg-1 col-xs-12">
                            <div class="">
                                <input type="submit" class="btn btn-block btn-success" value="搜索">
                            </div>

                        </div>
                    </form>
                </div>



                <div class='panel-body'>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style='width:8%; text-align: center;'>排行</th>
                                <th style="width: 12%; text-align: center;">商品名称</th>
                                <th style="width: 10%; text-align: center;">状态</th>
                                <th style="width: 12%; text-align: center;">下单件数</th>
                                <th style="width: 12%; text-align: center;">下单金额</th>
                                <th style="width: 12%; text-align: center;">下单买家数</th>
                                <th style="width: 12%; text-align: center;">支付件数</th>

                                <th style="width: 15%; text-align: center;">
                                    支付金额
                                    <br />
                                    <span style="font-size: 10px;">(支付商品最终价格求和)</span>
                                </th>

                                <th style="width: 15%; text-align: center;">
                                    支付转化率
                                    <br />
                                    <span style="font-size: 10px;">(支付数量/下单数量)</span>
                                </th>

                                <th style="width: 12%; text-align: center;">支付件数</th>
                            </tr>
                        </thead>
                        <tbody>



                        @foreach($data as $key => $item)
                            <tr>
                                <td>
                                    @if($key <= 2)
                                        <labe class='label label-danger' style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</labe>
                                    @else
                                        <labe class='label label-default'  style='padding:8px;'>&nbsp;{{ $key + 1 }}&nbsp;</labe>
                                    @endif
                                </td>
                                <td>{{ $item['member_id'] }}</td>
                                <td>
                                    @if(!empty($item['avatar']))
                                        <img src='{{$item['avatar']}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    @if(empty($item['member_name']))
                                        未更新
                                    @else
                                        {{$item['member_name']}}
                                    @endif
                                </td>
                                <td>{{ $item['lv1_offline_count'] }}</td>
                                <td>{{ $item['lv2_offline_count'] }}</td>
                                <td>{{ $item['lv3_offline_count'] }}</td>
                                <td>{{ $item['offline_count'] }}</td>
                            </tr>
                        @endforeach



                    </table>
                    {!! $page !!}
                </div>
            </div>
        </div>
    </div>
@endsection
