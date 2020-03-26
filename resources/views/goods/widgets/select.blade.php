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
                                        src="@if(isset($goods['thumb'])) {{tomedia($goods['thumb']) }} @endif"/></span>

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

<script language='javascript'>
    $(function () {
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
    });
</script>