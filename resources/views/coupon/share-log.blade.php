@extends('layouts.base')
@section('title', '优惠券领取发放记录')
@section('content')

<div class="w1200 m0a">
    @include('layouts.tabs')
    <form action="" method="post" class="form-horizontal" role="form" id="form1">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="yun_shop" />
        <input type="hidden" name="do" value="coupon" />
        <input type="hidden" name="route" value="coupon.share-coupon.log" />

        <div class="panel panel-info">
            <div class="panel-body">

                <div class="form-group">

                    <div class="form-group col-xs-12 col-sm-3">
                        <input class="form-control" name="search[coupon_name]" type="text" value="{{$search['coupon_name']}}" placeholder="搜索优惠券名称">
                    </div>

                    <div class="form-group col-xs-12 col-sm-3">
                        <input class="form-control" name="search[share_uid]" type="text" value="{{$search['share_uid']}}" placeholder="分享者会员ID">
                    </div>

                    <div class="form-group col-xs-12 col-sm-3">
                        <input class="form-control" name="search[share_name]" type="text"
                               value="{{$search['share_name']}}" placeholder="分享者会员昵称/姓名/手机号">
                    </div>

                    <div class="form-group col-xs-12 col-sm-3">
                        <input class="form-control" name="search[receive_uid]" type="text"
                               value="{{$search['receive_uid']}}" placeholder="领取者会员ID">
                    </div>


                    <div class="form-group col-xs-12 col-sm-3">
                        <input class="form-control" name="search[receive_name]" type="text"
                               value="{{$search['receive_name']}}" placeholder="领取者会员昵称/姓名/手机号">
                    </div>

                    <div class="form-group"></div>


                    <div class="form-group">
                        <label class="col-sm-1 control-label" style="padding-top:13px">领取时间</label>
                        <div class="col-sm-7 col-lg-9 col-xs-12">
                            <div class="col-sm-7 col-lg-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' value='0' name='search[time_search]' @if (empty($search['time_search'])) checked @endif>不搜索
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' value='1' name='search[time_search]' @if ($search['time_search']) checked @endif >搜索
                                </label>
                                {!! tpl_form_field_daterange('time', ['start'=>$search['time']['start'],'end'=>$search['time']['end']], true) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {{--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>--}}
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <button id="search" class="btn btn-success "><i class="fa fa-search"></i>搜索</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <div class="panel panel-default">
        <div class="panel-heading">领取数量：{{$list['total']}} 张</div>
        <div class="panel-body">
            <table class="table table-hover table-responsive">
                <thead class="navbar-inner" >
                    <tr>
                        <th width="4%">ID</th>
                        <th width="8%">优惠券名称</th>
                        <th width="6%" style="text-align: center">分享人</th>
                        <th width="6%" style="text-align: center">领取人</th>
                        <th width="30%">日志详情</th>
                        <th width="10%">创建时间</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list['data'] as $row)
                    <tr>
                        <td>{{$row['id']}}</td>
                        <td>{{$row['coupon_name']}}</td>
                        <td style="text-align: center;">
                            <img src='{{$row['share_member']['avatar']}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                            <br/>
                            <a href="{!! yzWebUrl('member.member.detail',['id' => $row['share_member']['uid]']])!!}">@if ($row['share_member']['nickname']) {{$row['share_member']['nickname']}} @else {{$row['share_member']['mobile']}} @endif</a>
                        </td>
                        <td style="text-align: center;">
                            <img src='{{$row['receive_member']['avatar']}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' />
                            <br/>
                            <a href="{!! yzWebUrl('member.member.detail',['id' => $row['receive_member']['uid]']])!!}">@if ($row['receive_member']['nickname']) {{$row['receive_member']['nickname']}} @else {{$row['receive_member']['mobile']}} @endif</a>
                        </td>
                        <td>{{$row['log']}}</td>
                        <td>{{$row['created_at']}}</td>
                    </tr>
                </tbody>
                @endforeach
            </table>
            {!! $pager !!}
        </div>
    </div>
</div>
<script language="javascript">

</script>

@endsection('content')