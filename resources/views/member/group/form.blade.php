@extends('layouts.base')

@section('content')
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li><a href="#">会员分组设置</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->

        <div class="main">
        @if(!$groupModel->id)
            <form action="{{ yzWebUrl('member.member-group.store') }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
        @else
            <form action="{{ yzWebUrl('member.member-group.update', array('group_id' => $groupModel->id)) }}" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <input type="hidden" name="goroup_id" value="{{ $groupModel->id or '' }}"/>
        @endif
                <div class='panel panel-default'>
                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 分组名称</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="group[group_name]" class="form-control" value="{{ $groupModel->group_name or '' }}"/>
                                <div class='form-control-static'></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-success "/>
                                <input type="hidden" name="group[uniacid]" value="<?php echo \YunShop::app()->uniacid; ?>"/>
                                <input type="button" name="back" onclick='history.back()'  value="返回列表" class=" back btn btn-default" />
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection