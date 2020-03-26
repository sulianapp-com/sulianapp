@extends('layouts.base')

@section('content')
@section('title', trans('过滤'))
    <div class="w1200 m0a">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">商品标签</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="id" class="form-control" value="{{$item->id}}"/>
            <div class="panel panel-default">
                <div class="panel-body">
                    @if(!empty($parent_id))
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">所属标签组:</label>
                            <div class="col-sm-9 col-xs-12 control-label" style="text-align:left;">
                                @if(!empty($parent)){{$parent->name}} @endif
                            </div>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12 control-label" style="text-align:left;">
                                <span style="color:red;">标签组</span>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="filter[name]" class="form-control" value="{{$item->name}}"/>

                        </div>
                    </div>
                    @if(empty($parent_id))
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='filter[is_show]' value='0'
                                       @if($item->is_show==0) checked @endif
                                /> 是
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' name='filter[is_show]' value='1'
                                       @if($item->is_show==1) checked @endif
                                /> 否
                            </label>
                        </div>
                    </div>
                    @endif
                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="hidden" name="parent_id" class="form-control"
                                   value="{{$parent_id}}"/>

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
    <script>
      
    </script>
@endsection

