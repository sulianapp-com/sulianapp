@extends('layouts.base')
@section('title', '会员关系基础设置')
@section('content')
    @include('layouts.tabs')
    <section class="content">

        <form id="setform" action="" method="post" class="form-horizontal form">
            <div class='panel panel-default'>

                <div class='panel-body'>
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">Banner</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! app\common\helpers\ImageHelper::tplFormFieldImage('base[banner]', $banner)!!}
                            <span class='help-block'>长方型图片</span>
                        </div>
                    </div>

                    <div class="form-group" style="display: none;">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">内容</label>
                        <div class="col-sm-9 col-xs-12">
                            {!! tpl_ueditor('base[content]', $content) !!}

                        </div>
                    </div>

                </div>

                <div class='panel-heading'>
                    {{trans('通知设置')}}
                </div>
                <div class='panel-body'>
                    {{--<div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">获得推广权限通知</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text"  name="base[generalize_title]" class="form-control" value="{{$base['generalize_title']}}" ></input>
                            标题: 默认'获得推广权限通知'
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea  name="base[generalize_msg]" class="form-control" >{{$base['generalize_msg']}}</textarea>
                            模板变量: [昵称] [时间]
                        </div>
                    </div>--}}

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">获得推广权限通知</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name='base[member_agent]' class='form-control diy-notice'>
                                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($base['member_agent'])) value="{{$base['member_agent']}}"
                                        selected @else value="" @endif>
                                    默认消息模板
                                </option>
                                @foreach ($temp_list as $item)
                                    <option value="{{$item['id']}}"
                                            @if($base['member_agent'] == $item['id'])
                                            selected
                                            @endif>{{$item['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input class="mui-switch mui-switch-animbg" id="member_agent" type="checkbox"
                               @if(\app\common\models\notice\MessageTemp::getIsDefaultById($base['member_agent']))
                               checked @endif
                               onclick="message_default(this.id)"/>
                    </div>

                    {{--<div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">新增下线通知</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text"  name="base[agent_title]" class="form-control" value="{{$base['agent_title']}}" ></input>
                            标题: 默认'新增下线通知'
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <textarea  name="base[agent_msg]" class="form-control" >{{$base['agent_msg']}}</textarea>
                            模板变量: [昵称] [时间] [下级昵称]
                        </div>
                    </div>--}}

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">新增下线通知</label>
                        <div class="col-sm-8 col-xs-12">
                            <select name='base[member_new_lower]' class='form-control diy-notice'>
                                <option @if(\app\common\models\notice\MessageTemp::getIsDefaultById($base['member_new_lower'])) value="{{$base['member_new_lower']}}"
                                        selected @else value="" @endif>
                                    默认消息模板
                                </option>
                                @foreach ($temp_list as $item)
                                    <option value="{{$item['id']}}"
                                            @if($base['member_new_lower'] == $item['id'])
                                            selected
                                            @endif>{{$item['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input class="mui-switch mui-switch-animbg" id="member_new_lower" type="checkbox"
                               @if(\app\common\models\notice\MessageTemp::getIsDefaultById($base['member_new_lower']))
                               checked @endif
                               onclick="message_default(this.id)"/>
                    </div>
                </div>

                <div class='panel-heading'>
                    {{trans('会员关系')}}
                </div>

                <div class='panel-body'>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员关系页面显示推荐人</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="radio radio-inline">
                                <input type="radio" name="base[is_referrer]" value="0"
                                       -                                       @if (empty($base['is_referrer'])) checked @endif/> 否
                            </label>
                            <label class="radio radio-inline">
                                <input type="radio" name="base[is_referrer]" value="1"
                                       -                                       @if ($base['is_referrer'] == 1) checked @endif/> 是
                            </label>
                            <span class="help-block">会员关系页面显示推荐人</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示关系等级</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="checkbox-inline">
                                <input type="checkbox"  name="base[relation_level][]" value="1" @if (array_search(1, $relation_level) === 0)
                                checked @endif />1级
                                <input type="text"  name="base[relation_level][name1]" value="{{ $relation_level['name1'] }}" placeholder="自定义名称"/>
                            </label>
                        </div>
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="checkbox-inline">
                                <input type="checkbox"  name="base[relation_level][]" value="2" @if (in_array(2, $relation_level))
                                checked @endif />2级
                                <input type="text"  name="base[relation_level][name2]" value="{{ $relation_level['name2'] }}" placeholder="自定义名称"/>
                            </label>
                        </div>
                        {{--<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>--}}
                        {{--<div class="col-sm-9 col-xs-12">--}}
                            {{--<label class="checkbox-inline">--}}
                                {{--<input type="checkbox"  name="base[relation_level][]"  value="3" @if (in_array(3, $relation_level)) checked @endif />3级--}}
                                {{--<input type="text"  name="base[relation_level][name3]" value="{{ $relation_level['name3'] }}" placeholder="自定义名称"/>--}}
                            {{--</label>--}}
                        {{--</div>--}}
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示按钮</label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="checkbox-inline">
                                <input type="checkbox"  name="base[relation_level][wechat]"  value="1" @if ($relation_level['wechat'] == 1) checked @endif />微信
                            </label>
                        </div>
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <label class="checkbox-inline">
                                <input type="checkbox"  name="base[relation_level][phone]"  value="1" @if ($relation_level['phone'] == 1) checked @endif />手机
                            </label>
                        </div>
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                        <div class="col-sm-9 col-xs-12">
                            <br>
                            <label>勾选后,页面将显示下级微信、手机号,如果个人资料中没有填写微信和绑定手机号,将不显示图标!</label>
                        </div>
                    </div>
                </div>

            <div class='panel-heading'>
                {{trans('关系链升级')}}
            </div>

            <div class='panel-body'>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">导入会员</label>
                    <div class="col-sm-9 col-xs-12">
                        <a href="{{yzWebUrl('member.member.exportRelation')}}"><input type="button" value="导入"></a>
                        <span class='help-block'>旧会员同步新关系链，如果不同步，除经销商团队业绩升级、统计团队业绩功能外，其他功能不影响使用。
同步功能暂时未做同步完成时间提示，点击同步后，1万会员同步预计5分钟，请根据会员数量耐心等待，不用关闭页面！</span>
                    </div>

                </div>

                <div class="form-group"></div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                    <div class="col-sm-9">
                        <input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1"
                               onclick='return formcheck()'/>
                    </div>
                </div>
            </div>
            </div>
        </form>

        <script>
            $('.diy-notice').select2();
        </script>
        <script>
            function message_default(name) {
                var id = "#" + name;
                var setting_name = "relation_base";
                var select_name = "select[name='base[" + name + "]']"
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
    </section>@endsection