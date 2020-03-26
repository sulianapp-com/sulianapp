@extends('layouts.base')
@section('title', '会员详情')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('member.member.index')}}">会员管理</a></li>
                    <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;会员详情</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="{{yzWebUrl('member.member.update', ['id'=> $member['uid']])}}" method='post'
                  class='form-horizontal'>
                <input type="hidden" name="id" value="{{$member['uid']}}">
                <input type="hidden" name="op" value="detail">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="member"/>
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝</label>
                            <div class="col-sm-9 col-xs-12">
                                <img src='{{$member['avatar']}}'
                                     style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                                {{$member['nickname']}}
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级</label>
                            <div class="col-sm-9 col-xs-12">
                                <select name='data[level_id]' class='form-control'>
                                    <option value="0" @if($member['yz_member']['level_id']==$level['id'])
                                    selected
                                            @endif;
                                    >
                                        {{$set['level_name']}}
                                    </option>
                                    @foreach ($levels as $level)
                                        <option value='{{$level['id']}}'
                                                @if($member['yz_member']['level_id']==$level['id'])
                                                selected
                                                @endif>{{$level['level_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($set['level_type'] == 2)
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员等级期限</label>
                                <div class="col-sm-6 col-xs-6">
                                    <div class='input-group'>
                                        <input type='text' name='data[validity]' class="form-control"
                                               value="{{$member['yz_member']['validity']}}"/>
                                        <div class='input-group-addon' style="width: auto;">天</div>
                                    </div>
                                </div>
                            </div>


                        @endif
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员分组</label>
                            <div class="col-sm-9 col-xs-12">
                                <select name='data[group_id]' class='form-control'>
                                    <option value='0' selected>无分组</option>
                                    @foreach($groups as $group)
                                        <option value='{{$group['id']}}'
                                                @if($member['yz_member']['group_id'] == $group['id']) selected @endif >{{ $group['group_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="data[realname]" class="form-control"
                                       value="{{$member['realname']}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">绑定手机</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{$member['mobile']}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">提现手机</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{$member['yz_member']['withdraw_mobile']}}</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">微信号</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="data[wechat]" class="form-control"
                                       value="{{$member['yz_member']['wechat']}}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝姓名</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="data[alipayname]" class="form-control"
                                       value="{{$member['yz_member']['alipayname']}}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝账号</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="data[alipay]" class="form-control"
                                       value="{{$member['yz_member']['alipay']}}"/>
                            </div>
                        </div>

                        @if (!empty($myform))
                            @foreach ($myform as $item)
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$item->name}}</label>
                                    <div class="col-sm-9 col-xs-12">
                                        <input type="text" name="myform[{{$item->pinyin}}]" class="form-control"
                                               value="{{$item->value}}"/>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">积分</label>
                            <div class="col-sm-3">
                                <div class='input-group'>
                                    <div class=' input-group-addon'>{{$member['credit1']}}</div>
                                    <div class='input-group-btn'>
                                        <a class='btn btn-success' href="{{yzWebUrl('point.recharge.index',['id'=>$member['uid']])}}">充值</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">余额</label>
                            <div class="col-sm-3">
                                <div class='input-group'>
                                    <div class=' input-group-addon'>{{$member['credit2']}}</div>
                                    <div class='input-group-btn'>
                                        <a class='btn btn-success' href="{{yzWebUrl('balance.recharge.index', ['member_id'=>$member['uid']])}}">充值</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">成交订单数</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    @if($member['has_one_order']['total'])
                                        {{$member['has_one_order']['total']}}
                                    @else
                                        0
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">成交金额</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    @if($member['has_one_order']['sum'])
                                        {{$member['has_one_order']['sum']}}
                                    @else
                                        0
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">注册时间</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>{{date('Y-m-d H:i:s', $member['createtime'])}}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">关注状态</label>
                            <div class="col-sm-9 col-xs-12">
                                <div class='form-control-static'>
                                    @if(!$member['has_one_fans']['followed'])
                                        <label class='label label-default'>未关注</label>
                                    @else
                                        <label class='label label-success'>已关注</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">推广员</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio" name="data[agent]" value="1"
                                                                   @if($member['agent']==1)
                                                                   checked
                                            @endif>是</label>
                                <label class="radio-inline"><input type="radio" name="data[agent]" value="0"
                                                                   @if($member['agent']==0)
                                                                   checked
                                            @endif>否</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员上线</label>
                            <div class="col-sm-5">
                                <div class='input-group'>
                                    <input type="hidden" id="parent_id" name="data[parent_id]" value="{{$member['yz_member']['parent_id']}}">
                                    <div class=' input-group-addon'  style="border-left: 1px solid #cccccc" id="parent_info">[{{$member['yz_member']['parent_id']}}]{{$parent_name}}</div>
                                    <div class='input-group-btn'><a class='btn btn-success'
                                                                    href="javascript:;" id="change_relation">修改</a>
                                    </div>
                                    <span class="help-block">&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" id="members_record">修改记录</a></span>
                                </div>
                                <span class="help-block">手动修改关系链可能会造成会员关系链异常，从而会导致分红、分销问题，请谨慎修改</span>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">会员邀请码</label>
                            <div class="col-sm-6 col-xs-6">
                                <div class='input-group'>
                                    <input type='text' name='data[invite_code]' class="form-control"
                                           value="{{$member['yz_member']['invite_code']}}"/>
                                </div>
                                <div><span>会员邀请码须8个字符</span></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">黑名单</label>
                            <div class="col-sm-9 col-xs-12">
                                <label class="radio-inline"><input type="radio" name="data[is_black]" value="1"
                                                                   @if($member['yz_member']['is_black']==1)
                                                                   checked
                                            @endif>是</label>
                                <label class="radio-inline"><input type="radio" name="data[is_black]" value="0"
                                                                   @if($member['yz_member']['is_black']==0)
                                                                   checked
                                            @endif>否</label>
                                <span class="help-block">设置黑名单后，此会员无法访问商城</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">备注</label>
                            <div class="col-sm-9 col-xs-12">
                                <textarea name="data[content]"
                                          class='form-control'>{{$member['yz_member']['content']}}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">{{$set['custom_title']}}</label>
                        <div class="col-sm-9 col-xs-12">
                            <input type="text" name="data[custom_value]" class="form-control"
                                   value="{{$member['yz_member']['custom_value']}}"/>
                        </div>
                    </div>


                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="提交" class="btn btn-success"/>
                                <input type="hidden" name="token" value="{{$var['token']}}"/>
                                <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)"
                                       value="返回" style='margin-left:10px;'/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-9">
            <div id="modal-module-menus-members" class="modal fade" tabindex="-1">
                <div class="modal-dialog" style='width: 920px;'>
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal"
                                    class="close" type="button">
                                ×
                            </button>
                            <h3>选择会员</h3></div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           name="keyword" value=""
                                           id="search-kwd-members"
                                           placeholder="请输入会员ID"/>
                                    <span class='input-group-btn'>
                                                            <button type="button" class="btn btn-default"
                                                                    onclick="search_members();">搜索
                                                            </button></span>
                                </div>
                            </div>
                            <div id="module-menus-members"
                                 style="padding-top:5px;"></div>
                        </div>
                        <div class="modal-footer"><a href="#"
                                                     class="btn btn-default"
                                                     data-dismiss="modal"
                                                     aria-hidden="true">关闭</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-9">
            <div id="modal-module-members-record" class="modal fade" tabindex="-1">
                <div class="modal-dialog" style='width: 920px;'>
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal"
                                    class="close" type="button">
                                ×
                            </button>
                            <h3>修改记录</h3></div>
                        <div class="modal-body">
                            <div id="module-members-record"
                                 style="padding-top:5px;"></div>
                        </div>
                        <div class="modal-footer"><a href="#"
                                                     class="btn btn-default"
                                                     data-dismiss="modal"
                                                     aria-hidden="true">关闭</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $('#change_relation').click(function () {
                $('#modal-module-menus-members').modal();
            });

            $('#members_record').click(function() {
                $('#modal-module-members-record').modal();

                $.get('{!! yzWebUrl('member.member.member_record') !!}', {
                    member: '{{$member['yz_member']['member_id']}}'
                    }, function (dat) {
                        $('#module-members-record').html(dat);
                    }
                );
            });
        });

        function search_members() {
            if ($.trim($('#search-kwd-members').val()) == '') {
                Tip.focus('#search-kwd-members', '请输入关键词');
                return;
            }
            $("#module-menus-members").html("正在搜索....");
            $.get('{!! yzWebUrl('member.member.search_member') !!}', {
                    parent: $.trim($('#search-kwd-members').val()),
                }, function (dat) {
                    if (dat != '') {
                        $('#module-menus-members').html(dat);
                    } else {
                        $("#modal-module-menus-members .close").click();
                    }
                }
            );
        }

        function select_member(o) {
            $.get('{!! yzWebUrl('member.member.change_relation') !!}', {
                    parent: $.trim(o.uid),
                    member: '{{$member['yz_member']['member_id']}}'
                }, function (dat) {
                    if (1 == dat.status) {
                        $("#parent_info").html("[" + o.uid + "]" + o.nickname);
                        $('#parent_id').val(o.uid);
                    }

                    $("#modal-module-menus-members .close").click();
                }
            );
        }
    </script>
@endsection
