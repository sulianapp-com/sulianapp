<style type='text/css'>
    .multi-item {
        height: 110px;
    }

    .img-thumbnail {
        width: 100px;
        height: 100px
    }

    .img-nickname {
        position: absolute;
        bottom: 0px;
        line-height: 25px;
        height: 25px;
        color: #fff;
        text-align: center;
        width: 90px;
        bottom: 55px;
        background: rgba(0, 0, 0, 0.8);
        left: 5px;
    }

    .multi-img-details {
        padding: 5px;
    }
</style>
@if(YunShop::notice()->getNotSend('withdraw.income_withdraw_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现申请通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='withdraw[notice][income_withdraw]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw'])) value="{{$set['income_withdraw']}}"
                            selected @else value="" @endif>
                        默认消息模版
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['income_withdraw'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-xs-6">
                <input class="mui-switch mui-switch-animbg" id="income_withdraw" type="checkbox"
                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw']))
                       checked
                       @endif
                       onclick="message_default(this.id)"/>
            </div>
        </div>
    </div>
@endif
@if(YunShop::notice()->getNotSend('withdraw.income_withdraw_check_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现审核通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='withdraw[notice][income_withdraw_check]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_check'])) value="{{$set['income_withdraw_check']}}"
                            selected @else value="" @endif>
                        默认消息模版
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['income_withdraw_check'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-xs-6">
                <input class="mui-switch mui-switch-animbg" id="income_withdraw_check" type="checkbox"
                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_check']))
                       checked
                       @endif
                       onclick="message_default(this.id)"/>
            </div>
        </div>
    </div>
@endif
@if(YunShop::notice()->getNotSend('withdraw.income_withdraw_pay_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现打款通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='withdraw[notice][income_withdraw_pay]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_pay'])) value="{{$set['income_withdraw_pay']}}"
                            selected @else value="" @endif>
                        默认消息模版
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['income_withdraw_pay'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-xs-6">
                <input class="mui-switch mui-switch-animbg" id="income_withdraw_pay" type="checkbox"
                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_pay']))
                       checked
                       @endif
                       onclick="message_default(this.id)"/>
            </div>
        </div>
    </div>
@endif
@if(YunShop::notice()->getNotSend('withdraw.income_withdraw_arrival_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现到账通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='withdraw[notice][income_withdraw_arrival]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_arrival'])) value="{{$set['income_withdraw_arrival']}}"
                            selected @else value="" @endif>
                        默认消息模版
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['income_withdraw_arrival'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-xs-6">
                <input class="mui-switch mui-switch-animbg" id="income_withdraw_arrival" type="checkbox"
                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_arrival']))
                       checked
                       @endif
                       onclick="message_default(this.id)"/>
            </div>
        </div>
    </div>
@endif
@if(YunShop::notice()->getNotSend('withdraw.income_withdraw_arrival_title'))
    <div class='panel-body'>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现失败管理员通知</label>
            <div class="col-sm-8 col-xs-12">
                <select name='withdraw[notice][income_withdraw_fail]' class='form-control diy-notice'>
                    <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_fail'])) value="{{$set['income_withdraw_fail']}}"
                            selected @else value="" @endif>
                        默认消息模版
                    </option>
                    @foreach ($temp_list as $item)
                        <option value="{{$item['id']}}"
                                @if($set['income_withdraw_fail'] == $item['id'])
                                selected
                                @endif>{{$item['title']}}</option>
                    @endforeach
                </select>
            </div>
          <!--   <div class="col-sm-2 col-xs-6">
                <input class="mui-switch mui-switch-animbg" id="income_withdraw_fail" type="checkbox"
                       @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['income_withdraw_fail']))
                       checked
                       @endif
                       onclick="message_default(this.id)"/>
            </div> -->
        </div>
    </div>
@endif
<div class="panel-body">
    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员提现管理员通知</label>
        <div class="col-sm-8 col-xs-12">
            <select name='withdraw[notice][member_withdraw]' class='form-control diy-notice'>
                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['member_withdraw'])) value="{{$set['member_withdraw']}}"
                        selected @else value=""
                        @endif
                >
                    默认消息模板
                </option>
                @foreach ($temp_list as $item)
                    <option value="{{$item['id']}}"
                            @if($set['member_withdraw'] == $item['id'])
                            selected
                            @endif>{{$item['title']}}</option>
                @endforeach
            </select>
            <div class="help-block">通知公众平台模板消息编号: OPENTM207574677</div>
        </div>
        <div class="col-sm-2 col-xs-6">
            <input class="mui-switch mui-switch-animbg" id="member_withdraw" type="checkbox"
                   @if(\app\common\models\notice\MessageTemp::getIsDefaultById($set['member_withdraw']))
                   checked
                   @endif
                   onclick="message_default(this.id)"/>
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
        <div class="col-sm-6 col-xs-12">
            <div class='input-group'>
                <input type="text" id='withdraw-user' name="withdraw_user" maxlength="30"
                       value="@foreach ($set['withdraw_user'] as $saler) {{ $saler['nickname'] }} @endforeach"
                       class="form-control" readonly/>
                <div class='input-group-btn'>
                    <button class="btn btn-default" type="button"
                            onclick="popwin = $('#modal-module-menus-w').modal();">选择通知人
                    </button>
                </div>
            </div>
            <div class="input-group multi-img-details" id='withdraw_user_container'>
                @foreach ($set['withdraw_user'] as $saler)
                    <div class="multi-item saler-item" openid='{{ $saler['openid'] }}'>
                        <img class="img-responsive img-thumbnail" src='{{ $saler['avatar'] }}'
                             onerror="this.src='{{static_url('resource/images/nopic.jpg')}}'; this.title='图片未找到.'">
                        <div class='img-nickname'>{{ $saler['nickname'] }}</div>
                        <input type="hidden" value="{{ $saler['openid'] }}"
                               name="withdraw[notice][withdraw_user][{{ $saler['uid'] }}][openid]">
                        <input type="hidden" value="{{ $saler['uid'] }}"
                               name="withdraw[notice][withdraw_user][{{ $saler['uid'] }}][uid]">
                        <input type="hidden" value="{{ $saler['nickname'] }}"
                               name="withdraw[notice][withdraw_user][{{ $saler['uid'] }}][nickname]">
                        <input type="hidden" value="{{ $saler['avatar'] }}"
                               name="withdraw[notice][withdraw_user][{{ $saler['uid'] }}][avatar]">
                        <em onclick="remove_member_w(this)" class="close">×</em>
                    </div>
                @endforeach
            </div>
            <span class='help-block'>会员收入提现商家通知，可以指定多人，如果不填写则不通知</span>
            <div id="modal-module-menus-w" class="modal fade" tabindex="-1">
                <div class="modal-dialog" style='width: 920px;'>
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal" class="close"
                                    type="button">×
                            </button>
                            <h3>选择通知人</h3></div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="keyword" value=""
                                           id="search-kwd-w" placeholder="请输入粉丝昵称/姓名/手机号"/>
                                    <span class='input-group-btn'><button type="button"
                                                                          class="btn btn-default"
                                                                          onclick="search_members_w();">
                                                                搜索
                                                            </button></span>
                                </div>
                            </div>
                            <div id="module-menus-w" style="padding-top:5px;"></div>
                        </div>
                        <div class="modal-footer"><a href="#" class="btn btn-default"
                                                     data-dismiss="modal" aria-hidden="true">关闭</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function message_default(name) {
        var id = "#" + name;
        var setting_name = "withdraw.notice";
        var select_name = "select[name='withdraw[notice][" + name + "]']"
        var url_open = "{!! yzWebUrl('setting.default-notice.index') !!}"
        var url_close = "{!! yzWebUrl('setting.default-notice.cancel') !!}"
        var postdata = {
            notice_name: name,
            setting_name: setting_name
        };
        if ($(id).is(':checked')) {
            //开
            $.post(url_open,postdata,function(data){
                if (data.result == 1) {
                    $(select_name).find("option:selected").val(data.id)
                    showPopover($(id),"开启成功")
                } else {
                    showPopover($(id),"开启失败，请检查微信模版")
                    $(id).attr("checked",false);
                }
            }, "json");
        } else {
            //关
            $.post(url_close,postdata,function(data){
                $(select_name).val('');
                showPopover($(id),"关闭成功")
            }, "json");
        }
    }
    function showPopover(target, msg) {
        target.attr("data-original-title", msg);
        $('[data-toggle="tooltip"]').tooltip();
        target.tooltip('show');
        target.focus();
        //2秒后消失提示框
        setTimeout(function () {
                target.attr("data-original-title", "");
                target.tooltip('hide');
            }, 2000
        );
    }
</script>
<script language='javascript'>
    function search_members_w() {
        if ($.trim($('#search-kwd-w').val()) == '') {
            Tip.focus('#search-kwd-w', '请输入关键词');
            return;
        }
        $("#module-menus-w").html("正在搜索....");
        $.get("{!! yzWebUrl('member.member.get-search-member') !!}", {
            keyword: $.trim($('#search-kwd-w').val())
        }, function (dat) {
            $('#module-menus-w').html(dat);
        });
    }
    function select_member(o) {
        if ($('.multi-item[openid="' + o.has_one_fans.openid + '"]').length > 0) {
            return;
        }
        var html = '<div class="multi-item" openid="' + o.has_one_fans.openid + '">';
        html += '<img class="img-responsive img-thumbnail" src="' + o.avatar + '" onerror="this.src=\'{{static_url('resource/images/nopic.jpg')}}\'; this.title=\'图片未找到.\'">';
        html += '<div class="img-nickname">' + o.nickname + '</div>';
        html += '<input type="hidden" value="' + o.has_one_fans.openid + '" name="withdraw[notice][withdraw_user][' + o.uid + '][openid]">';
        html += '<input type="hidden" value="' + o.nickname + '" name="withdraw[notice][withdraw_user][' + o.uid + '][nickname]">';
        html += '<input type="hidden" value="' + o.avatar + '" name="withdraw[notice][withdraw_user][' + o.uid + '][avatar]">';
        html += '<input type="hidden" value="' + o.uid + '" name="withdraw[notice][withdraw_user][' + o.uid + '][uid]">';
        html += '<em onclick="remove_member_w(this)"  class="close">×</em>';
        html += '</div>';
        $("#withdraw_user_container").append(html);
        refresh_members_w();
    }
    function remove_member_w(obj) {
        $(obj).parent().remove();
        refresh_members_w();
    }
    function refresh_members_w() {
        var nickname = "";
        $('.multi-item').each(function () {
            nickname += " " + $(this).find('.img-nickname').html() + "; ";
        });
        $('#withdraw-user').val(nickname);
    }
</script>

