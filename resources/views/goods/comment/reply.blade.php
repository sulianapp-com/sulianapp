@extends('layouts.base')

@section('content')
@section('title', trans('商品评论回复'))
    <div class="w1200 m0a">

        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">评价管理</a></li>
            </ul>
        </div>

        <form id="dataform" action="{{yzWebUrl("goods.comment.reply")}}" method="post" class="form-horizontal form" onsubmit='return formcheck()'>

            <div class='panel panel-default'>
                <div class='panel-heading'>
                    回复评价
                </div>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">评价商品</label>

                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="goods" maxlength="30"
                                   value="@if(!empty($goods)) [{{$goods['id']}}]{{$goods['title']}} @endif" id="goods"
                                   class="form-control" readonly/>
                            <span id="goodsthumb" class='help-block' @if(empty($goods)) style="display:none" @endif><img
                                        style="width:100px;height:100px;border:1px solid #ccc;padding:1px"
                                        src="{!! tomedia($goods['thumb']) !!}"/></span>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">评价者</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="goods" maxlength="30" value="{{$comment['nick_name']}}"
                                   id="goods" class="form-control" readonly/>
                            <span id="goodsthumb" class='help-block'><img
                                        style="width:100px;height:100px;border:1px solid #ccc;padding:1px"
                                        src="{!! tomedia($comment['head_img_url']) !!}"/></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">评分等级</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static" style='color:#ff6600'>
                                @if($comment['level']>=1) <i class='fa fa-star'></i> @else <i
                                        class='fa fa-star-o'></i> @endif
                                @if($comment['level']>=2) <i class='fa fa-star'></i> @else <i
                                        class='fa fa-star-o'></i> @endif
                                @if($comment['level']>=3) <i class='fa fa-star'></i> @else <i
                                        class='fa fa-star-o'></i> @endif
                                @if($comment['level']>=4) <i class='fa fa-star'></i> @else <i
                                        class='fa fa-star-o'></i> @endif
                                @if($comment['level']>=5) <i class='fa fa-star'></i> @else <i
                                        class='fa fa-star-o'></i> @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>
                                评论内容</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="form-control-static">{{$comment['content']}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group multi-img-details">
                                    @foreach(unserialize($comment['images']) as $img)
                                        <div class="multi-item">
                                            <a href='{!! tomedia($img) !!}' target='_blank'>
                                                <img class="img-responsive img-thumbnail" src='{!! tomedia($img) !!}'
                                                     onerror="this.src='{{static_url('./resource/images/nopic.jpg')}}'; this.title='图片未找到.'">
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>


                    @include('goods.comment.tpl-reply',['replyData'=>$comment['has_many_reply']])
                    <div class="form-group"></div>
                    {{--回复记录--}}
                    @include('goods.comment.tpl-reply',['replyData'=>$comment['has_many_append']])


                    <div class="form-group"></div>

                    <div class="form-group" id="reply_seat">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">回复 <span id="nick_name">{{$comment['nick_name']}}</span></label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea name='reply[reply_content]' id="reply_content" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldMultiImage('reply[reply_images]','') !!}
                        </div>
                    </div>
                    <input type="hidden" name="id" value="{{$comment['id']}}"/>
                    <input type="hidden" name="reply[order_id]" value="{{$comment['order_id']}}"/>
                    <input type="hidden" name="reply[goods_id]" value="{{$comment['goods_id']}}"/>

                    <input type="hidden" name="reply[nick_name]" id="nick_name" value="管理员"/>
                    <input type="hidden" name="reply[reply_id]" id="reply_id" value="{{$comment['uid']}}"/>
                    <input type="hidden" name="reply[comment_id]" id="comment_id" value="{{$comment['id']}}"/>

                    <input type="hidden" name="reply[type]" id="type" value="2"/>

                    <div class="form-group"></div>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"/>
                            <input type="button" name="back" onclick='history.back()' style='margin-left:10px;' value="返回列表" class="btn
                            btn-default" />
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
    <script language='javascript'>
        function formcheck() {

            if ($.trim($('#reply_content').val()) == '') {
                alert('请填写回复内容!');
                $('#reply_content').focus();
                return false;
            }

            return true;
        }

        $('.reply').click(function () {
            $('#reply_id').val($(this).data('uid'));
            $('#nick_name').text($(this).data('nick_name'));
            $('#reply_content').focus();
            $('html,body').animate({scrollTop: $(document).height()}, 100);
            return false;
        });

    </script>
@endsection