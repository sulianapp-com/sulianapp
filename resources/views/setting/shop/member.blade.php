@extends('layouts.base')

@section('content')


    <div class="w1200 m0a">
        <div class="rightlist">

        @include('layouts.tabs')

            <form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员默认头像</label>
                            <div class="col-sm-9 col-xs-12">
                                {!! app\common\helpers\ImageHelper::tplFormFieldImage('member[headimg]', $set['headimg'])!!}
                                <span class='help-block'>会员默认头像（会员自定义头像>微信头像>商城默认头像）</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">默认会员级别名称</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="member[level_name]" class="form-control"
                                       value="{{ empty($set['level_name'])?'普通会员':$set['level_name']}}"/>
                                <span class="help-block">会员默认等级名称，不填写默认“普通会员”</span>
                            </div>
                        </div>


                        {{--<div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级说明连接</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class="input-group ">
                                    <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接 (请以http://开头, 不填则不显示)" value="{{$set['level_url']}}" name="member[level_url]">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default nav-link" type="button" data-id="PAL-00010" >选择链接</button>
                                    </span>
                                </div>
                            </div>
                        </div>--}}

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级权益页面是否显示</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'><input type='radio' name='member[display_page]' value='0'
                                                                   @if ($set['display_page'] == 0) checked @endif />否</label>
                                <label class='radio-inline'><input type='radio' name='member[display_page]' value='1'
                                                                   @if ($set['display_page'] == 1) checked @endif/> 是</label>
                            <!-- <span class="help-block">后台会员等级权益页面是否显示设置为是， 前端会员中心等级按钮形式则可以点击进入</span> -->
                                <!-- <span class="help-block">ps：只有会员等级升级依据为购买指定商品，会员中心才会有显示</span> -->
                               {{-- <span class="help-block">ps：只有会员等级升级依据为购买指定商品，在会员中心点击会员等级才可以进入等级权益页面</span>--}}
                                <span class="help-block">ps：只有会员等级升级依据为购买指定商品，在商品详情页和会员中心点击会员等级才可以进入等级权益页面</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品详情会员折扣</label>
                            <div class="col-sm-9 col-xs-12">

                                <label class='radio-inline'><input type='radio' name='member[discount]' value='1'
                                                                   @if ($set['discount'] == 1 ||empty($set['discount'])) checked @endif />显示</label>
                                <label class='radio-inline'><input type='radio' name='member[discount]' value='2'
                                                                   @if ($set['discount'] == 2) checked @endif /> 隐藏</label>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品详情已添加数量</label>
                            <div class="col-sm-9 col-xs-12">

                                <label class='radio-inline'><input type='radio' name='member[added]' value='1'
                                                                   @if ($set['added'] == 1 ||empty($set['added'])) checked @endif />显示</label>
                                <label class='radio-inline'><input type='radio' name='member[added]' value='2'
                                                                   @if ($set['added'] == 2) checked @endif /> 隐藏</label>

                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级升级依据</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[level_type]" value="0"
                                           @if (empty($set['level_type'])) checked @endif/> 订单金额
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[level_type]" value="1"
                                           @if ($set['level_type'] == 1) checked @endif/> 订单数量
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[level_type]" value="2"
                                           @if ($set['level_type'] == 2) checked @endif/> 购买指定商品
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[level_type]" value="3"
                                           @if ($set['level_type'] == 3) checked @endif/> 团队业绩(自购+一级+二级)
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[level_after]" value="1"
                                           @if ($set['level_after']) checked @endif/>
                                    付款后
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[level_after]" value="0"
                                           @if (empty($set['level_after'])) checked @endif/>
                                    完成后
                                </label>
                                <span class="help-block">
                                    如果选择付款后，只要用户下单付款满足升级依据，即可升级；如果选择完成后，则表示需要订单完成状态才能升级
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级时间限制</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class='radio-inline'><input type='radio' name='member[term]' value='0'
                                                                   @if ($set['term'] == 0) checked @endif /> 关闭</label>
                                <label class='radio-inline'><input type='radio' name='member[term]' value='1'
                                                                   @if ($set['term'] == 1) checked @endif/> 开启</label>
                            </div>
                        </div>
                    <!--
                <div class="form-group"  >
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级到期时间</label>
                    <div class="col-sm-8">
                        <div class="input-group col-xs-12">
                            <input type="text" name="member[term_time]" class="form-control" value="{{ $set['term_time'] }}"  />
                            <div class="input-group-addon " style="padding: 4px 12px;">
                                <select name="member[term_unit]">
                                    <option value ="1" @if ($set['term_unit'] ==1) selected @endif>--天--</option>
                                    <option value ="2" @if ($set['term_unit'] ==2) selected @endif>--周--</option>
                                    <option value ="3" @if ($set['term_unit'] ==3) selected @endif>--月--</option>
                                    <option value ="4" @if ($set['term_unit'] ==4) selected @endif>--年--</option>
                                </select>
                            </div>
                        </div>
                        <span class='help-block'>会员等级到期时间</span>
                    </div>
                </div>
-->
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">强制绑定手机</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_bind_mobile]" value="0"
                                           @if (empty($set['is_bind_mobile'])) checked @endif/> 否
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_bind_mobile]" value="1"
                                           @if ($set['is_bind_mobile'] == 1) checked @endif/> 全局强制绑定
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_bind_mobile]" value="2"
                                           @if ($set['is_bind_mobile'] == 2) checked @endif/> 会员中心强制绑定
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_bind_mobile]" value="3"
                                           @if ($set['is_bind_mobile'] == 3) checked @endif/> 商品页面强制绑定
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_bind_mobile]" value="4"
                                           @if ($set['is_bind_mobile'] == 4) checked @endif/> 推广中心页面强制绑定
                                </label>
                                <span class="help-block">进入商城是否强制绑定手机号，指定页面才强制绑定手机</span>
                            </div>
                        </div>
                        <!--
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员中心显示推荐人</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_referrer]" value="0"
                                           @if (empty($set['is_referrer'])) checked @endif/> 否
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_referrer]" value="1"
                                           @if ($set['is_referrer'] == 1) checked @endif/> 是
                                </label>
                                <span class="help-block">会员中心显示推荐人</span>
                            </div>
                        </div>
                        -->
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员中心显示余额</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[show_balance]" value="0" @if (empty($set['show_balance'])) checked @endif/>显示
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[show_balance]" value="1" @if ($set['show_balance'] == 1) checked @endif/>不显示
                                </label>
                                <span class="help-block">会员中心是否显示会员余额值</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员中心显示{{$shop['credit1']?:'积分'}}</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[show_point]" value="0" @if (empty($set['show_point'])) checked @endif/>显示
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[show_point]" value="1" @if ($set['show_point'] == 1) checked @endif/>不显示
                                </label>
                                <span class="help-block">会员中心是否显示会员{{$shop['credit1']?:'积分'}}</span>
                            </div>
                        </div>
                        {{--<div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">绑定手机</label>
                            <div class="col-sm-9 col-xs-12">
                                <a class="btn btn-warning" href="{php echo $this->createWebUrl('member/query', array('op' => 'delbindmobile'))}" data-original-title="" title="">清除绑定记录</a>
                                <span class="help-block">公众号被封后可使用此功能清除手机号绑定记录，让会员重新绑定找回被封公众号会员信息</span>
                            </div>
                        </div>--}}

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">邀请码</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_invite]" value="0"
                                           @if (empty($set['is_invite'])) checked @endif/> 关闭
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_invite]" value="1"
                                           @if ($set['is_invite'] == 1) checked @endif/> 开启
                                </label>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">邀请码是否必填</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[required]" value="0"
                                           @if (empty($set['required'])) checked @endif/> 关闭
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[required]" value="1"
                                           @if ($set['required'] == 1) checked @endif/> 开启
                                </label>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">邀请页面</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[invite_page]" value="0"
                                           @if (empty($set['invite_page'])) checked @endif/> 关闭
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[invite_page]" value="1"
                                           @if ($set['invite_page'] == 1) checked @endif/> 开启
                                </label>
                                <span class="help-block">邀请页面与强制绑定手机页面不能同时启用</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">邀请页面总店强制修改</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_bind_invite]" value="0"
                                           @if (empty($set['is_bind_invite'])) checked @endif/> 关闭
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_bind_invite]" value="1"
                                           @if ($set['is_bind_invite'] == 1) checked @endif/> 开启
                                </label>
                                <span class="help-block">默认是关闭</span>
                            </div>
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品邀请页面</label>--}}
                            {{--<div class="col-sm-9 col-xs-12">--}}
                                {{--<label class="radio radio-inline">--}}
                                    {{--<input type="radio" name="member[goods_invite_page]" value="0"--}}
                                           {{--@if (empty($set['goods_invite_page'])) checked @endif/> 关闭--}}
                                {{--</label>--}}
                                {{--<label class="radio radio-inline">--}}
                                    {{--<input type="radio" name="member[goods_invite_page]" value="1"--}}
                                           {{--@if ($set['goods_invite_page'] == 1) checked @endif/> 开启--}}
                                {{--</label>--}}
                                {{--<span class="help-block"></span>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义字段</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_custom]" value="0"
                                           @if (empty($set['is_custom'])) checked @endif/> 关闭
                                </label>
                                <label class="radio radio-inline">
                                    <input type="radio" name="member[is_custom]" value="1"
                                           @if ($set['is_custom'] == 1) checked @endif/> 开启
                                </label>
                                <span class="help-block"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义字段显示名</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="member[custom_title]" class="form-control"
                                       value="{{$set['custom_title']}}"/>
                                <span class="help-block"></span>
                            </div>
                        </div>


                        @if($is_diyform)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">自定义表单</label>
                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                                    <select class="form-control tpl-category-parent" id="level" name="member[form_id]">
                                        <option value="0">选择表单</option>
                                        @foreach($diyForm as $form)
                                            <option value="{{$form->id}}"
                                                    @if($set['form_id']==$form->id)
                                                    selected
                                                    @endif
                                            >[ID:{{$form->id}}]{{$form->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">注册状态</label>
                            <div class="col-sm-9 col-xs-12" >
                                <label class="radio-inline">
                                    <input type="radio" name="member[get_register]" value="1" @if($set['get_register'] == 1)checked="true" @endif onclick="$('#register').show()" />关闭
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="member[get_register]" value="0" @if($set['get_register'] == 0)checked="true" @endif onclick="$('#register').hide()"/> 开启
                                </label>
                            </div>
                        </div>

                        <div class="form-group desc" id="register" @if ($set['get_register'] == 0) style="display:none" @endif>
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">关闭描述</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="member[Close_describe]" class="form-control" value="{{ $set['Close_describe']}}" />
                                <span class='help-block'>关闭描述</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">手机验证码登录</label>
                            <div class="col-sm-9 col-xs-12" >
                                <label class="radio-inline">
                                    <input type="radio" name="member[mobile_login_code]" value="1" @if($set['mobile_login_code'] == 1)checked="true" @endif/> 开启
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="member[mobile_login_code]" value="0" @if($set['mobile_login_code'] == 0)checked="true" @endif/> 关闭
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信端登录方式</label>
                            <div class="col-sm-9 col-xs-12" >
                                <label class="radio-inline">
                                    <input type="radio" name="member[wechat_login_mode]" value="1" @if($set['wechat_login_mode'] == 1)checked="true" @endif/> 手机号码
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="member[wechat_login_mode]" value="0" @if($set['wechat_login_mode'] == 0)checked="true" @endif/> 自动授权登录
                                </label>
                            </div>
                        </div>

                        <div class="form-group"></div>
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
            var numerictype = /^(0|[1-9]\d*)$/; //非负整数验证
            var thumb = /\.(gif|jpg|jpeg|png|GIF|JPG|PNG)$/;


            if ($(':input[name="member[headimg]"]').val() != '') {
                if (!thumb.test($(':input[name="member[headimg]"]').val())) {
                    Tip.focus(':input[name="member[headimg]"]', '图片类型必须是.gif,jpeg,jpg,png中的一种.');
                    return false;
                }
            }
            if ($(':input[name="member[is_bind_mobile]"]:checked').val() != 0 && $(':input[name="member[invite_page]"]:checked').val() != 0) {
                if (!thumb.test($(':input[name="member[is_bind_mobile]"]').val())) {
                    Tip.focus(':input[name="member[is_bind_mobile]"]', '强制绑定手机不能跟邀请页面同时开启');
                    alert('强制绑定手机不能跟邀请页面同时开启');
                    return false;
                }
            }

            /*
             if ($(':input[name="member[term_time]"]').val() != '') {
             if (!numerictype.test($(':input[name="member[term_time]"]').val())) {
             Tip.focus(':input[name="member[term_time]"]', '会员等级到期时间,只能为非负整数.');
             return false;
             }
             }
             */
            return true;

        }
    </script>
    @include('public.admin.mylink')
@endsection('content')
