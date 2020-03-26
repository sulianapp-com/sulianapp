<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>
        关联标签组</label>
    <div class="col-sm-9">
        <div class='input-group'>
            <input type="text" name="" maxlength="30"
                   value="" class="form-control" readonly/>
            <div class='input-group-btn'>
                <button class="btn btn-default" type="button"
                        onclick="popwin = $('#modal-module-menus-filter').modal();">选择标签组
                </button>
            </div>
        </div>
        <ul id="goods_label_gruop">
            @if (isset($label_group))
                @foreach ($label_group as $group)
                 <li id="label_gruop_{{$group->id}}" class="input-group form-group col-sm-2" style="float:left;margin: 10px 100px 30px 0;">
                    <input type="hidden" name="category[filter_ids][]" value="{{$group->id}}">
                    <span class="input-group-addon" style="border-left:1px solid #ccc;">{{$group->name}}</span>
                    <span class="input-group-addon" onclick="gruop_del(this);" style="background: white;cursor:pointer;">X</span>
                </li>
                @endforeach
            @endif
        </ul>
        <div id="modal-module-menus-filter" class="modal fade" tabindex="-1">
            <div class="modal-dialog" style='width: 920px;'>
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">
                            ×
                        </button>
                        <h3>选择标签组</h3></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" value=""
                                       id="search-kwd-filter" placeholder="请输入标签组名称进行查询筛选"/>
                                                    <span class='input-group-btn'>
                                                        <button type="button" class="btn btn-default"
                                                                onclick="search_filter_group();">搜索
                                                        </button></span>
                            </div>
                        </div>
                        <div id="module-menus-filter" style="padding-top:5px;">
                        </div>
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
        function search_filter_group() {
            if ($.trim($('#search-kwd-filter').val()) == '') {
                Tip.focus('#search-kwd-filter', '请输入关键词');
                return;
            }
            $("#module-menus-filter").html("正在搜索....");
            $.get('{!! yzWebUrl('filtering.filtering.get-search-label') !!}', {
                        keyword: $.trim($('#search-kwd-filter').val())
                    }, function (dat) {
                        $('#module-menus-filter').html(dat);
                    }
            )
            ;
        }
        //选择商品标签组
        function select_filter(obj) {

            // console.log($("#label_gruop_"+obj.id));
            if (!($("#label_gruop_"+obj.id).length > 0)) {            
                var str = '<li id="label_gruop_'+obj.id+'" class="input-group form-group col-sm-2" style="float:left;margin: 10px 100px 30px 0;"><input type="hidden" name="category[filter_ids][]" value="'+ obj.id +'"><span class="input-group-addon" style="border-left:1px solid #ccc;">'+ obj.name +'</span><span class="input-group-addon" onclick="gruop_del(this);" style="background: white;cursor:pointer;">X</span></li>';

                $('#goods_label_gruop').append(str);
            }

            $("#modal-module-menus-filter .close").click();
        }

        //删除商品标签组
        function gruop_del(obj) {
            $(obj).parent().remove();
        }
</script>