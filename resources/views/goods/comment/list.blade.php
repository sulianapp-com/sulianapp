@extends('layouts.base')

@section('content')
@section('title', trans('商品评论列表'))
    <div id="goods-comment-list" class="w1200 m0a">

        <link href="../addons/sz_yi/template/mobile/default/static/js/star-rating.css" media="all" rel="stylesheet"
              type="text/css"/>
        <script src="../addons/sz_yi/template/mobile/default/static/js/star-rating.js" type="text/javascript"></script>
        <link href="{{static_url('yunshop/goods/goods.css')}}" media="all" rel="stylesheet" type="text/css"/>
        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">评价管理</a></li>
            </ul>
        </div>
        <form action="" method="post" class="form-horizontal">
            <div class="panel panel-info">
                <div class="panel-body">
                    <div class="form-group col-xs-12 col-sm-12 col-lg-3">
                       <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">关键词</label>-->
                        <div class="">
                            <input class="form-control" name="search[keyword]" id="" type="text"
                                   value="{{$search['keyword']}}" placeholder="商品标题">
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 col-lg-3">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">类型</label>-->
                        <div class="">
                            <select name='search[fade]' class='form-control'>
                                <option value='' @if($search['fade']=='') selected @endif>全部评价类型</option>
                                <option value='2' @if($search['fade']=='2') selected @endif>模拟评价</option>
                                <option value='1' @if($search['fade']=='1') selected @endif >真实评价</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-12 col-lg-5 search-time">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">按时间</label>-->
                        <div class="time-select" >
                            <select name='search[searchtime]' class='form-control'>
                                <option value='' @if(empty($search['searchtime'])) selected @endif>不搜索时间区间</option>
                                <option value='1' @if($search['searchtime']==1) selected @endif >搜索时间区间</option>
                            </select>
                        </div>
                        <div class="time-btn" >
                            <div class="search-select">
                            </div>
                                {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', ['starttime'=>date('Y-m-d H:i', $search['starttime']), 'endtime'=>date('Y-m-d H:i',$search['endtime']),'start'=>0,'end'=>0], true) !!}
                        </div>

                    </div>

                    <div class="form-group col-xs-12 col-sm-1 col-lg-1 search-btn">
                        <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"> </label>-->
                        <div class="btn-input">
                            <input type="submit" class="btn btn-block btn-success" value="搜索">
                        </div>
                    </div>

                </div>
            </div>
        </form>

        <div class='panel panel-default'>
            <div class='panel-heading'>
                评价管理 (数量: {{$total}} 条)

            </div>
            <div class='panel-body'>

                <table class="table">
                    <thead>
                    <tr>
                        <th style='width:24%;'>商品信息</th>
                        <th style='width:6%;'>评价者</th>
                        <th style='width:10%;'>评分等级</th>
                        <th style='width:14%;'>评价时间</th>
                        <th style='width:14%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $row)
                        <tr>
                            <td><img src="{{tomedia($row['goods']['thumb'])}}"
                                     style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                {{$row['goods']['title']}}
                            </td>
                            <td>
                                <img src="{{tomedia($row['head_img_url'])}}"
                                     style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                                {{$row['nick_name']}}
                            </td>
                            <td style="color:#f39c12">
                                @if($row['level'] >= 1) <i class='fa fa-star'></i> @else <i
                                        class='fa fa-star-o'></i> @endif
                                @if($row['level'] >= 2) <i class='fa fa-star'></i> @else <i
                                        class='fa fa-star-o'></i> @endif
                                @if($row['level'] >= 3) <i class='fa fa-star'></i> @else <i
                                        class='fa fa-star-o'></i> @endif
                                @if($row['level'] >= 4) <i class='fa fa-star'></i> @else <i
                                        class='fa fa-star-o'></i> @endif
                                @if($row['level'] >= 5) <i class='fa fa-star'></i> @else <i
                                        class='fa fa-star-o'></i> @endif
                            </td>

                            <td>{{$row['created_at']}}</td>
                            <td>
                                @if(!empty($row['uid']))
                                    <a class='btn btn-default'
                                       href="{{yzWebUrl('goods.comment.reply', ['id' => $row['id']])}}"
                                       title='进行回复'><i class="fa fa-reply"></i>
                                    </a>
                                @else
                                    <a class='btn btn-default'
                                       href="{{yzWebUrl('goods.comment.updated', ['id' => $row['id']])}}"
                                       title='修改评价'><i class="fa fa-edit"></i>
                                    </a>
                                @endif

                                <a class='btn btn-default'
                                   href="{{yzWebUrl('goods.comment.add-comment', ['goods_id' => $row['goods_id']])}}"
                                   title='添加此商品评价'><i class="fa fa-plus"></i>
                                </a>
                                <a class='btn btn-default'
                                   href="{{yzWebUrl('goods.comment.deleted', ['id' => $row['id']])}}"
                                   onclick="return confirm('确认删除此评价吗？');return false;"><i class="fa fa-remove"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {!! $pager !!}

            </div>
            <div class='panel-footer'>
                <a class='btn btn-info' href="{{yzWebUrl('goods.comment.add-comment')}}"><i
                            class='fa fa-plus'></i> 添加评价</a>
            </div>
        </div>

    </div>
    </div>
@endsection