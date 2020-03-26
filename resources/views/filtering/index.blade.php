@extends('layouts.base')
@section('title', '商品标签')
@section('content')

<div class="w1200 m0a">
    <form action="" method="post" class="form-horizontal" role="form" id="form1">
      <!--   <input type="hidden" name="c" value="site" />
        <input type="hidden" name="a" value="entry" />
        <input type="hidden" name="m" value="yun_shop" />
        <input type="hidden" name="do" value="plugin" />
        <input type="hidden" name="p" value="" />
        <input type="hidden" name="method" value="" />
        <input type="hidden" name="op" value="" /> -->

        <div class="panel panel-info">
            <div class="panel-heading">商品标签列表</div>
            <div class="panel-body">
                
                </div>
            </div>
        </div>
    </form>

    <div class="panel panel-default">
        {{--<div class="panel-heading">总数: {{$total}}</div>--}}
        <div class="panel-body">
            <table class="table table-hover table-responsive">
                <thead class="navbar-inner" >
                    <tr>
                        <th width="10%">ID</th>
                        <th width="16%">标签组名称</th>
                        <th width="10%">拥有标签个数</th>
                        <th width="10%">是否显示</th>
                        <th width="15%">创建时间</th>
                        <th width="22%">操作</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($list as $row)
                    <tr>
                        <td>{{$row['id']}}</td>
                        <td>
                            {{$row['name']}}
                        </td>
                        <td>
                            {{$row['filter_num']}}
                        </td>
                        <td>
                            @if($row['is_show']==0)
                                <label class='label label-warning'>显示</label>
                            @else
                                <label class="label label-default">不显示</label>
                            @endif
                        </td>
                        <td>{!! $row['created_at'] !!}</td>
                        <td style="position:relative">
                            <a class='btn btn-default btn-sm' href="{{yzWebUrl('filtering.filtering.filter-value', ['parent_id'=>$row['id']])}}" title='值'>标签</a>
                            <a class='btn btn-default btn-sm' href="{{yzWebUrl('filtering.filtering.edit', ['id' => $row['id']])}}" title="编辑" ><i class='fa fa-edit'></i></a>
                            <a class='btn btn-default btn-sm' href="{{yzWebUrl('filtering.filtering.del', ['id' => $row['id']])}}" title="删除" onclick="return confirm('确定要删除该标签组吗？');"><i class='fa fa-remove'></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $pager !!}
        </div>
        <div class='panel-footer'>
            <a class='btn btn-primary' href="{{yzWebUrl('filtering.filtering.create', ['parent_id' => 0])}}"><i class='fa fa-plus'></i> 添加新标签组</a>
        </div>
    </div>
</div>

@endsection('content')