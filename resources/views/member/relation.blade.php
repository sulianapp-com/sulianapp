@extends('layouts.base')
@section('title', '会员关系')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <style>
        .radio-inline {padding-top: 4px !important;}
    </style>
    <style type='text/css'>
        .multi-item {height: 110px;}
        .img-thumbnail {width: 100px;height: 100px}
        .img-nickname {position: absolute;bottom: 0px;line-height: 25px;height: 25px;color: #fff;text-align: center;width: 90px;bottom: 55px;background: rgba(0, 0, 0, 0.8);left: 5px;}
        .multi-img-details {padding: 5px;}
    </style>
    <div class="w1200 m0a">
        <div class="rightlist">

            @include('layouts.tabs')
            <form id="setform"  action="{{ yzWebUrl('member.member-relation.save') }}" method="post" class="form-horizontal form">
                <div class='panel panel-default'>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">启用关系链</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[status]" value="1" @if($set['status'] ==1)
                                    checked="checked"
                                            @endif/> 开启</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[status]" value="0" @if($set['status'] ==0)
                                    checked="checked"
                                            @endif/> 关闭</label>
                                <span class='help-block'>开启后首页也需要授权登录</span>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">获得发展下线权利条件</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[become]" value="0" @if($set['become'] ==0) checked="checked"
                                            @endif/> 无条件</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-6">
                                <label class="radio-inline"><input type="radio"  name="setdata[become]" value="1" @if($set['become'] ==1) checked="checked"
                                            @endif /> 申请</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline">
                                    <input type="radio" name="setdata[become]" value="2"
                                           @if($set['become'] == 2)
                                           checked="checked" @endif />
                                    或</label>
                                <label class="radio-inline">
                                    <input type="radio" name="setdata[become]" value="3"
                                           @if($set['become'] == 3)
                                           checked="checked" @endif />
                                    与</label>
                                <span class='help-block'><b>[或]</b>满足以下任意条件都可以升级<br><b>[与]</b>满足以下所有条件才可以升级</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-6">
                                <div class='input-group become' >
                                    <div class='input-group-addon become' >
                                        <label class="radio-inline" >
                                            <input type="checkbox"  name="setdata[become_term][2]" value="2" @if($set['become_term'][2]) checked="checked"@endif /> 消费达到
                                        </label>
                                    </div>
                                    <input type='text' class='form-control' name='setdata[become_ordercount]' value="{{$set['become_ordercount']}}" />
                                    <div class='input-group-addon' style="border:0" >次</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-6">
                                <div class='input-group' >
                                    <div class='input-group-addon'  >
                                        <label class="radio-inline" >
                                            <input type="checkbox"  name="setdata[become_term][3]" value="3" @if($set['become_term'][3]) checked="checked"@endif /> 消费达到
                                        </label>
                                    </div>
                                    <input type='text' class='form-control' name='setdata[become_moneycount]' value="{{$set['become_moneycount']}}" />
                                    <div class='input-group-addon' style="border:0">元</div>
                                </div>
                            </div>
                        </div>
                        <!-- Author:Y.yang Date:2016-04-08 Content:购买指定商品成为分销商 -->
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-6">
                                {{--<input type='hidden' class='form-control' id='goods_id' name='setdata[become_goods_id]' value="{{$set['become_goods_id']}}" />--}}
                                <div class='input-group' >
                                    <div class='input-group-addon'  >
                                        <label class="radio-inline" ><input type="checkbox"  name="setdata[become_term][4]" value="4" @if($set['become_term'][4]) checked="checked"
                                                    @endif /> 购买商品
                                        </label>
                                    </div>
                                    <input type='text' class='form-control' id='goods' value="@if(!empty($goods))@foreach($goods as $good){{$good['title']}};@endforeach
                                    @endif" readonly />
                                    <div class="input-group-btn">
                                        <button type="button" onclick="$('#modal-goods').modal()" class="btn btn-default" >选择商品</button>
                                    </div>
                                </div>
                                <span class="help-block">可指定多件商品，只需购买其中一件就可以成为推广员</span>
                                <div class="input-group multi-img-details" id='goods_id'>
                                    @foreach ($goods as $goods_id => $good)
                                        <div class="multi-item saler-item" openid="{{ $goods_id }}">
                                            <img class="img-responsive img-thumbnail" src='{{ tomedia($good['thumb']) }}'
                                                 onerror="this.src='{{static_url('resource/images/nopic.jpg')}}'; this.title='图片未找到.'">
                                            <div class='img-nickname' style="overflow: hidden">{{ $good['title'] }}</div>
                                            <input type="hidden" value="{{ $goods_id }}"
                                                   name="setdata[become_goods_id][{{ $goods_id }}]">
                                            <input type="hidden" value="{{ $goods_id }}"
                                                   name="setdata[become_goods][{{ $goods_id }}][goods_id]">
                                            <input type="hidden" value="{{ $good['title'] }}"
                                                   name="setdata[become_goods][{{ $goods_id }}][title]">
                                            <input type="hidden" value="{{ $good['thumb'] }}"
                                                   name="setdata[become_goods][{{ $goods_id }}][thumb]">
                                            <em onclick="remove_member(this)" class="close">×</em>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>

                        @if (app('plugins')->isEnabled('sales-commission'))
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                <div class="col-sm-6">
                                    <div class='input-group' >
                                        <div class='input-group-addon'  >
                                            <label class="radio-inline" >
                                                <input type="checkbox"  name="setdata[become_term][5]" value="5" @if($set['become_term'][5]) checked="checked"@endif /> 自购销售佣金累计达到
                                            </label>
                                        </div>
                                        <input type='text' class='form-control' name='setdata[become_selfmoney]' value="{{$set['become_selfmoney']}}" />
                                        <div class='input-group-addon' style="border:0">元</div>
                                    </div>
                                    <span class="help-block">该条件只针对销售佣金插件使用</span>
                                </div>
                            </div>
                    @endif
                    <!-- END -->
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[become_order]" value="0" @if($set['become_order'] ==0) checked="checked"
                                            @endif /> 付款后</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[become_order]" value="1" @if($set['become_order'] ==1) checked="checked"
                                            @endif /> 完成后</label>
                                <span class="help-block">消费条件统计的方式</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">成为下线条件</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[become_child]" value="0" @if($set['become_child'] ==0) checked="checked"
                                            @endif/> 首次点击分享链接</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[become_child]" value="1" @if($set['become_child'] ==1) checked="checked"
                                            @endif /> 首次下单</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[become_child]" value="2" @if($set['become_child'] ==2) checked="checked"
                                            @endif /> 首次付款</label>
                                <span class='help-block'>首次下单/首次付款： 无条件不可用</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">发展下线是否需要审核</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio"  name="setdata[become_check]" value="1" @if($set['become_check'] ==1) checked="checked"
                                            @endif/> 需要</label>
                                <label class="radio-inline"><input type="radio"  name="setdata[become_check]" value="0" @if($set['become_check'] ==0) checked="checked"
                                            @endif /> 不需要</label>
                                <span class="help-block">以上条件达到后，是否需要审核才能发展下线</span>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"> 推荐一个人奖励积分</label>
                            <div class="col-sm-6">
                                <div class='input-group become' >
                                    <input type='text' class='form-control' name='setdata[reward_points]' value="{{$set['reward_points']}}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"> 赠送积分最大人数</label>
                            <div class="col-sm-6">
                                <div class='input-group become' >
                                    <input type='text' class='form-control' name='setdata[maximum_number]' value="{{$set['maximum_number']}}" />
                                    <span class="help-block">不填或为0则不限制</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">推广中心跳转</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline">
                                    <input type="radio"  name="setting[is_jump]" value="1" @if($setting['is_jump'] == 1) checked="checked" @endif/>
                                    显示
                                </label>
                                <label class="radio-inline">
                                    <input type="radio"  name="setting[is_jump]" value="0" @if($setting['is_jump'] == 0) checked="checked" @endif/>
                                    不显示
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">链接</label>
                            <div class="col-sm-9 col-xs-9">
                                <div class="input-group">
                                    <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{ $setting['jump_link'] }}" name="setting[jump_link]">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default nav-link" type="button" data-id="PAL-00010" >选择链接</button>
                                    </span>
                                </div>
                                <span class="help-block">当会员没有获得推广资格的时候，点击推广中心跳转到指定的页面的，默认进入推广中心</span>
                            </div>
                        </div>
                        @include('public.admin.mylink')

                        <div class='panel-body'>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">小程序链接</label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="input-group">
                                        <input type="text" name="setting[small_jump_link]" data-id="PAL-00012" class="form-control" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{$setting['small_jump_link']}}" />
                                        <span class="input-group-btn">
                                    <button class="btn btn-default nav-app-link" type="button" data-id="PAL-00012" >选择链接</button>
                                </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @include('public.admin.small')

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">我的收入页面</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline">
                                    <input type="radio"  name="setdata[share_page]" value="1" @if($set['share_page'] == 1) checked="checked" @endif/>
                                    显示
                                </label>
                                <label class="radio-inline">
                                    <input type="radio"  name="setdata[share_page]" value="0" @if($set['share_page'] == 0) checked="checked" @endif/>
                                    不显示
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">收入明细购买者信息</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline">
                                    <input type="radio"  name="setdata[share_page_deail]" value="1" @if($set['share_page_deail'] == 1) checked="checked" @endif/>
                                    显示
                                </label>
                                <label class="radio-inline">
                                    <input type="radio"  name="setdata[share_page_deail]" value="0" @if($set['share_page_deail'] == 0) checked="checked" @endif/>
                                    不显示
                                </label>
                            </div>
                        </div>

                        <div class="form-group"></div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9">
                                <input type="submit" name="submit" value="提交" class="btn btn-success" onclick='return formcheck()' />
                                <input type="hidden" name="token" value="{{$var['token']}}" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Author:Y.yang Date:2016-04-08 Content:购买指定商品成为分销商，（选择商品的输入框和JS） -->
            <div id="modal-goods"  class="modal fade" tabindex="-1">
                <div class="modal-dialog" style='width: 920px;'>
                    <div class="modal-content">
                        <div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>选择商品</h3></div>
                        <div class="modal-body" >
                            <div class="row">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods" placeholder="请输入商品名称" />
                                    <span class='input-group-btn'><button type="button" class="btn btn-default" onclick="search_goods();">搜索</button></span>
                                </div>
                            </div>
                            <div id="module-menus-goods" style="padding-top:5px;"></div>
                        </div>
                        <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>
        function search_goods() {
            if( $.trim($('#search-kwd-goods').val())==''){
                Tip.focus('#search-kwd-goods','请输入关键词');
                return;
            }
            $("#module-goods").html("正在搜索....")
            $.get('{!! yzWebUrl('member.member-relation.query') !!}', {
                keyword: $.trim($('#search-kwd-goods').val())
            }, function(dat){
                $('#module-menus-goods').html(dat);
            });
        }
        function select_good(o) {
            // var html = "<input type='hidden' class='form-control' name='setdata[become_goods_id]["+ o.id+"]' value='' />"
            var html = '<div class="multi-item" openid="' + o.id + '">';
            html += '<img class="img-responsive img-thumbnail" src="' + o.thumb + '" onerror="this.src=\'{{static_url('resource/images/nopic.jpg')}}\'; this.title=\'图片未找到.\'">';
            html += '<div class="img-nickname" style="overflow: hidden">' + o.title + '</div>';
            html += '<input type="hidden" value="' + o.title + '" name="setdata[become_goods][' + o.id + '][title]">';
            html += '<input type="hidden" value="' + o.thumb + '" name="setdata[become_goods][' + o.id + '][thumb]">';
            html += '<input type="hidden" value="' + o.id + '" name="setdata[become_goods][' + o.id + '][goods_id]">';
            html += '<input type="hidden" value="' + o.id + '" name="setdata[become_goods_id][' + o.id + ']">';
            html += '<em onclick="remove_member(this)"  class="close">×</em>';
            html += '</div>';
            // $("#goods_id").val(o.id);
            // var data = $("#goods").val();
            // $("#goods").val(data+ o.title);
            $("#goods_id").append(html);
            refresh_members();
        }

        function remove_member(obj) {
            $(obj).parent().remove();
            refresh_members();
        }
        function refresh_members() {
            var nickname = "";
            $('.multi-item').each(function () {
                nickname += " " + $(this).find('.img-nickname').html() + "; ";
            });
            $('#goods').val(nickname);
        }


        function formcheck(){
            var become_child =$(":radio[name='setdata[become_child]']:checked").val();
            if( become_child=='1'  || become_child=='2' ){
                if( $(":radio[name='setdata[become]']:checked").val() =='0'){
                    alert('成为下线条件选择了首次下单/首次付款，发展下线不能选择无条件!')   ;
                    return false;
                }
            }
            var become = $(":radio[name='setdata[become]']").is(':checked');
            if (!become) {
                alert('获得发展下线权利条件不能为空')   ;
                return false;
            }
            return true;
        }
        function credit_avoid_audit() {
            if ($("input[name='setdata[credit_avoid_audit]']:checked").val() == 1) {
                $(".form-closewithdrawcheck").show();
            } else {
                $(".form-closewithdrawcheck").hide();
            }
        }
    </script>
@endsection