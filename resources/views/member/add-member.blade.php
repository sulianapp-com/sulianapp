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
            <form action="{{yzWebUrl('member.member.add-member')}}" method='post' onsubmit="return isRemeber()"
                  class='form-horizontal'>
                <input type="hidden" name="op" value="add-member">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="member"/>
                <div class='panel panel-default'>
                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝</label>
                            <div class="col-sm-9 col-xs-12">
                                <img src='{{$img}}'
                                     style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                                {{$member['nickname']}}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">绑定手机</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="mobile" class="form-control" id="mobile"
                                       value=""/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">登录密码</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="password" name="password" class="form-control" id="password1"/>
                            </div>
                            <p id="p_ti"></p>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">确认密码</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="password" name="confirm_password" class="form-control" id="password2"/>
                            </div>
                        </div>

                    </div>

                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit"  name="submit" value="提交" class="btn btn-success"/>
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

        $("#tijiao").onclick(function(){
            alert(11);
            $.post('{!! yzWebUrl("member.member.add-member") !!}',{
                mobile:$("#mobile").val(),
                password:$("#password1").val(),
                confirm_password:$("#password2").val()
            },function(result){

            });
        });
            {{--var mobile = $("#mobile").val();--}}
            {{--var password = $("#password1").val();--}}
            {{--var confirm_password = $("#password2").val();--}}
            {{--$.post("{!! yzWebUrl('member.member.add-member') !!}", {--}}
            {{--mobile: mobile,--}}
            {{--password: password,--}}
            {{--confirm_password: confirm_password,--}}
            {{--}, function (json) {--}}
            {{--var json = $.parseJSON(json);--}}
            {{--if (json.status == 1) {--}}

            {{--}--}}
            {{--});--}}

        function isRemeber() {
            if ($("#mobile").val() == "") {
                alert("手机号码不能为空！");
                $("#mobile").focus();
                return false;
            }

            if (!$("#mobile").val().match(/^1\d{10}$/)) {
                alert("手机号码格式不正确！");
                $("#mobile").focus();
                return false;
            }
            if ($("#password1").val()=="" || $("#password2").val() == ""){
                alert('密码框不能为空');
                return false;
            }

            if ($("#password1").val() != $("#password2").val() ){
                alert("两次密码不正确");
                return false;
            }
            if ($("#password1").val().length < 6 || !$("#password1").val().match(/^[A-Za-z0-9@!#\$%\^&\*]+$/)){
                alert("密码格式不正确！");
                return false;
            }

            return true;
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
