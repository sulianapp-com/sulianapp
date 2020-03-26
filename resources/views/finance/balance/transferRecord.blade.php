@extends('layouts.base')
@section('title', '转让记录')
@section('content')
    <link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div id="member-blade" class="rightlist">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">余额转让记录</a></li>
            </ul>
        </div>


        <div class="panel panel-info">
            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action="" method="post" class="form-horizontal" role="form" id="form1">

                    <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                        <div class="">
                            <div class='input-group'>
                                <input class="form-control" name="search[transfer]" type="text"
                                       value="{{ $search['transfer'] or ''}}" placeholder="转让者ID/昵称/姓名/手机号">
                                <input class="form-control" name="search[recipient]" type="text"
                                       value="{{ $search['recipient'] or ''}}" placeholder="被转让者ID/昵称/姓名/手机号">
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-sm-1 col-lg-1 col-xs-12">
                        <div class="">
                            <input type="submit" class="btn btn-block btn-success"
                                   value="搜索">
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">总数：{{ $tansferList->total() }}</div>
            <div class="panel-body ">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th style='width:10%;'>编号</th>
                        <th style='width:10%;'>转让人</th>
                        <th style='width:14%;'>被转让人</th>
                        <th style='width:12%;'>转让金额</th>
                        <th style='width:12%;'>转让时间</th>
                        <th style='width:12%;'>状态</th>
                    </tr>
                    </thead>
                    @foreach($tansferList as $list)
                        <tr>
                            <td>{{ $list->id }}</td>
                            <td>{{ $list->transferorInfo->realname or $list->transferorInfo->nickname }}({{ $list->transferor }})</td>
                            <td>{{ $list->recipientInfo->realname or $list->recipientInfo->nickname }}({{ $list->recipient }})</td>
                            <td>{{ $list->money }}</td>
                            <td>{{ $list->created_at }}</td>
                            <td>
                                @if($list->status == 1)
                                    <span class='label label-success'>转让成功</span>
                                @else
                                    <span class='label label-default'>转让失败</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
                {!! $pager !!}
            </div>
        </div>
    </div>


@endsection