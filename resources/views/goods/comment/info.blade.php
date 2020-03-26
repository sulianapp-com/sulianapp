@extends('layouts.base')

@section('content')
@section('title', trans('商品评论详情'))
    <link href="../addons/sz_yi/template/mobile/default/static/js/star-rating.css" media="all" rel="stylesheet"
          type="text/css"/>
    <script src="../addons/sz_yi/template/mobile/default/static/js/star-rating.js" type="text/javascript"></script>
    <div class="w1200 m0a">
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">添加评价</a></li>
            </ul>
        </div>
        <form id="dataform" action="" method="post" class="form-horizontal form" onsubmit='return formcheck()'>

            <input type="hidden" name="id" value="{{isset($id) ? $id : ''}}">
            <div class='panel panel-default'>
                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>
                            选择商品</label>
                        <div class="col-sm-9">
                            <input type='hidden' id='goodsid' name='comment[goods_id]' value="{{$comment->goods_id}}"/>
                            <div class='input-group'>
                                <input type="text" name="goods" maxlength="30"
                                       value="@if(!empty($goods)) [{{$goods['id']}}]{{$goods['title']}} @endif"
                                       id="goods" class="form-control" readonly/>
                                <div class='input-group-btn'>
                                    <button class="btn btn-default" type="button"
                                            onclick="popwin = $('#modal-module-menus-goods').modal();">选择商品
                                    </button>
                                    <button class="btn btn-danger" type="button"
                                            onclick="$('#goodsid').val('');$('#goods').val('');">清除选择
                                    </button>
                                </div>
                            </div>
                            <span id="goodsthumb" class='help-block'
                                  @if(empty($goods)) style="display:none" @endif ><img
                                        style="width:100px;height:100px;border:1px solid #ccc;padding:1px"
                                        src="@if(isset($goods['thumb'])) {{yz_tomedia($goods['thumb']) }} @endif"/></span>

                            <div id="modal-module-menus-goods" class="modal fade" tabindex="-1">
                                <div class="modal-dialog" style='width: 920px;'>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                                                ×
                                            </button>
                                            <h3>选择商品</h3></div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="keyword" value=""
                                                           id="search-kwd-goods" placeholder="请输入商品名称"/>
                                                    <span class='input-group-btn'>
                                                        <button type="button" class="btn btn-default"
                                                                onclick="search_goods();">搜索
                                                        </button></span>
                                                </div>
                                            </div>
                                            <div id="module-menus-goods" style="padding-top:5px;"></div>
                                        </div>
                                        <div class="modal-footer"><a href="#" class="btn btn-default"
                                                                     data-dismiss="modal" aria-hidden="true">关闭</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">用户头像</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('comment[head_img_url]',$comment->head_img_url) !!}
                            <span class='help-block'>用户头像，如果不选择，默认从粉丝表中随机读取</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">用户昵称</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type='text' class='form-control' name='comment[nick_name]'
                                   value='{{$comment->nick_name}}'/>
                            <span class='help-block'>用户昵称，如果不填写，默认从粉丝表中随机读取</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>
                            评分等级</label>
                        <div class="col-sm-9 col-xs-12">
                            <input value="{{intval($comment->level)}}" type="number" name='comment[level]'
                                   id="level" class="rating" min=0 max=5 step=1 data-size="xs">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>
                            首次评价</label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea name='comment[content]' id="content"
                                      class="form-control">{{$comment->content}}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldMultiImage('comment[images]',unserialize($comment->images)) !!}
                        </div>
                    </div>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-success "/>
                            <input type="button" name="back" onclick='history.back()' style='margin-left:10px;'
                                   value="返回列表" class="btn
                            btn-default"/>
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>
    <script language='javascript'>
        $(function () {
            $(".rating").rating({});
        });
        function formcheck() {

            if ($(':input[name=goods]').val() == '') {
                Tip.focus($(':input[name=goods]'), '请选择要评价的商品!');
                return false;
            }

            if ($('#level').val() == '0') {
                alert('请选择评价等级!');
                return false;
            }
            if ($.trim($('#content').val()) == '') {
                alert('请填写评价内容!');
                $('#content').focus();
                return false;
            }

            return true;
        }
        function search_goods() {
            if ($.trim($('#search-kwd-goods').val()) == '') {
                Tip.focus('#search-kwd-goods', '请输入关键词');
                return;
            }
            $("#module-menus-goods").html("正在搜索....");
            $.get('{!! yzWebUrl('goods.goods.get-search-goods') !!}', {
                keyword: $.trim($('#search-kwd-goods').val())
            }, function (dat) {
                $('#module-menus-goods').html(dat);
            }
        )
            ;
        }
        function select_good(o) {
            $("#goodsid").val(o.id);
            $("#goodsthumb").show();
            $("#goodsthumb").find('img').attr('src', o.thumb);
            $("#goods").val("[" + o.id + "]" + o.title);
            $("#modal-module-menus-goods .close").click();
        }
    </script>
@endsection