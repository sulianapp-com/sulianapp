@extends('layouts.base')

@section('content')

    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            {{--<div class="right-titpos">--}}
                {{--<ul class="add-snav">--}}
                    {{--<li class="active"><a href="#">注册协议</a></li>--}}
                {{--</ul>--}}
            {{--</div>--}}
        @include('layouts.tabs')
        {{--@include('setting.shop.tabs')--}}
        <!-- 新增加右侧顶部三级菜单结束 -->

            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class='panel-body'>


                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否启用</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'><input type='radio' name='protocol[protocol]' value='0'
                                                                   @if ($set['protocol'] == 0) checked @endif /> 禁用</label>
                                <label class='radio-inline'><input type='radio' name='protocol[protocol]' value='1'
                                                                   @if ($set['protocol'] == 1) checked @endif/> 启用</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                {!! yz_tpl_ueditor('protocol[content]', $set['content']) !!}
                            </div>
                        </div>

                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-success"
                                       onclick="return formcheck();"/>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('public.admin.mylink')
@endsection('content')
