@extends('layouts.base')

@section('content')
@section('title', trans('收益广告'))
    <div class="w1200 m0a">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">收益广告</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
            <input type="hidden" name="id" class="form-control" value="{{$adv['id']}}"/>
            <div class="panel panel-default">
                <div class="panel-body">

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>排序</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[sort_by]" class="form-control"
                                   value="{{$adv['sort_by']}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>标题</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[name]" class="form-control" value="{{$adv['name']}}"/>

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>图片</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('adv[thumb]',
                            $adv['thumb'])!!}
                            <span class="help-block">建议尺寸: 640*1008</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                    style="color:red">*</span>链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[adv_url]" placeholder="请输入htpps://或http://开头的链接" class="form-control"
                                   value="{{$adv['adv_url']}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否显示</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class='radio-inline'>
                                <input type='radio' name='adv[status]' value='1'
                                       @if($adv['status'] == 1) checked @endif
                                /> 显示
                            </label>
                            <label class='radio-inline' style="margin-left: 55px;">
                                <input type='radio' name='adv[status]' value='0'
                                       @if($adv['status'] == 0) checked @endif
                                /> 不显示
                            </label>
                        </div>
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
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
<script type="text/javascript">
    function formcheck() {
        var shuzi = /^\d+$/;
        if (!shuzi.test($(':input[name="adv[sort_by]"]').val())) {
            Tip.focus(':input[name="adv[sort_by]"]', "必须为数字");
            return false;
        }
        if ($(':input[name="adv[name]"]').val() == '') {
            Tip.focus(':input[name="adv[name]"]', "请输入标题");
            return false;
        }

        if ($(':input[name="adv[thumb]"]').val() == '') {
            Tip.focus(':input[name="adv[thumb]"]', "请选择图片");
            return false;
        }

        var url = /^(http:\/\/|https:\/\/)/;
        if (!url.test($(':input[name="adv[adv_url]"]').val())) {
            Tip.focus(':input[name="adv[adv_url]"]', '格式错误');
            return false;
        }
        return true;
    }
</script>
@endsection

