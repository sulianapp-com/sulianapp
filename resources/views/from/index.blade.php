@extends('layouts.base')
@section('title', '商品表单')
@section('content')

    <div class="rightlist">
        @include('layouts.tabs')
        <form action="{{ yzWebUrl('from.div-from.store') }}" method="post"
              class="form-horizontal form" enctype="multipart/form-data">
            <div class='panel panel-default form-horizontal form'>

                <div class='panel panel-default'>

                    <div class='panel-heading'>表单使用规则</div>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">标题</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="div_from[explain_title]" class="form-control" value="{{$div_from['explain_title']}}"
                                       placeholder=""/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">内容</label>
                            <div class="col-sm-9 col-xs-12">
                                <textarea name="div_from[explain_content]" rows="10" class="form-control">{{ $div_from['explain_content'] }}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <span>ps：商品表单是针对跨境商品报税所开放使用的固定信息，非自定义表单信息</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="保存设置"
                               class="btn btn-primary col-lg-1" onclick='return formcheck()'/>
                    </div>
                </div>

            </div>
        </form>
    </div>

@endsection

