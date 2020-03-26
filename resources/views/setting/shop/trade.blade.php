@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
<div class="rightlist">
<!-- 新增加右侧顶部三级菜单 -->
<div class="right-titpos">
	<ul class="add-snav">
		<li class="active"><a href="#">交易设置</a></li>
	</ul>
</div>

@include('layouts.tabs')
<!-- 新增加右侧顶部三级菜单结束 -->
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
			<div class="panel-heading">
				自动关闭未付款订单
			</div>
			<div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自动关闭未付款订单天数</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" name="trade[close_order_days]" class="form-control" value="{{ $set['close_order_days'] }}" />
                            <div class="input-group-addon">天</div>
                        </div>
                        <span class='help-block'>订单下单未付款，n天后自动关闭，0/空为不自动关闭</span>
                    </div>
                </div>

				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">自动关闭未付款订单执行间隔时间</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" name="trade[close_order_time]" class="form-control" value="{{ $set['close_order_time'] }}" />
							<div class="input-group-addon">分钟</div>
						</div>
						<span class='help-block'>执行自动关闭未付款订单操作的间隔时间，如果为空默认为 60分钟 执行一次关闭到期未付款订单</span>
					</div>
				</div>
			</div>

			<div class="panel-heading">
				自动收货
			</div>
			<div class='panel-body'>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自动收货天数</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" name="trade[receive]" class="form-control" value="{{ $set['receive'] }}" />
                            <div class="input-group-addon">天</div>
                        </div>
                        <span class='help-block'>订单发货后，用户收货的天数，如果在期间未确认收货，系统自动完成收货，0/空为不自动收货</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">自动收货执行间隔时间</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" name="trade[receive_time]" class="form-control" value="{{ $set['receive_time'] }}" />
                            <div class="input-group-addon">分钟</div>
                        </div>
                        <span class='help-block'>执行自动收货操作的间隔时间，如果为空默认为 60分钟 执行一次自动收货</span>
                    </div>
                </div>
			</div>
			<div class="panel-heading">
				交易设置
			</div>	

            <div class='panel-body'>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款</label>
                    <div class="col-sm-9">
                        <div class="">
                            <label class='radio-inline'><input type='radio' name='trade[refund_status]' value='1' @if ($refund_status == true) checked @endif/> 开启</label>
                            <label class='radio-inline'><input type='radio' name='trade[refund_status]' value='0' @if ($refund_status != true) checked @endif /> 关闭</label>
                        </div>
                        <span class='help-block'>开关判断前端退款按钮是否显示</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递配送</label>
                    <div class="col-sm-9">
                        <label class="radio-inline">
                            <input type="radio" name="trade[is_dispatch]" value="0" @if (empty($set['is_dispatch']) && $set['is_dispatch'] == 0) checked @endif />开启
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="trade[is_dispatch]" value="1"  @if ($set['is_dispatch'] == 1) checked @endif />关闭
                        </label>
                        <span class='help-block'></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">完成订单多少天内可申请退款</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <input type="text" name="trade[refund_days]" class="form-control" value="{{ $set['refund_days'] }}" />
                            <div class="input-group-addon">天</div>
                        </div>
                        <span class='help-block'>订单完成后 ，用户在x天内可以发起退款申请，设置0天不允许完成订单退款</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付后跳转链接</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group ">
                            <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{ $set['redirect_url'] }}" name="trade[redirect_url]">
                            <span class="input-group-btn">
                                <button class="btn btn-default nav-link" type="button" data-id="PAL-00010">选择链接</button>
                            </span>
                        </div>
                        <span class='help-block'>当用户下单支付后，跳转到指定的页面，默认跳转到商城首页</span>
                    </div>
                </div>
                {{--<div class="form-group">--}}
                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">退款说明</label>--}}
                    {{--<div class="col-sm-9">--}}
                        {{--<textarea  name="trade[refund_content]" class="form-control" value="{{ $set['refund_content'] }}" >{{ $set['refund_content'] }}</textarea>--}}
                        {{--<span class='help-block'>用户在申请退款页面的说明</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="form-group">--}}
                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示用户下单飘窗</label>--}}
                    {{--<div class="col-sm-9">--}}
                        {{--<label class='radio-inline'><input type='radio' name='trade[show_last_order]' value='1' @if ($set['show_last_order'] == 1) checked @endif/> 开启</label>--}}
                        {{--<label class='radio-inline'><input type='radio' name='trade[show_last_order]' value='0' @if (empty($set['show_last_order'])) checked @endif /> 关闭</label>--}}
                        {{--<span class='help-block'>是否显示商城用户下单飘窗提示</span>--}}
                    {{--</div>--}}
                {{--</div>--}}

			</div>

            <div class="panel-heading">
                发票设置
            </div>

            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支持发票类型
                    </label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="checkbox-inline">
                            <input type="checkbox" value="1" name='trade[invoice][papery]'
                                   @if ($set['invoice']['papery']) checked @endif /> 纸质发票
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" value="1" name='trade[invoice][electron]'
                                   @if ($set['invoice']['electron']) checked @endif /> 电子发票
                        </label>

                        <div class="help-block">当商品支持开发票时，买家可以选择以上勾选的类型</div>
                    </div>
                </div>
            </div>

			<div class="panel-heading">
				收货地址
			</div>	

            <div class='panel-body'>

                {{--<div class="form-group">--}}
                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">获取微信共享收货地址</label>--}}
                    {{--<div class="col-sm-9 col-xs-12">--}}
                        {{--<label class='radio-inline'><input type='radio' name='trade[share_address]' value='0' @if ($set['share_address'] == 0) checked @endif /> 关闭</label>--}}
                        {{--<label class='radio-inline'><input type='radio' name='trade[share_address]' value='1' @if ($set['share_address'] == 1) checked @endif/> 开启</label>--}}
                        {{--<span class='help-block'>是否在用户添加收货地址时候获取用户的微信收货地址</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="form-group">

                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启乡镇及街道地址选择
                    </label>
                    <div class="col-sm-9 col-xs-12">

                        <label class='radio-inline'><input type='radio' name='trade[is_street]' value='1' @if ($set['is_street'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='trade[is_street]' value='0' @if ($set['is_street'] == 0) checked @endif /> 关闭</label>

                    </div>
                </div>
			</div>
			<div class="panel-heading">
				支付日志 
			</div>	

			<div class='panel-body'>
          
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付回调日志</label>
                    <div class="col-sm-9">

                        <label class='radio-inline'><input type='radio' name='trade[pay_log]' value='0' @if ($set['pay_log'] == 0) checked @endif /> 关闭</label>
                        <label class='radio-inline'><input type='radio' name='trade[pay_log]' value='1' @if ($set['pay_log'] == 1) checked @endif/> 开启</label>
                        <span class='help-block'>支付回调日志，如果出现手机付款而后台显示待付款状态，请开启日志，查错误</span>
                        <span class='help-block'>日志路径为 addon/yun_shop/data/paylog/[公众号ID]</span>

                    </div>
                </div>
      
			</div>

            <div class="panel-heading">
                提现绑定手机号
            </div>

            <div class='panel-body'>
                <div class="form-group">

                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启
                    </label>
                    <div class="col-sm-9 col-xs-12">

                        <label class='radio-inline'><input type='radio' name='trade[is_bind]' value='1' @if ($set['is_bind'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='trade[is_bind]' value='0' @if ($set['is_bind'] == 0) checked @endif /> 关闭</label>

                    </div>
                </div>
            </div>


            <div class="panel-heading">
                支付协议开启
            </div>

            <div class='panel-body'>
                <div class="form-group">

                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启
                    </label>
                    <div class="col-sm-9 col-xs-12">

                        <label class='radio-inline'><input type='radio' name='trade[share_chain_pay_open]' value='1' @if ($set['share_chain_pay_open'] == 1) checked @endif/> 开启</label>
                        <label class='radio-inline'><input type='radio' name='trade[share_chain_pay_open]' value='0' @if ($set['share_chain_pay_open'] == 0) checked @endif /> 关闭</label>

                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付协议</label>
                <div class="col-sm-9 col-xs-12">
                    {!! yz_tpl_ueditor('trade[pay_content]', $set['pay_content']) !!}
                </div>
            </div>

			<div class="form-group"></div>
            <div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
				<div class="col-sm-9 col-xs-12">
					<input type="submit" name="submit" value="提交" class="btn btn-success"  />
				</div>
            </div>

	 </div>     
</form>
</div>
@include('public.admin.mylink')
@endsection
