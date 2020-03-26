@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
<div class="rightlist">
<!-- 新增加右侧顶部三级菜单 -->
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">商城设置</a></li>
        </ul>
    </div>
    @include('setting.shop.tabs')
<!-- 新增加右侧顶部三级菜单结束 -->
    <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" >
        <div class="panel panel-default">
            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">模板选择</label>
                    <div class="col-sm-9 col-xs-12">
                        <select class='form-control' name='temp[style]'>
                            @foreach ($styles as $style)
                            <option value='{{ $style }}' @if ($style == $set['style']) selected @endif>{{ $style }}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
                {{--<div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">PC模板选择</label>
                    <div class="col-sm-9 col-xs-12">
                        <select class='form-control' name='temp[style_pc]'>
                            @foreach ($styles_pc as $style_pc)
                            <option value='{{ $style_pc }}' @if ($style_pc == $set['style_pc']) selected @endif>{{ $style_pc }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>--}}
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">主题选择</label>
                    <div class="col-sm-9 col-xs-12">
                        <select class='form-control' name='temp[theme]'>
                            <option value='style' @if ('style' == $set['theme']) selected @endif>默认主题</option>
                            <option value='style_red' @if ('style_red' == $set['theme']) selected @endif>红黑主题</option>
                        </select>
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
@endsection
