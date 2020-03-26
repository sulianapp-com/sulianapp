@extends('layouts.base')
@section('content')

<div class="w1200 m0a">
<div class="rightlist">

    @include('layouts.tabs')
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">不显示插件</label>
                    <div class="col-sm-9 col-xs-12">
                        <label class="checkbox-inline">
                            <input type="checkbox"  name="set[popularize]" onclick="cli(this)" value="1" @if (isset($info['popularize']) && $info['popularize'] == 1) checked @endif >推广中心</input>
                            <input type="checkbox"  name="set[vue_route][]" style="display: none" value="extension" @if (in_array('extension', $info['vue_route'])) checked @endif >
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9 col-xs-12">
                        @foreach($plugin_page as $item )
                            @if ($item['status'] == 1)
                                <label class="checkbox-inline" style="margin-left: 15px;margin-bottom: 10px">
                                    <input type="checkbox"  name="set[plugin_mark][]" value="{{$item['mark']}}" onclick="cli(this)" @if (array_intersect($item['url'], $info['vue_route'])) checked @endif >{{$item['title']}}</input>
                                    {{--<input type="checkbox"  name="set[plugin_mark][]" value="{{$item['mark']}}" onclick="cli(this)" @if (array_intersect($item['mark'], $info['plugin_mark'])) checked @endif >{{$item['title']}}</input>--}}
                                    @foreach($item['url'] as $value)
                                        <input type="checkbox"  name="set[vue_route][]" style="display: none" value="{{$value}}" @if (in_array($value, $info['vue_route'])) checked @endif >
                                    @endforeach
                                </label>
                            @else
                                <label class="checkbox-inline" style="margin-left: 15px;margin-bottom: 10px">
                                    <input type="checkbox"  name="set[vue_route][]"  onclick="cli(this)" value="{{$item['url']}}" @if (in_array($item['url'], $info['vue_route'])) checked @endif >{{$item['title']}}
                                    <input type="checkbox"  name="set[plugin_mark][]" style="display: none" value="{{$item['mark']}}" @if (array_intersect($item['mark'], $info['plugin_mark'])) checked @endif >
                                </label>
                            @endif
                        @endforeach
                    </div>

                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-6 col-xs-12">
                        <span class="help-block">
                            勾选推广中心，则不显示也无法访问推广中心页面，会员中心不显示我的推广。
                            其他插件如果勾选，则推广不显示插件前端页面入口。
                            勾选端口访问不显示的页面，将强制跳转到跳转页面。
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">H5跳转页面</label>
                    <div class="col-sm-9 col-xs-12">
                        <input type="text" name="set[callback_url]" placeholder="请输入https开头链接" class="form-control" value="{{$info['callback_url']}}">
                        {{--<span class="help-block"></span>--}}
                    </div>
                </div>

                @if ($info['min-app'])
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">小程序链接</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="input-group">
                            <input type="text" name="set[small_extension_link]" data-id="PAL-00012" class="form-control" placeholder="请填写指向的链接" value="{{$info['small_extension_link']}}" />
                            <span class="input-group-btn">
                                <button class="btn btn-default nav-app-link" type="button" data-id="PAL-00012" >选择链接</button>
                            </span>
                        </div>
                    </div>
                </div>
                @endif
                @include('public.admin.small')

                <div class="form-group"></div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"
                               onclick='return formcheck()'/>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
<script>
   function cli(obj) {
       if ($(obj).prop("checked")) {
           $(obj).parent().children(':checkbox').each(function(){
               $(this).prop('checked', true);
           });
       } else  {
           $(obj).parent().children(':checkbox').each(function(){
               $(this).prop('checked', false);
           });
       }

   }

</script>
@endsection
