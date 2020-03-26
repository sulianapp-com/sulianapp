@extends('layouts.base')
@section('title', '模板消息')
@section('content')
    <div class="page-content">
        <form  class="form-horizontal form-validate" enctype="multipart/form-data">
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" >模板ID</label>
                <div class="col-sm-9 col-xs-12">
                    <div class='form-control-static'>{{$template['template_id']}}</div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" >模板名称</label>
                <div class="col-sm-9 col-xs-12">
                    <div class='form-control-static'>{{$template['title']}}</div>
                </div>
            </div>
            @if(!isset($notice_type) && $notice_type !=2)
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" >所属行业</label>
                <div class="col-sm-9 col-xs-12">
                    <div class='form-control-static'>{{$template['primary_industry']}}/{{$template['deputy_industry']}}</div>
                </div>
            </div>
            @endif
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" >模板格式</label>
                <div class="col-sm-9 col-xs-12">
                    <div class='form-control-static'>
                        <textarea  style="height: 150px;resize: none;" class="form-control">{{$template['content']}}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label" >模板示例</label>
                <div class="col-sm-9 col-xs-12">
                    <div class='form-control-static'>
                        <textarea  style="height: 150px;resize: none;" class="form-control">{{$template['example']}}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <input type="button" name="back" onclick='history.back()'  value="返回列表" class="btn btn-default" />
                </div>
            </div>
        </form>
    </div>
@endsection