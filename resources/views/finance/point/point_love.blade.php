@extends('layouts.base')

@section('content')

    <div class="rightlist">
        <form action="{{ yzWebUrl('finance.point-love.update') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">

            <div class="right-titpos">
                @include('layouts.tabs')
            </div>

            <div class="panel panel-default">

                <div class="panel-heading">积分自动转入设置</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝</label>
                        <div class="col-sm-9 col-xs-12">
                            <img src='{{ $memberModel->avatar }}' style='width:100px;height:100px;padding:1px;border:1px solid #ccc' />
                            {{ $memberModel->nickname }}
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员信息</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">姓名: {{ $memberModel->realname or $memberModel->nickname }} / 手机号: {{ $memberModel->mobile }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">转入比例</label>
                        <div class="col-sm-4 col-lg-3">
                            <div class='input-group' style="width: 260px;">
                                <input type="text" name="rate" value="{{ $memberModel->pointLove->rate or '' }}" class="form-control" />
                                <span class='input-group-addon'>%</span>
                            </div>
                            <span class='help-block'>自动转入{{ $love_name }}独立比例设置：为空、为零使用基础设置中自动转入比例，-1 此会员不自动转入</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分转入爱心值比例设置</label>
                        <div class="col-sm-4 col-lg-2">
                            <div class='input-group recharge-item'>
                                <span class="input-group-addon"></span>
                                <input type="text" name="transfer_integral" value="{{ $memberModel->pointLove->transfer_integral or '' }}"
                                       class="form-control wid100"/>
                                <span class='input-group-addon'>:</span>
                                <input type="text" name="transfer_love" value="{{ $memberModel->pointLove->transfer_love or '' }}"
                                       class="form-control wid100"/>
                            </div>
                            <div class="help-block">
                                如果积分转入爱心值比例设置为空、为零，则默认为1：1
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="hidden" name="member_id" value="{{ $memberModel->uid }}"/>
                            <input name="submit" type="submit" value="修 改" class="btn btn-success span2" onclick="return confirm('确认修改？');return false;">
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>

@endsection