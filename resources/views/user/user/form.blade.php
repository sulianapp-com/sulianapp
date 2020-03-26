@extends('layouts.base')
@section('title', '操作员设置')
@section('content')

@if( $user->uid )
<form id="dataform" action="{{ yzWebUrl('user.user.update') }}" method="post" class="form-horizontal form" >
    <input type="hidden" name="id" value="{{ $user->uid }}" />
@else
    <form id="dataform" action="{{ yzWebUrl('user.user.store') }}" method="post" class="form-horizontal form" >
@endif
    <div class="right-titpos">
        <ul class="add-snav">
            <li class="active"><a href="#">操作员&nbsp;  <i class="fa fa-angle-double-right"></i> &nbsp;操作员设置</a></li>
        </ul>
    </div>
    <div class='panel panel-default'>


        <div class='panel-body'>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">角色</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <select name="widgets[role_id]" class='form-control' id='userRole'>
                        <option value=""  selected>点击选择角色</option>
                        @foreach($roleList as $role)
                        <option value="{{  $role['id'] }}" @if($role['id'] == $user->userRole->role_id) selected @endif >{{ $role['name'] }}</option>
                        @endforeach

                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 操作员用户名</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="user[username]" class="form-control" value="" @if($user->username)placeholder="{{ $user->username or '' }}" readonly AUTOCOMPLETE="off" @endif/>
                    <span class='help-block'>操作员用户名具有唯一性，且不支持修改</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>  操作员密码</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="password" name="user[password]" class="form-control" value="" @if($user->password)placeholder="重新输入密码将会修改密码"@endif />
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span> 姓名</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="widgets[profile][realname]" class="form-control" value="{{ $user->userProfile->realname or '' }}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>电话</label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="widgets[profile][mobile]" class="form-control" value="{{ $user->userProfile->mobile or '' }}" />
                    <span class='help-block'>此处可填可不填</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
                <div class="col-sm-9 col-xs-12">
                    <label class='radio-inline'>
                        <input type='radio' name='user[status]' value='2' @if($user->status == 2) checked @endif /> 启用
                    </label>
                    <label class='radio-inline'>
                        <input type='radio' name='user[status]' value='1' @if($user->status == 1 || $user->status == '') checked @endif /> 禁用
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <span class='form-control-static'>用户可以在此角色权限的基础上附加其他权限</span>
                </div>
            </div>



            @include('user.permission.permission')




            <div class="form-group"></div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <input type="submit" name="submit" value="提交" class="btn btn-success" />
                    <input type="button" name="back" onclick='history.back()' value="返回列表" class="btn btn-default back" />
                </div>
            </div>


        </div>
    </div>
    <div class="form-group col-sm-12">

    </div>
</form>
<script>
     $('#userRole').on('change', function(){
        var checkBoxs=$("input[type='checkbox']")
        //清空所有的选中项
        checkBoxs.each(function(){
            this.checked=false;
            this.disabled = false;
        })
        var id=this.value;
        $.ajax({
        type:'get',
        url:"{!!yzWebUrl('role.permission.index')!!}",
        data:{role_id:id},
        success:function(res){
            console.log(res)
            if(res.result==1){
              var arr=res.data;
            //   console.log(checkBoxs.length)
              checkBoxs.each(function(){
                  var valueRole=this.value;
                  if(arr.indexOf(valueRole)>-1){
                      this.checked=true;
                      this.disabled = true;
                  }
              })
            }
        }
    })
});
</script>

@endsection