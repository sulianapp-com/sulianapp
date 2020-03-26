@extends('layouts.base')
@section('title', '会员列表')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a"  style="padding-bottom:80px">
        <div class="rightlist" style="padding-bottom:100px">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('member.member.index')}}">会员管理</a></li>
                    <li><a href="javascript:void"> &nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;全部会员</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="yun_shop"/>
                        <input type="hidden" name="do" value="member" id="form_do"/>
                        <input type="hidden" name="route" value="member.member.index" id="route"/>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                            <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">ID</label>-->
                            <div class="">
                                <input type="text" placeholder="会员ID" class="form-control" name="search[mid]"
                                       value="{{$request['search']['mid']}}"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>-->
                            <div class="">
                                <input type="text" class="form-control" name="search[realname]"
                                       value="{{$request['search']['realname']}}" placeholder="可搜索昵称/姓名/手机号"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control" name="search[first_count]"
                                       value="{{$request['search']['first_count']}}" placeholder="一级人数"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control" name="search[second_count]"
                                       value="{{$request['search']['second_count']}}" placeholder="二级人数"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control" name="search[third_count]"
                                       value="{{$request['search']['third_count']}}" placeholder="三级人数"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control" name="search[team_count]"
                                       value="{{$request['search']['team_count']}}" placeholder="团队人数"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control" name="search[custom_value]"
                                       value="{{$request['search']['custom_value']}}" placeholder="自定义字段"/>
                            </div>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员等级</label>-->
                            <div class="">
                                <select name='search[level]' class='form-control'>
                                    <option value=''>会员等级不限</option>
                                    @foreach($levels as $level)
                                        <option value='{{$level['id']}}'
                                                @if($request['search']['level']==$level['id'])
                                                selected
                                                @endif
                                        >{{$level['level_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!--  <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员分组</label>-->
                            <div class="">
                                <select name='search[groupid]' class='form-control'>
                                    <option value=''>会员分组不限</option>
                                    @foreach($groups as $group)
                                        <option value='{{$group['id']}}'
                                                @if($request['search']['groupid']==$group['id'])
                                                selected
                                                @endif
                                        >{{$group['group_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <select name='search[isagent]' class='form-control'>
                                    <option value=''>推广员不限</option>
                                    <option value='0'
                                            @if($request['search']['isagent']=='0')
                                            selected
                                            @endif>否
                                    </option>
                                    <option value='1'
                                            @if($request['search']['isagent']=='1')
                                            selected
                                            @endif>是
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!--      <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">是否关注</label>-->
                            <div class="">
                                <select name='search[followed]' class='form-control'>
                                    <option value=''>不限关注</option>
                                    </option>
                                    <option value='1'
                                            @if($request['search']['followed']=='1')
                                            selected
                                            @endif
                                    >已关注
                                    </option>
                                    <option value='0'
                                            @if($request['search']['followed']=='0')
                                            selected
                                            @endif
                                    >未关注
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!--        <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">黑名单</label>-->
                            <div class="">
                                <select name='search[isblack]' class='form-control'>
                                    <option value=''>不限黑名单</option>
                                    <option value='0'
                                            @if($request['search']['isblack']=='0')
                                            selected
                                            @endif>否
                                    </option>
                                    <option value='1'
                                            @if($request['search']['isblack']=='1')
                                            selected
                                            @endif>是
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg">

                            <div class="time">

                                <select name='search[searchtime]' class='form-control'>
                                    <option value='0'
                                            @if($request['search']['searchtime']=='0')
                                            selected
                                            @endif>注册时间不限
                                    </option>
                                    <option value='1'
                                            @if($request['search']['searchtime']=='1')
                                            selected
                                            @endif>搜索注册时间
                                    </option>
                                </select>
                            </div>
                            <div class="search-select">
                                {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[times]', [
                                'starttime'=>date('Y-m-d H:i', $starttime),
                                'endtime'=>date('Y-m-d H:i',$endtime),
                                'start'=>0,
                                'end'=>0
                                ], true) !!}
                            </div>
                        </div>

                        <div class="form-group  col-xs-12 col-md-12 col-lg-6">
                            <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                            <div class="">
                                <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>
                                <button type="button" name="export" value="1" id="export" class="btn btn-default">导出
                                    Excel
                                </button>


                            </div>
                        </div>

                    </form>

                </div>
            </div>
            <div class="clearfix panel-heading">
                <a class="btn btn-info dropdown-toggle" style="height: 35px;margin-top: 5px;color: white;" href="{{yzWebUrl('member.member.add-member')}}">添加会员</a>
                <a class="btn btn-info dropdown-toggle" style="height: 35px;margin-top: 5px;color: white;" href="{{yzWebUrl('member.member.import')}}">会员excel导入</a>
            </div>
            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{{$total}}   </div>
                    <div class="panel-body" style="margin-bottom:200px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:7%;text-align: center;'>会员ID</th>
                                @if($opencommission)
                                    <th style='width:8%;text-align: center;'>推荐人</th>
                                @endif
                                <th style='width:8%;text-align: center;'>粉丝</th>
                                <th style='width:10%;'>姓名<br/>手机号码</th>
                                <th style='width:8%;'>等级/分组</th>
                                <th style='width:10%;'>注册时间</th>
                                <th style='width:15%;'>积分/余额</th>
                                <th style='width:15%;'>已完成订单</th>
                                <th style='width:6%'>关注</th>
                                <th style='width:13%'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list['data'] as $row)
                                <tr>
                                    <td style="text-align: center;">   {{$row['uid']}}</td>

                                    @if($opencommission)
                                        <td style="text-align: center;"
                                            @if(!empty($row['yz_member']['parent_id']))
                                            title='ID: {{$row['yz_member']['parent_id']}}'
                                                @endif
                                        >
                                                @if(empty($row['yz_member']['parent_id']))
                                                    <label class='label label-primary'>总店</label>
                                                @else
                                                    @if(!empty($row['yz_member']['agent']['avatar']))
                                                        <img src='{{$row['yz_member']['agent']['avatar']}}'
                                                             style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                                                        <br/>
                                                    @endif
                                                    @if(empty($row['yz_member']['inviter']))
                                                            (暂定)
                                                    @endif
                                                    @if(empty($row['yz_member']['agent']['nickname']))
                                                            未更新
                                                    @else
                                                            {{$row['yz_member']['agent']['nickname']}}
                                                    @endif
                                                @endif
                                        </td>
                                    @endif
                                    <td style="text-align: center;">
                                        @if(!empty($row['avatar']))
                                            <img src='{{$row['avatar']}}'
                                                 style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/><br/>
                                        @endif
                                        @if(empty($row['nickname']))
                                            未更新
                                        @else
                                            {{$row['nickname']}}
                                        @endif
                                    </td>
                                    <td>{{$row['realname']}}<br/>{{$row['mobile']}}</td>
                                    <td>
                                        @if(empty($row['yz_member']['level']['level_name']))
                                            {{$set['level_name']}}
                                        @else
                                            {{$row['yz_member']['level']['level_name']}}
                                        @endif
                                        <br/>
                                        @if(empty($row['yz_member']['group']['group_name']))
                                            无分组
                                        @else
                                            {{$row['yz_member']['group']['group_name']}}
                                        @endif
                                    </td>
                                    <td>
                                        {{date('Y-m-d',$row['createtime'])}}<br/>
                                        {{date('H:i',$row['createtime'])}}</td>
                                    <td><label class="label label-info">积分：{{$row['credit1']}}</label><br/><label
                                                class="label label-danger">余额：{{$row['credit2']}}</label></td>
                                    <td><label class="label label-info">订单：
                                            @if(!empty($row['has_one_order']['total']))
                                                {{$row['has_one_order']['total']}}
                                            @else
                                                0
                                            @endif</label><br/>
                                        <label class="label label-danger">金额：@if(!empty($row['has_one_order']['sum']))
                                                {{$row['has_one_order']['sum']}}
                                            @else
                                                0
                                            @endif</label></td>
                                    <td>
                                        @if($row['yz_member']['is_black']==1)
                                            <span class="label label-default"
                                                  style='color:#fff;background:black'>黑名单</span>
                                        @else
                                            @if(empty($row['has_one_fans']['followed']))
                                                <label class='label label-default'>未关注</label>
                                            @else
                                                <label class='label label-success'>已关注</label>
                                            @endif
                                        @endif
                                    </td>
                                    <td  style="overflow:visible;">
                                        <div class="btn-group btn-group-sm" >
                                            <a class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="javascript:;">操作 <span class="caret"></span></a>
                                            <ul class="dropdown-menu dropdown-menu-left" role="menu" style='z-index: 9999'>
                                                <li><a href="{{yzWebUrl('member.member.detail', ['id' => $row['uid']])}}" title="会员详情"><i class='fa fa-edit'></i> 会员详情</a></li>
                                                <li><a href="{{yzWebUrl('member.member-income.index', ['id' => $row['uid']])}}" title="收入记录"><i class='fa fa-edit'></i> 收入详情</a></li>
                                                <li><a href="{{yzWebUrl('order.list', ['search[ambiguous][field]' => 'order','search[ambiguous][string]'=>'uid:'.$row['uid']])}}" title='会员订单'><i class='fa fa-list'></i> 会员订单</a></li>
                                                <li><a href="{{yzWebUrl('point.recharge.index',['id'=>$row['uid']])}}" title='充值积分'><i class='fa fa-credit-card'></i> 充值积分</a></li>
                                                <li><a href="{{yzWebUrl('balance.recharge.index', ['member_id'=>$row['uid']])}}" title='充值余额'><i class='fa fa-money'></i> 充值余额 </a></li>
                                                <li><a href="{{yzWebUrl('member.member.agent-old', ['id'=>$row['uid']])}}" title='我的下线'><i class='fa fa-exchange'></i> 推广下线 </a></li>
                                                <li><a href="{{yzWebUrl('member.member.agent', ['id'=>$row['uid']])}}" title='团队下线'><i class='fa fa-exchange'></i> 团队下线 </a></li>
                                                <li><a href="{{yzWebUrl('member.member.agent-parent', ['id'=>$row['uid']])}}" title='我的上线'><i class='fa fa-exchange'></i> 推广上线 </a></li>
                                                @if($row['yz_member']['is_black']==1)
                                                    <li><a href="{{yzWebUrl('member.member.black', ['id' => $row['uid'],'black'=>0])}}" title='取消黑名单'><i class='fa fa-minus-square'></i> 取消黑名单</a></li>
                                                @else
                                                    <li><a href="{{yzWebUrl('member.member.black', ['id' => $row['uid'],'black'=>1])}}" title='设置黑名单'><i class='fa fa-minus-circle'></i> 设置黑名单</a></li>
                                                @endif

                                                <li>
                                                    <a href="{{yzWebUrl('member.bank-card.edit', ['member_id' => $row['uid']])}}"
                                                       title='银行卡管理'><i class='fa fa-credit-card'></i>银行卡管理</a>
                                                </li>
                                                <li>
                                                    <a href="{{yzWebUrl('member.member-address.index', ['member_id' => $row['uid']])}}"
                                                       title='收货地址管理'><i class='fa fa-truck'></i>收货地址管理</a>
                                                </li>
                                                <li>
                                                    {{--<a href="{{yzWebUrl('member.member.delete', ['id'=>$row['uid']])}}"--}}
                                                       {{--onclick="return confirm('确认删除该用户吗？此操作是不可逆的');return false;" title='删除会员'><i class='fa fa-delicious'></i>删除（危险）</a>--}}

                                                    <a href="javascript:return false;" title='禁用中' style="cursor: default;opacity: 0.5"><i class='fa fa-delicious'></i>删除（危险）</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {!!$pager!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>
        $(function () {
            $('#export').click(function () {
                $('#route').val("member.member.export");
                $('#form1').submit();
                $('#route').val("member.member.index");
            });
        });
    </script>
@endsection