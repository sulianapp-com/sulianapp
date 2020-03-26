@extends('layouts.base')
@section('title', '会员详情')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('member.member.index')}}">会员管理</a></li>
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

                        {{--<p>--}}
                            {{--<b>累计收入: </b><span style='color:red'>{{$incomeAll['income']}}</span> 元--}}
                        {{--</p>--}}
                        {{--<p>--}}
                            {{--<b> </b><span style='color:green'>{{$incomeAll['withdraw']}}</span> 元--}}
                        {{--</p>--}}
                        {{--<p>--}}
                            {{--<b>未提现: </b><span style='color:red'>{{$incomeAll['no_withdraw']}}</span> 元--}}
                        {{--</p>--}}
                    </div>
                    <br>

                    <form action="{{yzWebUrl("finance.withdraw.dealt",['id'=>$item->id])}}" method='post' class='form-horizontal'>
                        <div class='panel-body'>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr >
                                        <th style="font-size: 16px !important;font-weight:bold;">收入类型</th>
                                        <th style="font-size: 16px !important;font-weight:bold;">收入总金额</th>
                                        <th style="font-size: 16px !important;font-weight:bold;">已提现金额</th>
                                        <th style="font-size: 16px !important;font-weight:bold;">未提现金额</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 16px;">
                                @foreach($item as $k=>$row)
                                    <tr>
                                        <td>{{$row['type_name']}}</td>
                                        <td>{{$row['income']}}</td>
                                        <td>{{$row['withdraw']}}</td>
                                        <td>{{$row['no_withdraw']}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="form-group col-sm-12">
                            <input type="button" class="btn btn-default" name="submit" onclick="goBack()" value="返回"
                                   style='margin-left:10px;'/>
                        </div>
                    </form>

                </div>
            </form>
        </div>
    </div>


    <script language='javascript'>
        function goBack() {
            window.location.href="{!! yzWebUrl('member.member.index') !!}";
        }
    </script>
@endsection