@extends('layouts.base')

@section('content')
@section('title', trans('商品分类列表'))
    <div class="w1200 m0a">
        <script language="javascript" src="{{static_url('js/dist/nestable/jquery.nestable.js')}}"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/goods/goods.css')}}"/>
        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="{{yzWebUrl('goods.category.index')}}">商品分类 </a></li>
                @if(!empty($parent))
                    <li> <i class="fa fa-angle-double-right"></i>上级分类:{{$parent->name}}</li>
                    <li class="active back">
                        <a href="{{yzWebUrl('goods.category.index', ['parent_id'=>$parent['parent_id'], 'level'=>$parent['level']])}}">
                            <button type="button" class="btn btn-block btn-info">返回上一级</button>

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
                            @foreach($list as $category)
                                <li class="dd-item" data-id="{$category['id']}">
                                    <div class="dd-handle" >
                                        <p> <img src="{{tomedia($category['thumb'])}}" width='30' height="30"
                                             onerror="$(this).remove()"
                                             style='padding:1px;border: 1px solid #ccc;float:left;'/> &nbsp;
                                        [ID: {{$category['id']}}] {{$category['name']}}</p>
                                        <span class="pull-right">
                                            @if($category['level'] < 3)
                                                <a class='btn btn-default btn-sm'
                                                   href="{{yzWebUrl('goods.category.index', ['parent_id'=>$category['id']])}}"
                                                   title='子分类'>子分类</a>

                                                <a class='btn btn-default btn-sm'
                                                   href="{{yzWebUrl('goods.category.add-category', ['parent_id'=>$category['id'], 'level'=>$category['level']+1])}}"
                                                   title='添加子分类'><i class="fa fa-plus"></i></a>
                                            @endif
                                            <a class='btn btn-default btn-sm'
                                               href="{{yzWebUrl('goods.category.edit-category', ['id'=>$category['id']])}}"
                                               title="修改">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a class='btn btn-default btn-sm'
                                               href="{{yzWebUrl('goods.category.deleted-category', ['id'=>$category['id']])}}"
                                               title='删除' onclick="return confirm('确认删除此分类吗？');return false;">
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
                    <a class='btn btn-info' href="{{yzWebUrl('goods.category.add-category',['parent_id'=>$parent->id, 'level'=>$parent->level+1])}}"><i class='fa fa-plus'></i> 添加新分类</a>

                </div>
            </div>
        </div>
    </div>

@endsection