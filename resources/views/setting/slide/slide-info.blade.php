@extends('layouts.base')

@section('content')
    <div class="w1200 m0a">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">幻灯片</a></li>
            </ul>
        </div>


        @include('layouts.tabs')
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            {{--@if(isset($slideModel->id) && !empty($slideModel->id))--}}
            <input type="hidden" name="id" class="form-control" value="{{$slideModel->id}}"/>
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="slide[display_order]" class="form-control" value="{{$slideModel->display_order}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>幻灯片标题</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="slide[slide_name]" class="form-control" value="{{$slideModel->slide_name}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">幻灯片图片</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('slide[thumb]', $slideModel->thumb)!!}
                            <span class="help-block">建议尺寸:640 * 350 , 请将所有幻灯片图片尺寸保持一致 </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">幻灯片连接</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="input-group ">
                                <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{$slideModel['link']}}" name="slide[link]">
                            <span class="input-group-btn">
                                <button class="btn btn-default nav-link" type="button" data-id="PAL-00010" >选择链接</button>
                            </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="slide[enabled]" value="1" @if ($slideModel->enabled == 1) checked @endif /> 是
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="slide[enabled]" value="0" @if ($slideModel->enabled == 0 ) checked @endif/> 否
                            </label>
                        </div>
                    </div>


                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"
                                   onclick="return formcheck()"/>
                            <input type="button" name="back" onclick='history.back()' value="返回列表"
                                   class="btn btn-default back"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script language='javascript'>
        require(['bootstrap'], function ($) {
            $('form').submit(function () {
                if ($(':input[name=slide[slide_name]]').isEmpty()) {
                    Tip.focus(':input[name=slide[slide_name]]', '请输入幻灯片标题!');
                    return false;
                }
                return true;
            });
        });

    </script>
    @include('public.admin.mylink')
@endsection('content')

