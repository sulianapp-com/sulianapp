@extends('layouts.base')

@section('content')


    <div class="w1200 m0a">
        <div class="rightlist">

            @include('layouts.tabs')

            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class='panel-body'>


                        {{--<div class="form-group">--}}
                            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">订单支付流程</label>--}}
                            {{--<div class="col-sm-9 col-xs-12">--}}
                                {{--<label class="radio radio-inline">--}}
                                    {{--<input type="radio" name="order[paid_process]" value="1"--}}
                                           {{--@if ($set['paid_process']) checked @endif/>--}}
                                    {{--同步--}}
                                {{--</label>--}}
                                {{--<label class="radio radio-inline">--}}
                                    {{--<input type="radio" name="order[paid_process]" value="0"--}}
                                           {{--@if (empty($set['paid_process'])) checked @endif/>--}}
                                    {{--异步--}}
                                {{--</label>--}}
                                {{--<span class="help-block">--}}
                                    {{--获得推广资格的条件和分销商等级升级条件为同一个时，选择同步，否则选择异步（选择同步时会使订单付款变慢）--}}
                                {{--</span>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--<div class="form-group">--}}
                            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">订单完成流程</label>--}}
                            {{--<div class="col-sm-9 col-xs-12">--}}
                                {{--<label class="radio radio-inline">--}}
                                    {{--<input type="radio" name="order[receive_process]" value="1"--}}
                                           {{--@if ($set['receive_process']) checked @endif/>--}}
                                    {{--同步--}}
                                {{--</label>--}}
                                {{--<label class="radio radio-inline">--}}
                                    {{--<input type="radio" name="order[receive_process]" value="0"--}}
                                           {{--@if (empty($set['receive_process'])) checked @endif/>--}}
                                    {{--异步--}}
                                {{--</label>--}}
                                {{--<span class="help-block">--}}
                                    {{--获得推广资格的条件和分销商等级升级条件为同一个时，选择同步，否则选择异步（选择同步时会使订单完成变慢）--}}
                                {{--</span>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                            
                        <!-- 是否开启订单拆单  -->
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">按商品拆单</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="order[order_apart]" value="1"
                                           @if ($set['order_apart']) checked @endif/>
                                    开启
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="order[order_apart]" value="0"
                                           @if (empty($set['order_apart'])) checked @endif/>
                                    关闭
                                </label>
                                <span class="help-block">
                                    开启有用户购买平台自营商品，每一个商品拆成一个订单，订单原有逻辑不变。
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">首单商品</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group">
                                    <input type='text'
                                           class='form-control'
                                           id="many_good"
                                           value="@foreach($goods as $item){{$item['title']}};@endforeach"
                                           readonly/
                                    >
                                    <div class="input-group-btn">
                                        <button type="button" onclick="$('#modal-goods').modal()" class="btn btn-default" >选择商品</button>
                                    </div>
                                </div>
                                <div class="input-group multi-img-details" id='goods_id'>
                                    @foreach ($goods as $item)
                                        <div class="multi-item saler-item" style="height: 220px" openid="{{ $item['id'] }}">
                                            <img class="img-responsive img-thumbnail" src='{{ tomedia($item['thumb']) }}'
                                                 onerror="this.src='{{static_url('resource/images/nopic.jpg')}}'; this.title='图片未找到.'">
                                            <div class='img-nickname'>
                                                {{ $item['title'] }}[ID:{{$item['id']}}]
                                            </div>
                                            <input type="hidden" value="{{ $item['id'] }}"
                                                   name="order[goods][{{ $item['id'] }}]">
                                            <em onclick="remove_goods(this)" class="close">×</em>
                                        </div>
                                    @endforeach
                                </div>

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

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-success"
                                       onclick="return formcheck();"/>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        function formcheck() {

            return true;

        }

        function search_goods() {
            if( $.trim($('#search-kwd-goods').val())==''){
                Tip.focus('#search-kwd-goods','请输入关键词');
                return;
            }
            $("#module-goods").html("正在搜索....")
            $.get('{!! yzWebUrl('goods.goods.getSearchGoods') !!}', {
                keyword: $.trim($('#search-kwd-goods').val())
            }, function(dat){
                $('#module-menus-goods').html(dat);
            });
        }

        function select_good(o) {
            var html = '<div class="multi-item" style="height: 220px" openid="' + o.id + '">';
            html += '<img class="img-responsive img-thumbnail" src="' + o.thumb + '" onerror="this.src=\'{{static_url('resource/images/nopic.jpg')}}\'; this.title=\'图片未找到.\'">';
            html += '<div class="img-nickname" style="max-height: 58px;overflow: hidden">' + o.title + "[ID:"+ o.id +"]" + '</div>';
            html += '<input type="hidden" value="' + o.id + '" name="order[goods][' + o.id + ']">';
            html += '<em onclick="remove_goods(this)"  class="close">×</em>';
            html += '</div>';
            $("#goods_id").append(html);
            refresh_goods();
        }

        function remove_goods(obj) {
            $(obj).parent().remove();
            refresh_goods();
        }
        function refresh_goods() {
            var nickname = "";
            $('.multi-item').each(function () {
                nickname += " " + $(this).find('.img-nickname').html() + "; ";
            });
            $('#many_good').val(nickname);
        }
    </script>
    @include('public.admin.mylink')
@endsection('content')
