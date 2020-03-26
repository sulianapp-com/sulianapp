@foreach($replyData as $reply)
    <div style="">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
            <div class="col-sm-9 col-xs-12">
                <div class="form-control-static" style=" height: 20px;line-height: 20px;">
                    @if($reply['type'] == 3)
                        {{$reply['nick_name']}}-{{$reply['type_name']}}
                    @else
                        {{$reply['nick_name']}}-{{$reply['type_name']}}-{{$reply['reply_name']}}
                    @endif

                    <span>时间:{{$reply['created_at']}}</span>
                    @if(!empty($reply['uid']) && $reply['type'] != 3)
                        <input type="button" name="reply" data-uid="{{$reply['uid']}}" data-id="{{$reply['id']}}" data-nick_name="{{$reply['nick_name']}}"
                               value="回复"
                               class="btn btn-default reply"/>
                    @endif
                    <a class='btn btn-default'
                       href="{{yzWebUrl('goods.comment.deleted', ['id' => $reply['id']])}}"
                       onclick="return confirm('确认删除此评价吗？');return false;"><i
                                class="fa fa-remove"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="form-group" style="margin: 0;">

            <label class="col-xs-12 col-sm-3 col-md-2 control-label">

            </label>
            <div class="col-sm-9 col-xs-12">
                <div class="">内容: {{$reply['content']}}</div>
            </div>
        </div>

        <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-9 col-xs-12">
        <div class="input-group multi-img-details">
        @foreach(iunserializer($reply['images']) as $img)
        <div class="multi-item">
        <a href='{!! tomedia($img) !!}' target='_blank'>
        <img class="img-responsive img-thumbnail" src='{!! tomedia($img) !!}'
        onerror="this.src='./resource/images/nopic.jpg'; this.title='图片未找到.'">
        </a>
        </div>
        @endforeach
        </div>
        </div>
        </div>

    </div>



@endforeach