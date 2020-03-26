@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
<div class="rightlist">

    @include('layouts.tabs')
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">

              <div class='panel-heading'>
                关注设置
            </div>

            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">启用会员关注</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="radio radio-inline">
                            <input type="radio" name="share[follow]" value="1"
                                   @if ($set['follow'] == 1 || empty($set['follow'])) checked @endif/> 是
                        </label>
                        <label class="radio radio-inline">
                            <input type="radio" name="share[follow]" value="0"
                                   @if (isset($set['follow']) && $set['follow'] == 0) checked @endif/> 否
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">关注引导页</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="share[follow_url]" class="form-control" value="{{ $set['follow_url'] }}" />
                        <span class='help-block'>用户未关注的引导页面，建议使用短链接：<a target="_blank" href="http://www.dwz.cn">短网址</a>
                    </div>
                </div>
            </div>
            <div class='panel-heading'>
                分享设置
            </div>
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="share[title]" class="form-control" value="{{ $set['title'] }}" />
                        <span class="help-block">不填写默认商城名称</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图标</label>
                    <div class="col-sm-9 col-xs-12">
                        {!! app\common\helpers\ImageHelper::tplFormFieldImage('share[icon]', $set['icon'])!!}
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享描述</label>
                    <div class="col-sm-9 col-xs-12">
                        <textarea style="height:100px;" name="share[desc]" class="form-control" cols="60">{{ $set['desc'] }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">分享连接</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group ">
                            <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{ $set['url'] }}" name="share[url]">
                            <span class="input-group-btn">
                                <button class="btn btn-default nav-link" type="button" data-id="PAL-00010" >选择链接</button>
                            </span>
                        </div>
                        <span class='help-block'>用户分享出去的连接，默认为首页</span>

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
        </div>
    </form>
</div>
</div>
{{--@include('setting.mylink')--}}

@include('public.admin.mylink')

{{--点击复制链接--}}
    <script>
        $('#cp').click(function () {
            util.clip(this, $(this).attr('data-url'));
        });
    </script>
@endsection
