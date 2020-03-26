@extends('layouts.base')
@section('title', '余额管理')
@section('content')
<link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
        <div id="member-blade" class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            {{--<div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="#">余额管理</a></li>
                </ul>
            </div>--}}
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info">
                <div class="panel-heading">筛选</div>
                <div class="panel-body">
                    <form action="" method="post" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site" />
                        <input type="hidden" name="a" value="entry" />
                        <input type="hidden" name="m" value="yun_shop" />
                        <input type="hidden" name="do" value="member" id="form_do" />

                        <div class="form-group col-sm-11 col-lg-11 col-xs-12">
                            <div class="">
                                <div class='input-group'>

                                    <input class="form-control" name="search[realname]" type="text"
                                    value="{{ $search['realname'] or ''}}" placeholder="会员姓名／昵称／手机号">

                                    <div class='form-input'>
                                        <p class="input-group-addon" >会员等级</p>
                                        <select name="search[level]" class="form-control">
                                            <option value="" selected>不限</option>
                                            @foreach($memberLevel as $level)
                                            <option value="{{ $level['id'] }}" @if($search['level'] == $level['id']) selected @endif>{{ $level['level_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class='form-input'>
                                        <p class="input-group-addon" >会员分组</p>
                                        <select name="search[groupid]" class="form-control">
                                            <option value="" selected >不限</option>
                                            @foreach($memberGroup as $group)
                                            <option value="{{ $group['id'] }}" @if($search['groupid'] == $group['id']) selected @endif>{{ $group['group_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class='form-input'>
                                        <p class="input-group-addon price">余额区间</p>
                                        <input class="form-control price" name="search[min_credit2]" type="text" value="{{ $search['min_credit2'] or ''}}" placeholder="最小">
                                        <p class="line">—</p>
                                        <input class="form-control price" name="search[max_credit2]" type="text" value="{{ $search['max_credit2'] or ''}}" placeholder="最大">
                                    </div>

                                </div>
                            </div>
                        </div>


                        <div class="form-group col-sm-1 col-lg-1 col-xs-12">
                            <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                            <div class="">
                                <input type="submit" class="btn btn-block btn-success" value="搜索">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{{ $memberList->total() }}</div>
                    <div class="panel-body">
                        <table class="table table-hover" style="overflow:visible;">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:8%;text-align: center;'>会员ID</th>
                                <th style='width:8%;text-align: center;'>粉丝</th>
                                <th style='width:12%;'>姓名<br/>手机号码</th>
                                <th style='width:8%;'>等级/分组</th>
                                <th style='width:15%;'>余额</th>
                                <th style='width:8%'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($memberList as $list)
                            <tr>
                                <td style="text-align: center;">{{ $list->uid }}</td>
                                <td style="text-align: center;">

                                    @if($list->avatar || $shopSet['headimg'])
                                    <img src='{{ $list->avatar ? tomedia($list->avatar) : tomedia($shopSet['headimg']) }}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc' /><br/>
                                    @endif

                                    {{ $list->nickname ?: '未更新'}}

                                </td>
                                <td>{{ $list->realname }}<br/>{{ $list->mobile }}</td>
                                <td>
                                    {{ isset($list->yzMember->level) ? $list->yzMember->level->level_name : $shopSet['level_name'] }}
                                    <br/>
                                    {{ $list->yzMember->group->group_name or '' }}
                                </td>
                                <td>
                                    <label class="label label-danger">余额：{{ $list->credit2 }}</label>
                                </td>
                                <td  style="overflow:visible;">
                                    <a class='btn btn-default' href="{{ yzWebUrl('balance.recharge.index', array('member_id' => $list->uid)) }}" style="margin-bottom: 2px">充值余额</a>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {!! $pager !!}

                    </div>
                </div>
            </div>

@endsection