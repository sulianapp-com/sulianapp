@extends('layouts.base')
@section('title', '优惠券领取发放记录')
@section('content')

<div class="w1200 m0a">
    @include('layouts.tabs')
    <form action="" method="post" class="form-horizontal" role="form" id="form1">
        <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="yun_shop" />
        <input type="hidden" name="do" value="plugin" />
        <input type="hidden" name="p" value="coupon" />
        <input type="hidden" name="method" value="coupon" />
        <input type="hidden" name="op" value="display" />

        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">

                <div class="form-group">
                    @if(empty($couponid))
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">优惠券名称</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <input type="text" class="form-control" name="couponname" placeholder='可搜索指定优惠券名称'/>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">用户昵称</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <input type="text" class="form-control" name="nickname" placeholder='可搜索指定用户'/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">领取还是发放</label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type="radio" value="1" name="getfrom">用户领取
                            </label>
                            <label class='radio-inline'>
                                <input type="radio" value="0" name="getfrom">商城发放
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">领取时间/发放时间</label>
                        <div class="col-sm-7 col-lg-9 col-xs-12">
                            <div class="col-sm-7 col-lg-9 col-xs-12">
                                <label class='radio-inline'>
                                    <input type='radio' value='0' name='timesearchswtich'>不搜索
                                </label>
                                <label class='radio-inline'>
                                    <input type='radio' value='1' name='timesearchswtich' >搜索
                                </label>
                                {!! tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d H:i', strtotime('-7 days 00:00')),'endtime'=>date('Y-m-d H:i', strtotime('today'))), true) !!}
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>
                        <div class="col-sm-8 col-lg-9 col-xs-12">
                            <button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <div class="panel panel-default">
        <div class="panel-heading">领取发放记录</div>
        <div class="panel-body">
            <table class="table table-hover table-responsive">
                <thead class="navbar-inner" >
                    <tr>
                        <th width="8%">优惠券名称</th>
                        <th width="8%">用户名称</th>
                        <th width="6%">获取途径</th>
                        <th width="50%">日志详情</th>
                        <th width="10%">创建时间</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $row)
                    <tr>
                        <td>
                            {{$row['coupon']['name']}}
                        </td>
                        <td>
                            {{$row['member']['nickname']}}
                        </td>
                        <td>
                            @if($row['getfrom'] == 1)
                                <label class="label label-danger">领取</label>
                            @elseif($row['getfrom'] == 4)
                                <label class="label label-success">购物赠送</label>
                            @elseif($row['getfrom'] == 5)
                                <label class="label label-success">会员转赠</label>
                            @elseif($row['getfrom'] == 6)
                                <label class="label label-success">签到奖励</label>
                            @elseif($row['getfrom'] == 0)
                                <label class="label label-warning">发放</label>
                            @endif
                        </td>
                        <td>
                            {{$row['logno']}}
                        </td>
                        <td>
                            {{date('Y-m-d H:i', $row['createtime'])}}
                        </td>
                    </tr>
                </tbody>
                @endforeach
            </table>
            {!! $pager !!}
        </div>
    </div>
</div>

@endsection('content')