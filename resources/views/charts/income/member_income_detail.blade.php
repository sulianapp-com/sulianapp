@extends('layouts.base')
@section('title', '会员详情')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('charts.income.member-income.index')}}">会员收入统计</a></li>
                    <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;收入详情</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="{{yzWebUrl('member.member.update', ['id'=> $member['uid']])}}" method='post'
                  class='form-horizontal'>
                <input type="hidden" name="id" value="{{$member['uid']}}">
                <input type="hidden" name="op" value="detail">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="member"/>

                <div class="panel panel-default">
                    <div class='panel-body'>
                        <div style='height:auto;width:120px;float:left;'>
                            <img src='{{$member['avatar']}}'
                                 style='width:100px;height:100px;border:1px solid #ccc;padding:1px'/>
                        </div>

                        <div style='float:left;height:auto;overflow: hidden; width: 100px; font-size:16px;'>
                            <p>
                                <b>会员id:</b>
                                {{$member['uid']}}
                            </p>
                            <p>
                                <b>昵称:</b>
                                {{$member['nickname']}}
                            </p>
                            <p>
                                <b>姓名:</b>
                                {{$member['realname']}}
                            </p>
                        </div>
                        <div >
                            <div style='float:left;height:auto;overflow: hidden; margin-left: 300px;'>
                                <h5>累计收入:</h5>
                                <b style="font-size: 18px">{{$incomeAll['income']}}</b>
                            </div>
                            <div style='float:left;height:auto;overflow: hidden;margin-left: 200px;'>
                                <h5>累计提现:</h5>
                                <b style="font-size: 18px">{{$incomeAll['withdraw']}}</b>
                            </div>
                            <div style='float:left;height:auto;overflow: hidden; margin-left: 200px;'>
                                <h5>未提现:</h5>
                                <b style="font-size: 18px">{{$incomeAll['no_withdraw']}}</b>
                            </div>
                        </div>

                    </div>
                    <br>

                    <form action="{{yzWebUrl("finance.withdraw.dealt",['id'=>$item->id])}}" method='post' class='form-horizontal'>

                    </form>
                    <div class='panel-body'>
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr >
                                <th class="col-sm-2">收入时间</th>
                                <th class="col-sm-2">收入类型</th>
                                <th class="col-sm-2">收入金额</th>
                                <th class="col-sm-2">收入状态</th>
                                <th class="col-sm-2">收入详情</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($item as $k=>$row)
                                <tr>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row['type_name']}}</td>
                                    <td>{{$row['amount']}}</td>
                                    <td>{{$row['status'] ? '已提现' : '未提现'}}</td>
                                    <td>
                                        <a href="javascript:;" data-toggle="modal" data-target="#modal-refund{{$k}}">
                                            详情
                                        </a>
                                    </td>
                                </tr>

                                <div id="modal-refund{{$k}}" class="modal fade" tabindex="-1" role="dialog"
                                     style="width:600px;margin:0px auto;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×
                                                </button>
                                                <h3>收入信息</h3>

                                                @foreach(json_decode($row['detail'],true) as $data)
                                                    <div class="form-group">{{$data['title']}}</div>
                                                    @foreach($data['data'] as $value)
                                                        @if(!isset($value['title']))
                                                            @foreach($value as $v)
                                                                <div class="modal-body" style="background: #eee">
                                                                    <div class="form-group">
                                                                        <label class="control-label">{{$v['title']}}</label>
                                                                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                                                            {{$v['value']}}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="modal-body" style="background: #eee">
                                                                <div class="form-group">
                                                                    <label class="col-xs-10 col-sm-3 col-md-3 control-label">{{$value['title']}}</label>
                                                                    <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                                                        @if($value['title'] === '订单号')
                                                                            {{$value['value']}}
                                                                            <a target="_blank"
                                                                               href="{{yzWebUrl('order.list',['search'=>['ambiguous'=>['field'=>'order','string'=>$value['value']]]])}}">订单详情</a>
                                                                        @else
                                                                            {{$value['value']}}
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    @endforeach
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    @include('order.modals')
                    <div id="pager">{!! $pager !!}</div>
                    <div class="form-group col-sm-12">
                        <input type="button" class="btn btn-default" name="submit" onclick="goBack()" value="返回"
                               style='margin-left:10px;'/>
                    </div>


                </div>
            </form>
        </div>
    </div>


    <script language='javascript'>
        function goBack() {
            window.location.href="{!! yzWebUrl('charts.income.member_income.index') !!}";
        }
    </script>
@endsection