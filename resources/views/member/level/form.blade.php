@extends('layouts.base')

@section('content')
<style type="text/css">
    .multi-item{
        margin-bottom: 65px;
        z-index: 1;
    }
</style>
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="rightlist">
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li><a href="#">会员等级设置</a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->

        <div id="member-level" class="main">
            @if(!$levelModel->id)
                <form action="{{ yzWebUrl('member.member-level.store') }}" method="post" class="form-horizontal form"
                      enctype="multipart/form-data">
                    @else
                        <form action="{{ yzWebUrl('member.member-level.update') }}" method="post"
                              class="form-horizontal form" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="{{ $levelModel->id }}">
                            @endif
                            <div class='panel panel-default'>
                                <div class='panel-body'>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                                    style='color:red'>*</span>等级权重</label>
                                        <div class="col-sm-9 col-xs-12">
                                            <input type="text" name="level[level]" class="form-control"
                                                   value="{{ $levelModel->level }}"/>
                                            <span class='help-block'>等级权重，数字越大越高级</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span
                                                    style='color:red'>*</span> 等级名称</label>
                                        <div class="col-sm-9 col-xs-12">
                                            <input type="text" name="level[level_name]" class="form-control"
                                                   value="{{ $levelModel->level_name }}"/>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">升级条件</label>
                                        <div class="col-sm-9 col-xs-12">
                                            <div class='input-group'>
                                                @if(empty($shopSet['level_type']))
                                                    <span class='input-group-addon'>订单金额满</span>
                                                    <input type="text" name="level[order_money]" class="form-control"
                                                           value="{{ $levelModel->order_money }}"/>
                                                    <span class='input-group-addon'>元</span>
                                                @endif

                                                @if($shopSet['level_type'] == 1)
                                                    <span class='input-group-addon'>订单数量满</span>
                                                    <input type="text" name="level[order_count]" class="form-control"
                                                           value="{{ $levelModel->order_count }}"/>
                                                    <span class='input-group-addon'>个</span>
                                                @endif

                                                @if($shopSet['level_type'] == 3)
                                                    <div class="input-group row">
                                                        <label class="radio-inline col-xs-12 col-sm-12">
                                                            团队业绩满(自购+一级+二级)
                                                            <input type="text" name="level[team_performance]"
                                                                   value="{{ $levelModel->team_performance }}">
                                                            元
                                                        </label>
                                                    </div>
                                                @endif

                                                @if($shopSet['level_type'] == 2)
                                                   <!--  <div class="col-sm-12">
                                                        <input type='hidden' class='form-control' id='goodsid'
                                                               name='level[goods_id]' value="{{ $levelModel->goods->id }}"/>
                                                        <div class='input-group'>
                                                            <div class='input-group-addon'
                                                                 style='border:none;background:#fff;'>
                                                                <label class="radio-inline" style='margin-top:-3px;'>
                                                                    购买指定商品</label>
                                                            </div>
                                                            <input type='text' class='form-control' id='goods'
                                                                   value="{{ $levelModel->goods->title }}" readonly/>
                                                            <div class="input-group-btn">
                                                                <button type="button"
                                                                        onclick="$('#modal-goods').modal()"
                                                                        class="btn btn-default">选择商品
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                    <div class='input-group'>
                                                        <div class='input-group-addon'>
                                                            <label class="radio-inline">
                                                                购买商品
                                                            </label>
                                                        </div>
                                                        <input type='text' class='form-control' id='goods' value="@if(!empty($goods))@foreach($goods as $good){{$good['title']}};@endforeach
                                                        @endif" readonly />
                                                        <div class="input-group-btn">
                                                            <button type="button" onclick="$('#modal-goods').modal()" class="btn btn-default" >选择商品</button>
                                                        </div>
                                                    </div>
                                                <span class="help-block">可指定多件商品，只需购买其中一件就可以升级</span>
                                                <div class="input-group multi-img-details" id='goods_id' style="margin-bottom: 50px">
                                                    @foreach ($goods as $k => $good)
                                                        <div class="multi-item saler-item" openid="{{ $goods[$k]['id'] }}">
                                                            <img  width="130px" height="120px" style="margin-bottom: 20px" class="img-responsive img-thumbnail" src="{{ yz_tomedia($good['thumb']) }}"
                                                                 onerror="this.src='{{static_url('resource/images/nopic.jpg')}}'; this.title='图片未找到.'">
                                                            <div class='img-nickname' style="overflow: hidden">{{ $good['title'] }}</div>

                                                            <input type="hidden" value="{{ $good['id'] }}"
                                                                   name="level[goods][{{$k}}][goods_id]">

                                                            <input type="hidden" value="{{ $good['title'] }}"
                                                                   name="level[goods][{{$k}}][title]">

                                                            <input type="hidden" value="{{ $good['thumb'] }}"
                                                                   name="level[goods][{{$k}}][thumb]">

                                                            <em onclick="remove_member(this)" class="close">×</em>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                    <div class="input-group">
                                                        <div class="input-group-addon" style='width: auto !important;'>等级有效天数</div>
                                                        <input type="text" name="level[validity]" class="form-control" value="{{ $levelModel->validity }}"/>
                                                        <div class="input-group-addon" style='width: auto !important;'>天</div>
                                                    </div>
                                                @endif
                                            </div>
                                            <span class='help-block'>会员升级条件，不填写默认为不自动升级, 设置<a
                                                        href="{{ yzWebUrl('setting.shop.member') }}">【会员升级依据】</a> </span>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">折扣</label>
                                        <div class="col-sm-9 col-xs-12">
                                            <input type="text" name="level[discount]" class="form-control"
                                                   value="{{ $levelModel->discount }}"/>
                                            <span class='help-block'>请输入0.1~10之间的数字,值为空代表不设置折扣(例如:设置9.5  会员价格=商品价格*(9.5/10))</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                         <label class="col-xs-12 col-sm-3 col-md-2 control-label">运费减免</label>
                                        <div class="col-sm-9 col-xs-12 form-inline">
                                            <div class='input-group col-sm-3'>

                                                <input type="text" name="level[freight_reduction]" class="form-control"
                                                       value="{{ $levelModel->freight_reduction }}"/>
                                                <span class='input-group-addon'>%</span>
                                            </div>
                                            <span class='help-block'>快递运费减免优惠%</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">权益说明</label>
                                        <div class="col-sm-9 col-xs-12">
                                            <textarea name="level[description]" style="height: 250px" class="form-control">{{ $levelModel->description }}</textarea>
                                            <span class='help-block'>权益说明在前端会员等级权益页面显示</span>
                                        </div>
                                    </div>

                                    <script type="text/javascript">
                                      require(['bootstrap'], function ($) {
                                        $(document).scroll(function () {
                                          var toptype = $("#edui1_toolbarbox").css('position');
                                          if (toptype == "fixed") {
                                            $("#edui1_toolbarbox").addClass('top_menu');
                                          }
                                          else {
                                            $("#edui1_toolbarbox").removeClass('top_menu');
                                          }
                                        });
                                      });
                                    </script>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                                        <div class="col-sm-9 col-xs-12">
                                            <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                                            <input type="hidden" name="token" value="token"/>
                                            <input type="button" name="back" onclick='history.back()' value="返回列表"
                                                   class="btn btn-default back"/>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </form>

        <div id="modal-goods" class="modal fade" tabindex="-1">
            <div class="modal-dialog" style='width: 920px;'>
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <h3>选择商品</h3></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" value="" id="search-kwd-goods"
                                       placeholder="请输入商品名称"/>
                                <span class='input-group-btn'><button type="button" class="btn btn-default"
                                                                      onclick="search_goods();">搜索</button></span>
                            </div>
                        </div>
                        <div id="module-menus-goods" style="padding-top:5px;"></div>
                    </div>
                    <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal"
                                                 aria-hidden="true">关闭</a></div>
                </div>
            </div>
        </div>
        <script language='javascript'>

            function search_goods() {
                if ($.trim($('#search-kwd-goods').val()) == '') {
                    Tip.focus('#search-kwd-goods', '请输入关键词');
                    return;
                }
                $("#module-goods").html("正在搜索....")
                $.get('{!! yzWebUrl('member.member-level.searchGoods') !!}', {
                        keyword: $.trim($('#search-kwd-goods').val())
                    }, function (dat) {
                        $('#module-menus-goods').html(dat);
                    }
                )
                ;
            }

            // function select_good(o) {
            //     $("#goodsid").val(o.id);
            //     $("#goods").val("[" + o.id + "]" + o.title);
            //     $("#modal-goods .close").click();
            // }

            function select_good(o) {
                // var html = "<input type='hidden' class='form-control' name='level[become_goods_id]["+ o.id+"]' value='' />"
                var html = '<div class="multi-item" openid="' + o.id + '">';
                html += '<img class="img-responsive img-thumbnail" src="' + o.thumb + '" onerror="this.src=\'{{static_url('resource/images/nopic.jpg')}}\'; this.title=\'图片未找到.\'">';
                html += '<div class="img-nickname" style="overflow: hidden">' + o.title + '</div>';
                html += '<input type="hidden" value="' + o.title + '" name="level[goods_id][' + o.id + '][title]">';
                html += '<input type="hidden" value="' + o.thumb + '" name="level[goods_id][' + o.id + '][thumb]">';
                html += '<input type="hidden" value="' + o.id + '" name="level[goods_id][' + o.id + '][goods_id]">';
                html += '<input type="hidden" value="' + o.id + '" name="level[goods_id][' + o.id + ']">';
                html += '<em onclick="remove_member(this)"  class="close">×</em>';
                html += '</div>';

                // console.log(html);
                // $("#goods_id").val(o.id);
                // var data = $("#goods").val();
                // $("#goods").val(data+ o.title);
                $("#goods_id").append(html);
                refresh_members();
            }

            function remove_member(obj) {
                console.log('remove---members---');
                // console.log('obj_html: '+ JSON.stringify($(obj).parent().html()));

                // var arr = new Array();
                // for (var i in obj) {
                //     console.log('i+'+i);
                //     arr.push(obj[i]); //属性
                //     //arr.push(obj[i]); //值
                // }
                // console.log(arr);
                
                $(obj).parent().remove();
                
                // console.log('obj_parent: '+ $(obj).parent());
                
                refresh_members();
            }
            
            function refresh_members() {
                console.log('reffresh---members---');
                
                var nickname = "";
                
                $('.multi-item').each(function () {
                    
                    // console.log('img-nickname: '+$(this).find('.img-nickname').html());
                    
                    nickname += " " + $(this).find('.img-nickname').html() + "; ";
                    
                    // console.log('nickname: '+nickname);

                });
                $('#goods').val(nickname);
            }    
        </script>
    </div>


@endsection