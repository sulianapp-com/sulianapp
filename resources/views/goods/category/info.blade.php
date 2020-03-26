@extends('layouts.base')

@section('content')
@section('title', trans('商品分类详情'))
    <div class="w1200 m0a">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">商品分类</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="id" class="form-control" value="{{$item->id}}"/>
            <div class="panel panel-default">
                <div class="panel-body">
                    @if($item->id)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类链接(点击复制)</label>
                            <div class="col-sm-9 col-xs-12">
                                <p class='form-control-static'>
                                    <a href='javascript:;' data-url="{{yzAppFullUrl('/catelist/'.$item->id, ['type'=>5])}}" title='点击复制链接' id='cp'>
                                        {{yzAppFullUrl('/catelist/'.$item->id, ['type'=>5])}}
                                    </a>
                                </p>
                            </div>
                        </div>
                    @endif

                    @if(!empty($parent))
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">上级分类</label>
                            <div class="col-sm-9 col-xs-12 control-label" style="text-align:left;">
                                @if(!empty($parent)){{$parent->name}} @endif
                            </div>
                        </div>
                    @endif

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="category[display_order]" class="form-control"
                                   value="{{$item->display_order}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>分类名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="category[name]" class="form-control" value="{{$item->name}}"/>

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类图片</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('category[thumb]',
                            $item->thumb)!!}
                            <span class="help-block">建议尺寸: 100*100，或正方型图片 </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类描述</label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea name="category[description]" class="form-control"
                                      cols="70">{{$item->description}}</textarea>
                        </div>
                    </div>
                    @if($level<=2)
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">移动端分类广告</label>
                            <div class="col-sm-9 col-xs-12">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('category[adv_img]',
                                $item->adv_img)!!}
                                <span class="help-block">建议尺寸: 640*320</span>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">分类广告链接</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group ">
                                    <input class="form-control" type="text" data-id="PAL-00010"
                                           placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{$item->adv_url}}"
                                           name="category[adv_url]">
                                <span class="input-group-btn">
                                    <button class="btn btn-default nav-link" type="button" data-id="PAL-00010">
                                        选择链接
                                    </button>
                                </span>
                                </div>
                            </div>
                        </div>
                    @endif
            @include('goods.category.search-group');
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否推荐</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='category[is_home]' value='1'
                                       @if($item->is_home==1) checked @endif
                                /> 是
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='category[is_home]' value='0'
                                       @if($item->is_home==0) checked @endif
                                /> 否
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='category[enabled]' value='1'
                                       @if($item->enabled==1) checked @endif
                                /> 是
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='category[enabled]' value='0'
                                       @if($item->enabled==0) checked @endif
                                /> 否
                            </label>
                        </div>
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="hidden" name="category[parent_id]" class="form-control"
                                   value="{{$item->parent_id}}"/>
                            <input type="hidden" name="category[level]" class="form-control"
                                   value="{{$item->level}}"/>

                            <input type="submit" name="submit" value="提交" class="btn btn-success"
                                   onclick="return formcheck()"/>
                            <input type="button" name="back" onclick='history.back()' style=''
                                   value="返回列表"
                                   class="btn btn-default back"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @include('public.admin.mylink');

    {{--点击复制链接--}}
    <script>
        $('#cp').click(function () {
            util.clip(this, $(this).attr('data-url'));
        });
    </script>
@endsection

