@extends('layouts.base')
@section('title', '申请协议')
@section('content')
    <section class="content">

        <form id="setform" action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    {{trans('申请协议')}}
                </div>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">是否开启</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio-inline">
                                <input type="radio" name="protocol[enable]" value="0"
                                       @if($info['enable'] == 0) checked="checked" @endif />
                                关闭</label>
                            <label class="radio-inline">
                                <input type="radio" name="protocol[enable]" value="1"
                                       @if($info['enable'] == 1) checked="checked" @endif />
                                开启</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">协议名称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="protocol[name]" class="form-control"
                                   value="{{$info['name']}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">协议内容</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! tpl_ueditor('protocol[content]', $info['content']) !!}

                        </div>
                    </div>


                <div class="form-group"></div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"
                               onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </section>@endsection