@extends('layouts.base')
@section('title', '操作员管理')
@section('content')

<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">操作员</a></li>
    </ul>
</div>
<form action="" method="get" class='form form-horizontal'>
    <div class="panel panel-info">
        <!--<div class="panel-heading">筛选</div>-->
        <div class="panel-body">
            <form action="./index.php" method="get" class="form-horizontal" role="form">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="QDaf"/>
                <input type="hidden" name="route" value="user.user.index"/>
                <div class="form-group col-xs-12 col-sm-8 col-lg-5">
                   <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>-->
                    <div class="">
                        <input class="form-control" name="search[keyword]" id="" type="text" value="{{ $search['keyword'] }}" placeholder="可搜索操作名帐号/姓名/手机号">
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-8 col-lg-3">
                    <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">角色</label>-->
                    <div class="">
                        <select name="search[role_id]" class='form-control'>
                            <option value="" selected >不搜索角色</option>
                            @foreach($roleList as $list)
                            <option value="{{ $list['id'] }}" @if($search['role_id'] == $list['id']) selected @endif>{{ $list['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="form-group col-xs-12 col-sm-8 col-lg-3">
                   <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>-->
                    <div class="">
                        <select name="search[status]" class='form-control'>
                            <option value="" selected >不搜索状态</option>
                            <option value="2" @if($search['status'] == 2) selected @endif>启用</option>
                            <option value="1" @if($search['status'] == 1) selected @endif>禁用</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-xs-12 col-sm-8 col-lg-1">
                   <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label"> </label>-->
                    <div class="">
                        <button class="btn btn-block btn-success"><i class="fa fa-search"></i> 搜索</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class='panel panel-default'>
        <div class='panel-heading'>
            操作员管理
        </div>
        <div class='panel-body'>

            <table class="table">
                <thead>
                <tr>
                    <th>操作员账号</th>
                    <th>角色</th>
                    <th>姓名</th>
                    <th>手机</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($userList->items() as $key => $user)
                    <tr>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->userRole->role->name or "无" }}</td>
                        <td>{{ $user->userProfile->realname or '' }}</td>
                        <td>{{ $user->userProfile->mobile or '' }}</td>
                        <td>
                            @if($user->status == 2)
                                <span class='label label-success'>启用</span>
                            @elseif($user->status == 1)
                                <span class='label label-danger'>禁用</span>
                            @endif
                        </td>
                        <td>
                            <a class='btn btn-default' href="{{ yzWebUrl('user.user.update', array('id' => $user->uid)) }}">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a class='btn btn-default'  href="{{ yzWebUrl('user.user.destroy',array('id' => $user->uid)) }}" onclick="return confirm('确认删除此操作员吗？'); return false;">
                                <i class="fa fa-remove"></i>
                            </a>

                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>

            {!! $pager !!}

        </div>
        <div class='panel-footer'>
            <a class='btn btn-info' href="{{ yzWebUrl('user.user.store') }}"><i class="fa fa-plus"></i> 添加新操作员</a>
        </div>
    </div>
</form>



    <script language='javascript'>

        function search_roles() {
            $("#module-menus1").html("正在搜索....")
            $.get('{php echo $this->createPluginWebUrl('perm/role',array('op'=>'query'));}', {
                keyword: $.trim($('#search-kwd1').val())
            }, function(dat){
                $('#module-menus1').html(dat);
            });
        }
        function select_role(o) {
            $("#roleid").val(o.id);
            $("#role").val( o.rolename );
            var perms = o.perms.split(',');
            $(':checkbox')
            $(':checkbox').removeAttr('disabled').removeAttr('checked').each(function(){

                var _this = $(this);
                var perm = '';
                if( _this.data('group') ){
                    perm+=_this.data('group');
                }
                if( _this.data('child') ){
                    perm+="." +_this.data('child');
                }
                if( _this.data('op') ){
                    perm+="." +_this.data('op');
                }
                if( $.arrayIndexOf(perms,perm)!=-1){
                    $(this).attr('disabled',true).get(0).checked =true;
                }

            });
            $(".close").click();
        }
    </script>


@endsection