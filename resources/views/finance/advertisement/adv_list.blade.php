@extends('layouts.base')

@section('content')
@section('title', '商城收益')
<div class="right-titpos">
    <ul class="add-snav">
        <li class="active"><a href="#">收益广告</a></li>
    </ul>
</div>

<div class='panel panel-default'>
    <form action="" method="get" class="form-horizontal" id="form1">
        <input type="hidden" name="c" value="site"/>
        <input type="hidden" name="a" value="entry"/>
        <input type="hidden" name="m" value="yun_shop"/>
        <input type="hidden" name="do" value="subs" id="form_do"/>
        <input type="hidden" name="route" value="finance.advertisement.index" id="route" />
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="form-group col-xs-12 col-sm-2">
                    <input class="form-control" name="search[name]" type="text"
                           value="{{$search['name']}}" placeholder="广告标题">
                </div>
                <div class="form-group  col-xs-12 col-sm-5 col-lg-4">
                    <div class="">
                        {{--<button type="submit" name="export" value="1" id="export" class="btn btn-info">导出--}}
                            {{--Excel--}}
                        {{--</button>--}}
                        <button class="btn btn-success ">
                            <i class="fa fa-search"></i>
                            搜索
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<div class='panel panel-default'>
    <div class='panel-heading'>
        广告列表
        {{--总数：{{$list->total()}}个--}}
    </div>
    <div class=''>
        <table class="table table-hover" style="overflow:visible;">
            <thead>
            <tr>
                <th style='width:4%;text-align: center;'>ID</th>
                <th style='width:10%;text-align: center;'>标题</th>
                <th style='width:10%;text-align: center;'>状态</th>
                <th style='width:10%;'>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $row)
                <tr>
                    <td style="text-align: center;">{{$row->id}}</td>
                    <td style="text-align: center;">{{$row->name}}</td>
                    <td style="text-align: center;">
                        <button type="button" onclick="setStatus(this, {{$row->id}})" @if($row->status==1) class="btn btn-info" @else class="btn btn-warning" @endif>
                            @if ($row->status == 1)
                                显示
                            @else
                                不显示
                            @endif

                        </button>
                    </td>
                    <td style="overflow:visible;">

                        <a href="{{yzWebUrl('finance.advertisement.edit', array('id' => $row['id']))}}"
                           class="btn btn-sm btn-default" title="编辑"><i class="fa fa-edit"></i></a>

                        <a href="{{yzWebUrl('finance.advertisement.del', array('id' => $row['id']))}}"
                           onclick="return confirm('确认删除此广告');
                                                       return false;" class="btn btn-default  btn-sm" title="删除"><i
                                    class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {!! $pager !!}
    </div>
    <div style="margin-left:13px;margin-top:8px">
        <a class='btn btn-success' href="{{yzWebUrl('finance.advertisement.add')}}"><i
                        class='fa fa-plus'></i>添加广告 </a>
    </div>
</div>

<div style="width:100%;height:150px;"></div>
<script type="text/javascript">
    function setStatus(obj, id) {
        $(obj).html($(obj).html() + "...");
        var obj_v = $(obj);
        $.post("{!! yzWebUrl('finance.advertisement.setStatus') !!}", {id: id}
            , function (d) {
            console.log(d);
                if (d.result == '1') {

                    if (d.status == '1') {
                        obj_v.html('显示');
                        obj_v.toggleClass("btn-info btn-warning");

                    } else {
                        obj_v.html('不显示');
                        obj_v.toggleClass("btn-info btn-warning");
                    }

                } else {
                    alert('状态快速切换出错');
                }
            }
            , "json"
        );
    }
</script>
@endsection