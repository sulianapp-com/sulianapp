@extends('layouts.base')

@section('content')
@section('title', trans('品牌列表'))
    <div class="w1200 m0a">
        <script language="javascript" src="{{static_url('js/dist/nestable/jquery.nestable.js')}}"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('js/dist/nestable/nestable.css')}}"/>

        <!-- 新增加右侧顶部三级菜单 -->
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="{{ yzWebUrl('goods.brand.index') }}">商品品牌 </a></li>
            </ul>
        </div>
        <!-- 新增加右侧顶部三级菜单结束 -->
        <div class="category">
            <div class="panel panel-default">
                <div class="panel-body table-responsive">
                    <div class="dd" id="div_nestable">
                        <ol class="dd-list">
                            @foreach($list['data'] as $brand)
                                <li class="dd-item" data-id="{$brand['id']}">
                                    <div class="dd-handle" style='width:100%;'>
                                        <img src="{{tomedia($brand['logo'])}}" width='30' height="30"
                                             style='padding:1px;border: 1px solid #ccc;float:left;'/> &nbsp;
                                        [ID: {{$brand['id']}}] {{$brand['name']}}
                                        <span class="pull-right">

                                                    <a class='btn btn-default btn-sm'
                                                       href="{{yzWebUrl('goods.brand.edit', ['id'=>$brand['id']])}}"
                                                       title='修改'><i class="fa fa-edit"></i>
                                                    </a>
                                                <a class='btn btn-default btn-sm'
                                                   href="{{yzWebUrl('goods.brand.deleted-brand', ['id'=>$brand['id']])}}"
                                                   title='删除' onclick="return confirm('确认删除此品牌吗？');return false;">
                                                    <i class="fa fa-remove"></i>
                                                </a>
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                        {!! $pager !!}


                        <!--<table class='table'>
                            <tr>
                                <td>
                                    <a href="{{yzWebUrl('goods.brand.add')}}"
                                       class="btn btn-success"><i class="fa fa-plus"></i> 添加新品牌</a>
                                </td>
                            </tr>
                        </table>-->


                    </div>

                </div>

                <div class='panel-footer'>
                    <a class='btn btn-info' href="{{yzWebUrl('goods.brand.add')}}"><i class='fa fa-plus'></i> 添加新品牌</a>

                </div>
            </div>
        </div>
    </div>

@endsection