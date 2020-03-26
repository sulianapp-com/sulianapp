@extends('layouts.base')
@section('content')

    <script type="text/javascript">
        function formcheck() {
            var thumb = /\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/;

            if ($(':input[name="shop[logo]"]').val() != '') {
                if (!thumb.test($(':input[name="shop[logo]"]').val())) {
                    Tip.focus(':input[name="shop[logo]"]', '图片类型必须是.gif,jpeg,jpg,png中的一种.');
                    return false;
                }
            }

//            if ($(':input[name="shop[img]"]').val() != '') {
//                if (!thumb.test($(':input[name="shop[img]"]').val())) {
//                    Tip.focus(':input[name="shop[img]"]', '图片类型必须是.gif,jpeg,jpg,png中的一种.');
//                    return false;
//                }
//            }

            if ($(':input[name="shop[signimg]"]').val() != '') {
                if (!thumb.test($(':input[name="shop[signimg]"]').val())) {
                    Tip.focus(':input[name="shop[signimg]"]', '图片类型必须是.gif,jpeg,jpg,png中的一种.');
                    return false;
                }
            }
            return true;

        }

    </script>

<div class="w1200 m0a">
<div class="rightlist">

    @include('layouts.tabs')
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="shopform" >
        <div class="panel panel-default">
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">关闭站点</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="shop[close]" value="0"
                                   @if (empty($set['close'])) checked @endif/> 否
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="shop[close]" value="1"
                                   @if ($set['close'] == 1) checked @endif/> 是
                        </label>
                    </div>
                </div>
                {{--<div class="form-group">--}}
                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">强制https跳转</label>--}}
                    {{--<div class="col-sm-9 col-xs-12">--}}
                        {{--<label class="radio radio-inline">--}}
                            {{--<input type="radio" name="shop[https]" value="0"--}}
                                   {{--@if (empty($set['https'])) checked @endif/> 否--}}
                        {{--</label>--}}
                        {{--<label class="radio radio-inline">--}}
                            {{--<input type="radio" name="shop[https]" value="1"--}}
                                   {{--@if ($set['https'] == 1) checked @endif/> 是--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城名称</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="shop[name]" class="form-control" value="{{ $set['name']}}" />

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城LOGO</label>
                    <div class="col-sm-9 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('shop[logo]', $set['logo'])!!}
                        <span class='help-block'>正方型图片</span>
                    </div>
                </div>
                {{--<div class="form-group">--}}
                    {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">店招</label>--}}
                    {{--<div class="col-sm-9 col-xs-12">--}}
                        {{--{!! app\common\helpers\ImageHelper::tplFormFieldImage('shop[img]', $set['img']) !!}--}}
                        {{--<span class='help-block'>商城首页店招，建议尺寸640*450</span>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城海报</label>
                    <div class="col-sm-9 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('shop[signimg]', $set['signimg']) !!}
                        <span class='help-block'>推广海报，建议尺寸640*640</span>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商城业绩显示</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="shop[achievement]" value="0"
                                   @if (empty($set['achievement'])) checked @endif/> 否
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="shop[achievement]" value="1"
                                   @if ($set['achievement'] == 1) checked @endif/> 是
                        </label>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级查看权限</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="shop[member_level][]"  value="-1" @if(in_array(-1,$set['member_level'])) checked @endif> 全部会员等级
                                </label>
                                @foreach($level as $row)
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="shop[member_level][]"  value="{{$row['id']}}" @if(in_array($row['id'],$set['member_level']))  checked @endif> {{$row['level_name']}}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">客服链接</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="shop[cservice]" class="form-control" value="{{ $set['cservice']}}" />
                        <span class='help-block'>支持任何客服系统的聊天链接，例如QQ、企点、53客服、百度商桥等</span>
                    </div>
                </div>
                <!--
                 <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">全局统计代码</label>
                    <div class="col-sm-9 col-xs-12">
			            <textarea name="shop[diycode]" class="form-control richtext" cols="70" rows="5">{{ $set['diycode']}}</textarea>
                    </div>
                </div>
                -->
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">版权信息</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="shop[copyright]" class="form-control" value="{{ $set['copyright']}}" />
                        <span class='help-block'>版权所有 © 后面文字字样</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额字样</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="shop[credit]" class="form-control" value="{{ $set['credit']}}" />
                        <span class='help-block'>会员中心页面余额字样的自定义功能</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分字样</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="shop[credit1]" class="form-control" value="{{ $set['credit1']}}" />
                        <span class='help-block'>会员中心页面积分字样的自定义功能</span>
                    </div>
                </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">百度统计</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="shop[baidu]" class="form-control" value="{{ $set['baidu']}}" />
                            <span class='help-block'>请填入百度统计的站点ID（百度统计只能统计域名下的流量统计，链接#号后面的百度统计可能会默认截取掉）</span>
                        </div>
                    </div>
                  <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"  onclick="return formcheck()"/>
                     </div>
            </div>

            </div>
        </div>
    </form>
</div>
</div>
@endsection
