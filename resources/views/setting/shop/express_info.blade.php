@extends('layouts.base')

@section('content')

    <script type="text/javascript">
        function formcheck() {
            return true;

        }
    </script>
<div class="w1200 m0a">
<div class="rightlist">

    @include('layouts.tabs')
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            <div class='panel-body'>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否收费接口</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class='radio-inline'><input type='radio' name='express_info[KDN][express_api]' value='1002'
                                                           @if ($set['KDN']['express_api'] == 1002 || empty($set['KDN']['express_api'])) checked @endif  />否</label>
                        <label class='radio-inline'><input type='radio' name='express_info[KDN][express_api]' value='8001'
                                                           @if ($set['KDN']['express_api'] == 8001) checked @endif/> 是</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">用户ID</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="express_info[KDN][eBusinessID]" class="form-control" value="{{ $set['KDN']['eBusinessID'] }}" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">API key</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="express_info[KDN][appKey]" class="form-control" value="{{ $set['KDN']['appKey'] }}" />
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">发货人手机号码</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="express_info[KDN][Mobile]" class="form-control" value="{{ $set['KDN']['Mobile'] }}" />
                        <p style="color: red;">注：顺丰快递必须填快递单号发货人的手机号码，否则查询不到物流信息</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">青龙配送编码</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="express_info[KDN][CustomerName]" class="form-control" value="{{ $set['KDN']['CustomerName'] }}" />
                        <span class='help-block'>在青龙系统申请一个商家编码，也叫青龙配送编码，格式：数字＋字母＋数字，举例：001K123456,<b>京东配送必填</b></span>
                        <span class='help-block'>使用快递鸟物流接口，请注意开通相关账号。官网： <a href="http://www.kdniao.com/reg?from=yunzhong">http://www.kdniao.com/reg?from=yunzhong</a>    ，开通详情步骤说明请联系客服。
                        </span>
                    </div>
                </div>

                        
                       <div class="form-group"></div>
            <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success " onclick="return formcheck()" />
                     </div>
            </div>

            </div>
        </div>     
    </form>
</div>
</div>
@endsection
