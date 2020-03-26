@extends('layouts.base')
@section('title', '角色管理')
@section('content')
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">角色管理</a></li>
    </ul>
</div>

        <form action="" method="post" class='form form-horizontal'>
            <div class="panel panel-info">

                <div class="panel-body">
                    <form action=" " method="get" class="form-horizontal" role="form">
                        <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="yun_shop"/>
                        <input type="hidden" name="do" value="QDaf"/>
                        <input type="hidden" name="route" value="user.role.index"/>
                        <div class="form-group col-xs-12 col-sm-6 col-lg-6">
                            <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>-->
                            <div class="">
                                <input class="form-control" name="search[keyword]" id="" type="text" value="{{ $search['keyword'] or '' }}" placeholder="可搜索角色名称">
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-5 col-lg-5 ">
                            <!--<label class="">状态</label>-->
                            <div class="c">
                                <select name="search[status]" class='form-control'>
                                    <option value="" selected>状态不限</option>
                                    <option value="2" @if($search['status'] == \app\common\models\user\YzRole::ROLE_ENABLE) selected @endif>启用</option>
                                    <option value="1" @if($search['status'] == \app\common\models\user\YzRole::ROLE_DISABLE) selected @endif>禁用</option>
                                </select>  </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-6 col-md-5 col-lg-1 ">
                            <!--<label class="control-label">&nbsp;</label>-->
                            <div class="">
                                <button class="btn btn-block btn-success"><i class="fa fa-search"></i> 搜索</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class='panel panel-default'>
                <div class='panel-heading'>
                    角色设置
                </div>
                <div class='panel-body'>

                    <table class="table">
                        <thead>
                        <tr>
                            <th>角色名称</th>
                            <th>操作员数量</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($roleList->items() as $role)
                        <tr>
                            <td> {{ $role->name }}</td>
                            <td>{{$role->roleUser->count()}}</td>
                            <td>
                                @if($role['status'] == \app\common\models\user\YzRole::ROLE_ENABLE)
                                <span class='label label-success'>启用</span>
                                @else
                                <span class='label label-danger'>禁用</span>
                                @endif
                            </td>
                            <td>
                                <a class='btn btn-default' href="{{ yzWebUrl('user.role.update', array('id' => $role->id)) }}"><i class="fa fa-edit"></i></a>
                                <a class='btn btn-default'  href="{{ yzWebUrl('user.role.destory', array('id' => $role->id)) }}" onclick="return confirm('确认删除该角色吗？');return false;"><i class="fa fa-remove"></i></a>
                            </td>

                        </tr>
                        @endforeach


                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
                <div class='panel-footer'>
                    <a class='btn btn-info' href="{{ yzWebUrl('user.role.store') }}"><i class="fa fa-plus"></i> 添加新角色</a>
                </div>


@endsection