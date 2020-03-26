@extends('layouts.base')

@section('content')

    <div class="w1200 m0a">
        <div class="rightlist">


            @include('layouts.tabs')
            <form action="" method="post">
                <div class="panel panel-default">
                    <div class="panel-body table-responsive">
                        <table class="table table-hover">
                            <thead class="navbar-inner">
                            <tr>
                                <th style="width:80px;">ID</th>
                                <th style='width:80px'>显示顺序</th>
                                <th>标题</th>
                                <th>连接</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($slide as $row)
                                <tr>
                                    <td>{{$row['id']}}</td>
                                    <td>
                                        <input type="text" class="form-control" name="displayorder[{{$row['id']}}]"
                                               value="{{$row['display_order']}}">
                                    </td>

                                    <td>{{$row['slide_name']}}</td>
                                    <td>{{$row['link']}}</td>
                                    <td>
                                        @if($row['enabled']==1)
                                            <span class='label label-success'>显示</span>
                                        @else
                                            <span class='label label-danger'>隐藏</span>
                                        @endif
                                    </td>
                                    <td style="text-align:left;">
                                        <a href="{{yzWebUrl("setting.slide.edit",['id'=>$row['id']])}}"
                                           class="btn btn-default btn-sm" title="{修改"><i class="fa fa-edit"></i></a>
                                        <a href="{{yzWebUrl("setting.slide.deleted",['id'=>$row['id']])}}" class="btn btn-default btn-sm" onclick="return confirm('确认删除此幻灯片?');"
                                           title="删除"><i class="fa fa-times"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan='6'>
                                    <a class='btn btn-primary' href="{{yzWebUrl("setting.slide.create")}}"><i
                                                class='fa fa-plus'></i> 添加幻灯片</a>
                                    {{--<input name="submit" type="submit" class="btn btn-default" value="提交排序">--}}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {!! $pager !!}
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection()


