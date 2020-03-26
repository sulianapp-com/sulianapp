<link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>

<div class="form-group notice">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">商家通知</label>
    <div class="col-sm-9 col-md-10">


        <input type='hidden' id='uid' name='widgets[notice][uid]' value="{{ $uid }}"/>
        <div class='input-group col-md-6'>
            <input type="text" name="saler" maxlength="30"
                   value="@if (!empty($saler)) {{ $saler['nickname'] }} / {{ $saler['realname'] }} / {{ $saler['mobile'] }} @endif"
                   id="saler" class="form-control" readonly/>
            <div class='input-group-btn'>
                <button class="btn btn-default" type="button"
                        onclick="popwin = $('#modal-module-menus-notice').modal();">选择通知人
                </button>
                <button class="btn btn-danger" style="height:34px" type="button"
                        onclick="$('#uid').val('');$('#saler').val('');$('#saleravatar').hide()">清除选择
                </button>
            </div>
        </div>
        <span id="saleravatar" class='help-block' @if (empty($saler)) style="display:none" @endif >
            <img style=""
                    src="@if (!empty($saler)) {{ $saler->avatar }} @endif"/></span>
        <span class="help-block">单品下单通知，可指定某个用户，通知商品下单备货通知,如果商品为同一商家，建议使用系统统一设置</span>

        <div id="modal-module-menus-notice" class="modal fade" tabindex="-1">
            <div class="modal-dialog" >
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                        <h3>选择通知人</h3></div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group">
                                <input type="text" class="form-control" name="keyword" value="" id="search-kwd-notice"
                                       placeholder="请输入粉丝昵称/姓名/手机号"/>
                                <span class='input-group-btn'><button type="button" class="btn btn-default"
                                                                      onclick="search_members();">搜索</button></span>
                            </div>
                        </div>
                        <div id="module-menus-notice" ></div>
                    </div>
                    <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal"
                                                 aria-hidden="true">关闭</a></div>
                </div>

            </div>
        </div>


    </div>
</div>

<div class="form-group">
    <label class="col-xs-12 col-sm-3 col-md-2 control-label">通知方式</label>
    <div class="col-sm-9 col-xs-12">


        <label class="checkbox-inline">
            <input type="checkbox" value="1" name='widgets[notice][type][]'
                   @if (in_array(1, $noticetype)) checked @endif /> 下单通知
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" value="2" name='widgets[notice][type][]'
                   @if (in_array(2, $noticetype)) checked @endif /> 付款通知
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" value="3" name='widgets[notice][type][]'
                   @if (in_array(3, $noticetype)) checked @endif /> 买家收货通知
        </label>
        <div class="help-block">通知商家方式</div>

    </div>
</div>

<script language='javascript'>

    function search_members() {
        if ($('#search-kwd-notice').val() == '') {
            Tip.focus('#search-kwd-notice', '请输入关键词');
            return;
        }
        $("#module-menus-notice").html("正在搜索....");
        $.get("{!! yzWebUrl('member.member.get-search-member') !!}", {
            keyword: $.trim($('#search-kwd-notice').val())
        }, function (dat) {
            $('#module-menus-notice').html(dat);
        });
    }
    function select_member(o) {
        $("#uid").val(o.uid);
        $("#saleravatar").show();
        $("#saleravatar").find('img').attr('src', o.avatar);
        $("#saler").val(o.nickname + "/" + o.realname + "/" + o.mobile);
        $("#modal-module-menus-notice .close").click();
    }

</script>
