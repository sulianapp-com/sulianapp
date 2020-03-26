@extends('layouts.base')

@section('content')
@section('title', trans('品牌详情'))
    <div class="w1200 m0a">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">商品品牌</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            {{--@if(isset($brandModel->id) && !empty($brandModel->id))--}}
            <input type="hidden" name="id" class="form-control" value="{{$brandModel->id}}"/>

            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>品牌名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="brand[name]" class="form-control" value="{{$brandModel->name}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">品牌别名</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="brand[alias]" class="form-control" value="{{$brandModel->alias}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">LOGO</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('brand[logo]', $brandModel->logo)!!}
                            <span class="help-block">建议尺寸: 100*100，或正方型图片 </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否推荐</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='brand[is_recommend]' value='1'
                                       @if($brandModel->is_recommend==1) checked @endif
                                /> 是
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='brand[is_recommend]' value='0'
                                       @if($brandModel->is_recommend==0) checked @endif
                                /> 否
                            </label>
                        </div>
                    </div> 
                   
                   
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">品牌描述</label>
                        <div class="col-sm-9 col-xs-12">
                            <!-- <textarea name="brand[desc]" class="form-control" cols="70">{{$brandModel->desc}}</textarea> -->
                            {!! yz_tpl_ueditor('brand[desc]', $brandModel->desc) !!}

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
                if ($(':input[name=brand[name]]').isEmpty()) {
                    Tip.focus(':input[name=brand[name]]', '请输入分类名称!');
                    return false;
                }
                return true;
            });
        });

    </script>
@endsection

