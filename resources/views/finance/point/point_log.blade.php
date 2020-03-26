@extends('layouts.base')

@section('content')
<link href="{{static_url('yunshop/balance/balance.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div id="recharge-blade" class="rightlist">
        <div class="panel panel-info">

            <div class="right-titpos">
                @include('layouts.tabs')
            </div>

            <div class="panel-heading">筛选</div>
            <div class="panel-body">
                <form action=" " method="post" class="form-horizontal" role="form">
                    <div class="form-group">
                        <div class="col-sm-12 col-lg-12 col-xs-12">
                            <div class='input-group'>
                                <input class="form-control" name="search[realname]" type="text" value="{{ $search['realname'] or ''}}" placeholder="会员姓名／昵称／手机号">

                                <div class="input-group">
                                    <select name="search[level_id]" class="form-control">
                                        <option value="" selected>会员等级</option>
                                        @foreach($memberLevel as $level)
                                            <option value="{{ $level['id'] }}" @if($search['level_id'] == $level['id']) selected @endif>{{ $level['level_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class='input-group'>
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
                                <option value='' @if(empty($search['searchtime'])) selected @endif>不搜索</option>
                                <option value='1' @if($search['searchtime']==1) selected @endif >搜索</option>
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
            <div class="panel-heading">总数：{{ $list->total() }}</div>
            <div class="panel-body ">
                <table class="table table-hover">
                    <thead class="navbar-inner">
                        <tr>
                            <th style='width:5%; text-align: center;'>ID</th>
                            <th style='width:8%; text-align: center;'>粉丝</th>
                            <th style='width:8%; text-align: center;'>会员信息<br/>手机号</th>
                            <th style='width:10%; text-align: center;'>时间</th>
                            <th style='width:6%; text-align: center;'>业务类型</th>
                            <th style='width:10%; text-align: center;'>积分</th>
                            <th style='width:10%; text-align: center;'>收入/支出</th>
                            <th style='width:12%; text-align: center;'>操作</th>
                        </tr>
                    </thead>
                @foreach($list as $log)
                    <tr style="text-align: center;">
                        <td>{{ $log->id }}</td>
                        <td>
                            <img src='{{ $log->hasOneMember->avatar}}' style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                            <br/>
                            {{ $log->hasOneMember->nickname}}
                        </td>
                        <td>
                            {{ $log->hasOneMember->realname }}
                            <br/>
                            {{ $log->hasOneMember->mobile }}
                        </td>

                        <td>{{ $log->created_at }}</td>
                        <td>
                            <span class='label label-info'>{{$log->mode_name}}</span>
                        </td>
                        <td>
                            {{--<span class='label label-success'>{{ $log->before_point }}</span><br />--}}
                            <span class='label label-danger'>积分：{{ $log->after_point }}</span><br />
                            {{--<span class='label label-danger'>{{ $log->point }}</span>--}}
                        </td>
                        <td><span class='label label-success'>{{ $log->point }}</span></td>
                        <td>
                            <a class='btn btn-default' href="{{ yzWebUrl('member.member.detail', array('id' => $log->hasOneMember->uid)) }}" style="margin-bottom: 2px">用户信息</a>
                        </td>
                    </tr>
                @endforeach
                </table>
                {!! $pager !!}
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $("#ambiguous-field").on('change',function(){

                $(this).next('input').attr('placeholder',$(this).find(':selected').text().trim())
            });
        })
        $('#export').click(function () {
            $('#form_p').val("order.list.export");
            $('#form1').submit();
            $('#form_p').val("order.list");
        });
    </script>

@endsection