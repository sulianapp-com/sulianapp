@extends('layouts.base')
@section('title', trans('站点信息'))
@section('content')

    <link rel="stylesheet" type="text/css" href="{{static_url('css/font-awesome.min.css')}}">
    {{--<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>--}}
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#"><i class="fa fa-circle-o" style="color: #33b5d2;"></i>站点设置</a></li>
        </ul>
    </div>
    {{--<div class="main rightlist">--}}

    <form id="goods-edit" action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="panel-default panel-center">
            <div class="top">
                <ul class="add-shopnav" id="myTab">
                    <li class="active"><a href="#tab_basic">基本信息</a></li>
                </ul>
            </div>
            <div class="info">
                <div class="panel-body">
                    <div class="tab-content">

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">站点状态</label>
                            <div class="col-sm-9 col-xs-12">
                                <div style="float: left" id="ttttype">
                                    <label for="isshow3" class="radio-inline"><input type="radio" name="setdata[status]" value="0" id="isshow3" @if ($setdata->status == 0) checked="true" @endif />
                                        开启站点
                                    </label>
                                    <label for="isshow4" class="radio-inline"><input type="radio" name="setdata[status]" value="1" id="isshow4"  @if ($setdata->status == 1) checked="true" @endif />关闭站点</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">站点名称</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="setdata[name]" id="displayorder" placeholder="单行输入" class="form-control" value="{{$setdata->name}}" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">平台logo</label>
                            <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[site_logo]', $setdata->site_logo) !!}
                                <span class="help-block">请上传 100 * 60 的图片 </span>
                                @if (!empty($setdata->site_logo))
                                    <a href='{{tomedia($setdata->site_logo)}}' target='_blank'>
                                        <img src="{{tomedia($setdata->site_logo)}}" style='width:100px;border:1px solid #ccc;padding:1px' />
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">首图视频</label>
                            <div class="col-sm-9  col-md-6 col-xs-12">

                                {!! app\common\helpers\ImageHelper::tplFormFieldVideo('widgets[video][goods_video]', $goods->hasOneGoodsVideo->goods_video) !!}
                                {{--{!! tpl_form_field_video('widgets[video][goods_video]',$goods->hasOneGoodsVideo->goods_video) !!}--}}
                                <span class="help-block">设置后商品详情首图默认显示视频，建议时长9-30秒</span>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">其他图片</label>
                            <div class="col-sm-9  col-md-6 col-xs-12">

                                {!! app\common\helpers\ImageHelper::tplFormFieldMultiImage('goods[thumb_url]',$goods['thumb_url']) !!}
                                <span class="help-block">建议尺寸: 640 * 640 ，或正方型图片 </span>
                                @if (!empty($goods['piclist']))
                                    @foreach ($goods['piclist'] as $p)
                                        <a href='{{yz_tomedia($p)}}' target='_blank'>
                                            <img src="{{yz_tomedia($p)}}" style='height:100px;border:1px solid #ccc;padding:1px;float:left;margin-right:5px;' />
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-1 control-label">{{$lang['shopinfo']}}</label>
                            <div class="col-sm-9 col-xs-12 col-md-11">
                                {!! yz_tpl_ueditor('goods[content]', $goods['content']) !!}

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">favorite icon</label>
                            <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                                {--!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[title_icon]', $setdata->title_icon) !!--}
                                <span class="help-block">显示在浏览器标题的图标</span>
                                @if (!empty($setdata->title_icon))
                                    <a href='{{tomedia($setdata->title_icon)}}' target='_blank'>
                                        <img src="{{tomedia($setdata->title_icon)}}" style='width:100px;border:1px solid #ccc;padding:1px' />
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">登录页广告</label>
                            <div class="col-sm-9 col-xs-12 col-md-6 detail-logo">
                                {--!! app\common\helpers\ImageHelper::tplFormFieldImage('setdata[advertisement]', $setdata->advertisement) !!--}
                                <span class="help-block">请上传 400 * 250 px 图片</span>
                                @if (!empty($setdata->advertisement))
                                    <a href='{{tomedia($setdata->advertisement)}}' target='_blank'>
                                        <img src="{{tomedia($setdata->advertisement)}}" style='width:100px;border:1px solid #ccc;padding:1px' />
                                    </a>
                                @endif
                            </div>
                        </div>


                    </div>
                    <div class="form-group col-sm-12 mrleft40 border-t">
                        <input type="submit" name="submit" value="提交" class="btn btn-success"
                               onclick="return formcheck()"/>
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{--</div>--}}

    <script type="text/javascript">
        $("#form1").submit(function() {
            if ($("input[name='status']:checked").val() == 1) {
                if ($("textarea[name='reason']").val() == '') {
                    util.message('请填写站点关闭原因');
                    return false;
                }
            }
        });
        $("input[name='status']").click(function() {
            if ($(this).val() == 1) {
                $(".reason").show();
                var reason = $("input[name='reasons']").val();
                $("textarea[name='reason']").text(reason);
            } else {
                $(".reason").hide();
            }
        });
        $("input[name='mobile_status']").click(function() {
            if ($(this).val() == 0) {
                $("#login_type_status-1").attr("checked", false);
                $("#login_type_status-0").prop("checked", true);
                $("#login_type_status-1").attr("disabled", true);
            } else {
                $("#login_type_status-1").attr("disabled", false);
            }
        });
    </script>
@endsection