@extends('layouts.base')
@section('title', '推广下线')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">

            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">会员信息</a></li>
                </ul>
            </div>

            <div class="panel panel-default">
                <div class='panel-body'>
                    <div style='height:100px;width:110px;float:left;'>
                        <img src='{{$member->avatar}}' style='width:100px;height:100px;border:1px solid #ccc;padding:1px' />
                    </div>
                    <div style='float:left;height:100px;overflow: hidden'>
                        昵称: {{$member->nickname}}<br/>
                        姓名: {{$member->realname}} <br/>
                        手机号: {{$member->mobile}}<br/>
                        余额: {{$member->credit2}} / 积分：{{$member->credit1}}<br/>
                    </div>
                </div>
            </div>

                <div class="panel panel-info">

                    <div class="panel-body">
                        <form action="" method="post" class="form-horizontal" role="form" id="form1">
                            <input type="hidden" name="route" value="member.member.agent" id="route" />
                            <input type="hidden" name="id" value="{{$request->id}}" />

                            <div class="form-group col-xs-12 col-sm-6 col-md-2">
                                <div class="">
                                    <input type="text" class="form-control"  name="aid" value="{{$request->aid}}" placeholder="请输入您的ID"/>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2">
                                <div class="">
                                    <input type="text" class="form-control"  name="keyword" value="{{$request->keyword}}" placeholder='可搜索昵称/名称/手机号'/>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-2">
                                <div class="">
                                    <select name='followed' class='form-control'>
                                        <option value=''>是否关注</option>

                                        <option value='1'
                                                @if($request->followed=='1')
                                                selected
                                                @endif
                                        >已关注
                                        </option>
                                        <option value='0'
                                                @if($request->followed=='0')
                                                selected
                                                @endif
                                        >未关注
                                        </option>
                                    </select>
                                </div>
                            </div>
                            {{--<div class="form-group col-xs-12 col-sm-6 col-md-2">--}}
                                {{--<div class="">--}}
                                    {{--<select name='status' class='form-control'>--}}
                                        {{--<option value=''>状态</option>--}}
                                        {{--<option value='0'--}}
                                                {{--@if($request->status == '0')--}}
                                                {{--selected--}}
                                                {{--@endif--}}
                                        {{-->未审核</option>--}}
                                        {{--<option value='2'--}}
                                                {{--@if($request->status == '2')--}}
                                                {{--selected--}}
                                                {{--@endif--}}
                                        {{-->已审核</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div  class="form-group col-xs-12 col-sm-6 col-md-2">
                                <div class="">
                                    <select name='isblack' class='form-control'>
                                        <option value=''>黑名单状态</option>
                                        <option value='0'
                                           @if($request->isblack == '0')
                                           selected
                                           @endif
                                        >否</option>
                                        <option value='1'
                                           @if($request->isblack == '1')
                                           selected
                                           @endif
                                        >是</option>
                                    </select>
                                </div>
                            </div>
                            <div  class="form-group col-xs-12 col-sm-6 col-md-2">
                                <div class="">
                                    <select name='level' class='form-control'>
                                        <option value=''>层级</option>
                                        @foreach($level_total as $level)
                                        <option value='{{ $level['level'] }}' @if($request->level == $level['level']) selected @endif>
                                            {{ $level['level'] }}级（{{ $level['total'] }}）
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                                <label class=" control-label"></label>
                                <button class="btn btn-success"><i class="fa fa-search"></i> 搜索</button>
                                <button type="button" name="export" value="1" id="export" class="btn btn-default">导出 Excel</button>
                            </div>
                        </form>
                    </div>
                </div>


            <div class="panel panel-default"  style="margin-bottom:200px!important">
                <div class="panel-heading">总数：{{$total}}</div>
                <div class="panel-body">
                    <table class="table table-hover"   style="overflow:visible;">
                        <thead class="navbar-inner">
                        <tr>
                            <th style='width:5%;'>会员ID</th>
                            {{--<th style='width:13%;text-align: center;'>推荐人</th>--}}
                            <th style='width:10%;text-align: center;'>粉丝</th>
                            <th style='width:12%;'>姓名</th>
                            <th style='width:12%;'>手机号码</th>
                            {{--<th style='width:12%;'>状态</th>--}}
                            <th style='width:8%;text-align: center;'>下线人数</th>
                            <th style='width:14%;'>注册时间</th>
                            <th style='width:10%;text-align: center;'>关注</th>
                            <th style='width:13%'>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['data'] as $row)
                        <tr>
                            <td>{{$row['member_id']}}</td>
                            {{--<td  style="text-align: center;" @if(!empty($row['yz_member']['parent_id']))title='ID: {{$row['yz_member']['parent_id']}}'@endif>--}}
                            {{--@if(empty($row['yz_member']['parent_id']))--}}
                                {{--@if($row['yz_member']['is_agent']==1)--}}
                            {{--<label class='label label-primary'>总店</label>--}}
                                {{--@else--}}
                            {{--<label class='label label-default'>暂无</label>--}}
                                {{--@endif--}}
                            {{--@else--}}
                            {{--<img src='{{$row['yz_member']['agent']['avatar']}}' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /><br/> {{$row['yz_member']['agent']['nickname']}}--}}
                            {{--@endif--}}
                            {{--</td>--}}
                            <td  style="text-align: center;">
                                    @if(!empty($row['has_one_child_member']['avatar']))
                                        <img src='{{$row['has_one_child_member']['avatar']}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif
                                    @if(empty($row['has_one_child_member']['nickname']))
                                        未更新
                                    @else
                                        {{$row['has_one_child_member']['nickname']}}
                                    @endif
                            </td>
                            <td>{{$row['has_one_child_member']['realname']}}</td>
                            <td>{{$row['has_one_child_member']['mobile']}}</td>
                            <td style="text-align: center;">{{$row['has_one_lower']['first'] ?: 0}}</td>


                            <td>{{date('Y-m-d H:i',$row['has_one_child_member']['createtime'])}}</td>
                            <td class="text-center">
                                @if(empty($row['has_one_child_fans']['follow']))
                                    @if(empty($row['has_one_child_fans']['uid']))
                                        <label class='label label-default'>未关注</label>
                                    @else
                                        <label class='label label-warning'>取消关注</label>
                                    @endif
                                @else
                                    <label class='label label-success'>已关注</label>
                                @endif
                            </td>
                            <td  style="overflow:visible;">

                                <div class="btn-group btn-group-sm" >
                                    <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">操作 <span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 9999'>
                                        <li><a href="{{yzWebUrl('member.member.detail', ['id' => $row['member_id']])}}" title="会员详情"><i class='fa fa-edit'></i> 会员详情</a></li>
                                        <li><a  href="{{yzWebUrl('order.list', ['search[ambiguous][field]' => 'member','search[ambiguous][string]'=>'uid:'.$row['member_id']])}}" target="_blank" title='会员订单'><i class='fa fa-list'></i> 会员订单</a></li>
                                        <li><a href="{{yzWebUrl('point.recharge.index',['id'=>$row['member_id']])}}" title='充值积分'><i class='fa fa-credit-card'></i> 充值积分</a></li>
                                        <li><a href="{{yzWebUrl('balance.recharge.index', ['member_id'=>$row['member_id']])}}" title='充值余额'><i class='fa fa-money'></i> 充值余额 </a></li>
                                        <li><a href="{{yzWebUrl('member.member.agent-old', ['id'=>$row['member_id']])}}" title='我的下线'><i class='fa fa-exchange'></i> 推广下线 </a></li>
                                        <li><a href="{{yzWebUrl('member.member.agent', ['id'=>$row['member_id']])}}" title='团队下线'><i class='fa fa-exchange'></i> 团队下线 </a></li>
                                        <li><a href="{{yzWebUrl('member.member.agentParent', ['id'=>$row['member_id']])}}" title='我的上线'><i class='fa fa-exchange'></i> 我的上线 </a></li>
                                        @if($row['yz_member']['is_black']==1)
                                            <li><a href="{{yzWebUrl('member.member.black', ['id' => $row['member_id'],'black'=>0])}}" title='取消黑名单'><i class='fa fa-minus-square'></i> 取消黑名单</a></li>
                                        @else
                                            <li><a href="{{yzWebUrl('member.member.black', ['id' => $row['member_id'],'black'=>1])}}" title='设置黑名单'><i class='fa fa-minus-circle'></i> 设置黑名单</a></li>
                                        @endif
                                        <li><a  href="{{yzWebUrl('member.member.delete', ['id' => $row['member_id']])}}" title='删除会员' onclick="return confirm('确定要删除该会员吗？');"><i class='fa fa-remove'></i> 删除会员</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {!! $pager !!}
                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>
        $(function () {
            $('#export').click(function () {
                $('#route').val("member.member.agent-export");
                $('#form1').submit();
                $('#route').val("member.member.agent-parent");
            });
        });
    </script>

@endsection