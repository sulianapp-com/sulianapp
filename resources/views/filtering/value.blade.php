@extends('layouts.base')

@section('content')
@section('title', trans('商品分类列表'))
    <div class="w1200 m0a">
        <script language="javascript" src="{{static_url('js/dist/nestable/jquery.nestable.js')}}"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">所属组 :</a></li>
                @if(!empty($parent))
                    <li style="color: red">{{$parent->name}}</li>
                    <li class="active back">
                        <a href="{{yzWebUrl('filtering.filtering.index')}}">
                            <button type="button" class="btn btn-block btn-info">返回组列表</button>

                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="category">
            <div class="panel panel-default">
                <div class="panel-body table-responsive">
                    <div class="dd" id="div_nestable">
                        <ol class="dd-list">
                            @foreach($list as $value)
                                <li class="dd-item" data-id="{$value['id']}">
                                    <div class="dd-handle" >
                                        <p> <img src="{{tomedia($value['thumb'])}}" width='30' height="30"
                                             onerror="$(this).remove()"
                                             style='padding:1px;border: 1px solid #ccc;float:left;'/> &nbsp;
                                        [ID: {{$value['id']}}] {{$value['name']}}</p>
                                        <span class="pull-right">
                                            <a class='btn btn-default btn-sm'
                                               href="{{yzWebUrl('filtering.filtering.edit', ['id'=>$value['id']])}}"
                                               title="修改">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a class='btn btn-default btn-sm'
                                               href="{{yzWebUrl('filtering.filtering.del', ['id'=>$value['id']])}}"
                                               title='删除' onclick="return confirm('确认删除此过滤值吗？');return false;">
                                                <i class="fa fa-remove"></i>
                                            </a>
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                        {!! $pager !!}

                    </div>
                </div>
                <div class='panel-footer'>
                    <a class='btn btn-info' href="{{yzWebUrl('filtering.filtering.create',['parent_id'=>$parent->id])}}"><i class='fa fa-plus'></i> 添加标签</a>

                </div>
            </div>
        </div>
    </div>

@endsection