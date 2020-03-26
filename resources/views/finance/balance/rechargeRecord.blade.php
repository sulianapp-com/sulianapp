@extends('layouts.base')
@section('title', '充值记录')
@section('content')
<link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div id="recharge-blade" class="rightlist">

        <div class="right-titpos">
            <ul class="add-snav">
                <li class="active"><a href="#">余额充值记录</a></li>
            </ul>
        </div>

        <div class="panel panel-info">
            <div class="panel-body">
                <form action=" " method="post" class="form-horizontal" role="form" id="form1">
                    <input type="hidden" name="c" value="site"/>
                    <input type="hidden" name="a" value="entry"/>
                    <input type="hidden" name="m" value="yun_shop"/>

                    <div class="form-group">
                        <div class="col-sm-12 col-lg-12 col-xs-12">
                            <div class='input-group'>
                                <input class="form-control" name="search[ordersn]" type="text" value="{{ $search['ordersn'] or ''}}" placeholder="充值单号">
                                <input class="form-control" name="search[realname]" type="text" value="{{ $search['realname'] or ''}}" placeholder="会员ID／姓名／昵称／手机号">

                                <div class="form-input">
                                    <select name="search[level_id]" class="form-control">
                                        <option value="" selected>会员等级</option>
                                        @foreach($memberLevel as $level)
                                            <option value="{{ $level['id'] }}" @if($search['level_id'] == $level['id']) selected @endif>{{ $level['level_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class='form-input'>
                                    <select name="search[group_id]" class="form-control">
                                        <option value="" selected >会员分组</option>
                                        @foreach($memberGroup as $group)
                                            <option value="{{ $group['id'] }}" @if($search['group_id'] == $group['id']) selected @endif>{{ $group['group_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-xs-12 col-sm-6 col-lg-6 search-time">
                        <div class="time-select" >
                            <select name='search[searchtime]' class='form-control'>
                                <option value='' @if(empty($search['searchtime'])) selected @endif>不搜索充值时间</option>
                                <option value='1' @if($search['searchtime']==1) selected @endif >搜索充值时间</option>
                            </select>
                        </div>
                        <div class="time-btn">
                            {!! tpl_form_field_daterange(
                                'search[time_range]',
                                array(
                                    'starttime'=>array_get($requestSearch,'time_range.start',0),
                                    'endtime'=>array_get($requestSearch,'time_range.end',0),
                                    'start'=>0,
                                    'end'=>0
                                ),
                                true
                            )!!}
                        </div>
                    </div>




                    <div class="form-group col-xs-12 col-sm-6 col-lg-6 search-btn">
                        <!--<label class="col-xs-12 col-sm-12 col-md-1 col-lg-1 control-label"></label>-->
                        <div class="btn-input">
                            <input type="submit" class="btn btn-block btn-success" value="搜索">
                            <!--<button type="submit" name="export" value="1" class="btn btn-primary">导出 Excel</button>-->
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">总数：{{ $recordList->total() }}</div>
            <div class="panel-body ">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                        <tr>
                            <th style='width:15%; text-align: center;'>充值单号</th>
                            <th style='width:10%; text-align: center;'>粉丝</th>
                            <th style='width:10%; text-align: center;'>会员信息<br/>手机号</th>
                            <th style='width:10%; text-align: center;' class='hidden-xs'>等级/分组</th>
                            <th style='width:12%; text-align: center;'>充值时间</th>
                            <th style='width:10%; text-align: center;'>充值方式</th>
                            <th style='width:10%; text-align: center;'>充值金额<br/>状态</th>
                            <th style='width:13%; text-align: center;'>备注信息</th>
                            <th style='width:10%; text-align: center;'>操作</th>
                        </tr>
                    </thead>
                @foreach($recordList as $list)
                    <tr style="text-align: center;">
                        <td>{{ $list->ordersn }}</td>
                        <td>
                            @if($list->member->avatar || $shopSet['headimg'])
                            <img src='{{ $list->member->avatar ? tomedia($list->member->avatar) : tomedia($shopSet['headimg'])}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                            <br/>
                            @endif
                            {{ $list->member->nickname ? $list->member->nickname : '未更新' }}
                        </td>
                        <td>
                            {{ $list->member->realname }}
                            <br/>
                            {{ $list->member->mobile }}
                        </td>

                        <td class='hidden-xs'>
                            {{ $list->member->yzMember->level->level_name or $shopSet['level_name']}}
                            <br />
                            {{ $list->member->yzMember->group->group_name or '' }}
                        </td>

                        <td>{{ $list->created_at }}</td>
                        <td>
                            @if($list->type == 0)
                                <span class='label label-default'>{{ $list->type_name }}</span>
                            @elseif($list->type ==1)
                                <span class='label label-success'>{{ $list->type_name }}</span>
                            @elseif($list->type == 2)
                                <span class='label label-warning'>{{ $list->type_name }}</span>
                            @elseif($list->type == 9 || $list->type == 10)
                                <span class='label label-info'>{{ $list->type_name }}</span>
                            @else
                                <span class='label label-primary'>{{ $list->type_name }}</span>
                            @endif

                        </td>
                        <td>
                            {{ $list->money }}
                            <br/>
                            @if($list->status == 1)
                                <span class='label label-success'>充值成功</span>
                            @elseif($list->status == '-1')
                                <span class='label label-warning'>充值失败</span>
                            @else
                                <span class='label label-default'>申请中</span>
                            @endif

                        </td>
                        <td><a style="color: #0a0a0a" title="{{ $list->remark }}">{{ $list->remark }}</a></td>
                        <td>
                            <a class='btn btn-default' href="{{ yzWebUrl('member.member.detail', array('id' => $list->member_id)) }}" style="margin-bottom: 2px">用户信息</a>
                        </td>
                    </tr>
                @endforeach
                </table>
                {!! $page !!}
            </div>
        </div>
    </div>


@endsection