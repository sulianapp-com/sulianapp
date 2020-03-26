@extends('layouts.base')

@section('content')
<div class="w1200 m0a">
    <div class="rightlist">


        @include('layouts.tabs')
        <form action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>
                <div class="'panel-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位一</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('adv[1][img]',
                            $adv->advs['1']['img'])!!}
                            <span class="help-block">建议尺寸:186.5 * 180</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位一链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[1][link]" class="form-control" value="{{$adv->advs['1']['link']}}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位二</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('adv[2][img]',
                            $adv->advs['2']['img'])!!}
                            <span class="help-block">建议尺寸:186.5 * 180</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位二链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[2][link]" class="form-control" value="{{$adv->advs['2']['link']}}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位三</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('adv[3][img]',
                            $adv->advs['3']['img'])!!}
                            <span class="help-block">建议尺寸:186.5 * 180</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位三链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[3][link]" class="form-control" value="{{$adv->advs['3']['link']}}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位四</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('adv[4][img]',
                            $adv->advs['4']['img'])!!}
                            <span class="help-block">建议尺寸:92.25 * 90</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位四链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[4][link]" class="form-control" value="{{$adv->advs['4']['link']}}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位五</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('adv[5][img]',
                            $adv->advs['5']['img'])!!}
                            <span class="help-block">建议尺寸:92.25 * 90</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位五链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[5][link]" class="form-control" value="{{$adv->advs['5']['link']}}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位六</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('adv[6][img]',
                            $adv->advs['6']['img'])!!}
                            <span class="help-block">建议尺寸:92.25 * 90</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位六链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[6][link]" class="form-control" value="{{$adv->advs['6']['link']}}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位七</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('adv[7][img]',
                            $adv->advs['7']['img'])!!}
                            <span class="help-block">建议尺寸:92.25 * 90</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">广告位七链接</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="adv[7][link]" class="form-control" value="{{$adv->advs['7']['link']}}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9">
                            <input type="submit" name="submit" value="保存设置" class="btn btn-primary col-lg"
                                   onclick='return formcheck()'/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
