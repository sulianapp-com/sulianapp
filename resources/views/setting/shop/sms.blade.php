@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
<div class="rightlist">

    @include('layouts.tabs')
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            <div class='panel-body'>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">短信图形验证码</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="radio" name="sms[status]" value="1" @if ($set['status'] == 1) checked @endif/> 开启
                        &nbsp;&nbsp;
                        <input type="radio" name="sms[status]" value="0" @if ($set['status'] == 0) checked @endif/> 关闭
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">国家区号</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="radio" name="sms[country_code]" value="1" @if ($set['country_code'] == 1) checked @endif/> 显示
                        &nbsp;&nbsp;
                        <input type="radio" name="sms[country_code]" value="0" @if ($set['country_code'] == 0) checked @endif/> 不显示
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">短信设置</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline sms_type' type="1"><input type='radio' name='sms[type]' value='1' @if  ($set['type'] == 1 || !$set['type']) checked @endif/> 互亿无线</label>
                        <label class='radio-inline sms_type' type="2"><input type='radio' name='sms[type]' value='2' @if  ($set['type'] == 2) checked @endif /> 阿里大鱼</label>
                        <label class='radio-inline sms_type' type="3"><input type='radio' name='sms[type]' value='3' @if  ($set['type'] == 3) checked @endif /> 阿里云</label>
                    </div>
                </div>

                <div id="sms1" @if ($set['type'] == 2 || $set['type'] == 3) class="hide" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">国内短信账号</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[account]" class="form-control" value="{{ $set['account'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">国内短信密码</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[password]" class="form-control" value="{{ $set['password'] }}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 col-xs-12">
                            <hr>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">国际短信账号</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[account2]" class="form-control" value="{{ $set['account2'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">国际短信密码</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[password2]" class="form-control" value="{{ $set['password2'] }}" />
                        </div>
                    </div>
                </div>

                <div id="sms2"  @if ($set['type'] == 1 || $set['type'] == 3 || !$set['type']) class="hide" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12 alert alert-info">
                            请到 <a href='http://www.alidayu.com/' taget="_blank">阿里大鱼</a> 去申请开通,短信模板中必须包含code和product,请参考默认用户注册验证码设置。
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">AppKey:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[appkey]" class="form-control" value="{{ $set['appkey'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">secret:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[secret]" class="form-control" value="{{ $set['secret'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">短信签名:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[signname]"
                            class="form-control"
                            value="{{ $set['signname'] }}" placeholder="例如: 注册验证" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">注册短信模板ID:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[templateCode]" class="form-control" value="{{ $set['templateCode'] }}"  placeholder="例如: SMS_5057806" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">注册模板变量:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[product]" class="form-control" value="{{ $set['product'] }}"  placeholder="product=xx商城" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">找回密码短信模板ID:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[templateCodeForget]" class="form-control" value="{{ $set['templateCodeForget'] }}"  placeholder="例如: SMS_5057806" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">找回密码变量:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[forget]" class="form-control" value="{{ $set['forget'] }}"  placeholder="product=xx商城" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        {{--<div class="col-sm-9 col-xs-12 alert alert-info">--}}
                            {{--模板变量请以"变量名=变量值"形式填写,多个值请以回车换行。--}}
                        {{--</div>--}}
                    </div>
                </div>

                <div id="sms3" @if ($set['type'] == 1 || $set['type'] == 2 || !$set['type']) class="hide" @endif>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12 alert alert-info">
                            请到 <a href='https://dayu.aliyun.com' taget="_blank">阿里云</a> 去申请开通,短信模板中必须包含number；阿里云默认模板为code，将code改为number，请参考默认用户注册验证码设置。
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">AccessKeyId:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[aly_appkey]" class="form-control" value="{{ $set['aly_appkey'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">AccessKeySecret:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[aly_secret]" class="form-control" value="{{ $set['aly_secret'] }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">短信签名:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[aly_signname]"
                                   class="form-control"
                                   value="{{ $set['aly_signname'] }}" placeholder="例如: 注册验证" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">注册模板编号:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[aly_templateCode]" class="form-control" value="{{ $set['aly_templateCode'] }}"  placeholder="例如: SMS_5057806" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">找回密码模板编号:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[aly_templateCodeForget]" class="form-control" value="{{ $set['aly_templateCodeForget'] }}"  placeholder="例如: SMS_5057806" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额定时提醒:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[aly_templateBalanceCode]" class="form-control" value="{{ $set['aly_templateBalanceCode'] }}"  placeholder="例如: SMS_5057806" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品发货提醒:</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="sms[aly_templateSendMessageCode]" class="form-control" value="{{ $set['aly_templateSendMessageCode'] }}"  placeholder="例如: SMS_5057806" />
                        </div>
                    </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员充值提醒:</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="sms[aly_templatereChargeCode]" class="form-control" value="{{ $set['aly_templatereChargeCode'] }}"  placeholder="例如: SMS_5057806" />
                    </div>
                </div>
            </div>




                <!--
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">用于测试短信接口的手机号</label>
                    <div class="col-sm-9 col-xs-12">
                        {ifp 'sysset.save.sms'}
                        <input type="text" name="sms[password]" class="form-control" value="{$set['sms']['password']}" />
                        {else}
                        <input type="hidden" name="sms[password]" value="{$set['sms']['password']}"/>
                        <div class='form-control-static'>{$set['sms']['password']}</div>
                        {/if}
                    </div>
                </div>
                -->
              <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"  />
                     </div>
            </div>
                       
            </div>
        </div>     
    </form>
</div>
</div>
<script>
$(function(){
    $('.sms_type').click(function(){
        var type = $(this).attr('type');
        $('#sms1').hide();
        $('#sms2').hide();
        $('#sms3').hide();
        $('#sms'+type).removeClass('hide').show();
    });
});
</script>
@endsection
