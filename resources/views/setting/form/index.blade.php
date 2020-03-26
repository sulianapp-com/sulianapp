@extends('layouts.base')
@section('title', '自定义表单')
@section('content')
    <style>
        .del_btn {
            display: inline-block;
            color: #ffffff;
            height: 34px;
            line-height: 34px;
            float: right;
            margin-right: 320px;
            padding: 0px 16px;
            background-color: #f15353;
            border-color: #f15353;
            cursor: pointer;
        }
    </style>
    <div class="w1200 m0a">
        <div class="rightlist">

        @include('layouts.tabs')
            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="shopform" onsubmit="return checkform()" >
                <div class="panel panel-default">

                    <div class="panel-heading">
                        默认
                    </div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">性别</label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float: left" id="ttttype">
                                    <label class='radio-inline'><input type='radio' name='base[sex]' value='1' @if ($set['base']['sex'] == 1) checked @endif/> 开启</label>
                                    <label class='radio-inline'><input type='radio' name='base[sex]' value='0' @if ($set['base']['sex'] == 0) checked @endif /> 关闭</label>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">详细地址</label>
                            <div class="col-sm-9 col-xs-12">

                                <label class='radio-inline'><input type='radio' name='base[address]' value='1' @if ($set['base']['address'] == 1) checked @endif/> 开启</label>
                                <label class='radio-inline'><input type='radio' name='base[address]' value='0' @if ($set['base']['address'] == 0) checked @endif /> 关闭</label>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">生日</label>
                            <div class="col-sm-9 col-xs-12">

                                <label class='radio-inline'><input type='radio' name='base[birthday]' value='1' @if ($set['base']['birthday'] == 1) checked @endif/> 开启</label>
                                <label class='radio-inline'><input type='radio' name='base[birthday]' value='0' @if ($set['base']['birthday'] == 0) checked @endif /> 关闭</label>

                            </div>
                        </div>
                    </div>

                    <div class="panel-heading">
                        自定义 <label id="form_add" style="margin-bottom: inherit;"><a class="btn" href="javascript:;" style="margin-left:10px; background-color: #00a65a; border-color: #008d4c; color: #ffffff"><i class="fa fa-plus"></i> 添加</a></label>
                    </div>
                    <div class='panel-body' id="container">
                    @if ($set['form'])
                        @foreach ($set['form'] as $rows)
                            <span class="part_content">
                            <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                            <div class="col-sm-6">
                                <div class="input-group col-xs-6">
                                    <input type="text" name="form[sort][]" class="form-control" value="{{$rows['sort']}}" />
                                </div>
                            </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义表单名称</label>
                                <div class="col-sm-6">
                                    <div class="input-group col-xs-6" style="display: inline-block">
                                        <input type="text" name="form[name][]" class="form-control" value="{{$rows['name']}}" />
                                    </div>
                                    <div class="del_btn" data-code="{{$rows['del']}}">删除</div>
                                </div>
                            </div>
                        </span>
                        @endforeach
                    @endif
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success"  />
                        </div>
                    </div>

                    <div id="tpl" style="display: none;">
                        <span class="part_content">
                            <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
                            <div class="col-sm-6">
                                <div class="input-group col-xs-6">
                                    <input type="text" name="form[sort][]" class="form-control" value="" />
                                </div>
                            </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义表单名称</label>
                                <div class="col-sm-6">
                                    <div class="input-group col-xs-6" style="display: inline-block">
                                        <input type="text" name="form[name][]" class="form-control" value="" />
                                    </div>
                                    <div class="del_btn" data-code="0">删除</div>
                                </div>
                            </div>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function () {
            $(document).on('click', '#form_add', function() {
                $('#container').append($('#tpl').html());
            });

            $(document).on('click', '.del_btn', function() {
                if ($(this).data('code') == 0) {
                    $(this).parents('.part_content').remove();
                }

                if ($(this).data('code') == 1 && confirm('此数据项已存在数据，是否强制删除')) {
                        $(this).parents('.part_content').remove();
                }
            });
        });

        function checkform()
        {
            var res = true;
            $('#tpl').remove();

            $('#container input').each(function () {
                if ($(this).val() == '') {
                    alert('表单数据不能为空');
                    res = false;

                    return res;
                }
            });

            return res;
        }
    </script>

@endsection