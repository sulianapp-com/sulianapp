@extends('layouts.base')
@section('title', '资格申请')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
        @include('layouts.tabs')
           
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="post" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="route" value="member.member-relation.apply" id="route" />
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                            <div>
                                <input type="text" placeholder="ID/昵称/姓名/手机号" class="form-control" name="search[member]"
                                       value="{{$requestSearch['member']}}"/>
                            </div>
                        </div>


                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <select name='search[referee]' class='form-control'>
                                <option value='1' @if($request['referee']=='1') selected @endif>推荐人</option>
                                <option value='0' @if($request['referee']=='0') selected @endif>总店</option>
                            </select>
                        </div>

                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <div class="">
                                <input type="text" class="form-control" name="search[referee_info]"
                                       value="{{$requestSearch['referee_info']}}" placeholder="推荐人昵称/姓名/手机号"/>
                            </div>
                        </div>


                        <div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-8">

                            <div class="time">

                                <select name='search[searchtime]' class='form-control'>
                                    <option value=''>申请时间不限</option>
                                    <option value='0'
                                            @if($requestSearch['searchtime']=='0')
                                            selected
                                            @endif>不搜索申请时间
                                    </option>
                                    <option value='1'
                                            @if($requestSearch['searchtime']=='1')
                                            selected
                                            @endif>搜索申请时间
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

                        <div class="form-group col-sm-7 col-lg-4 col-xs-12">
                            <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label"></label>-->
                            <div class="">
                                <button type="button" name="export" value="1" id="export"
                                        class="btn btn-default excel back ">导出 Excel
                                </button>
                                <button class="btn btn-success "><i class="fa fa-search"></i> 搜索</button>

                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{{$total}}   </div>
                    <div class="panel-body">
                        <table class="table table-hover" style="overflow:visible;">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:8%;text-align: center;'>ID</th>
                                <th style='width:8%;text-align: center;'>推荐人</th>
                                <th style='width:8%;text-align: center;'>粉丝</th>
                                <th style='width:12%;'>姓名</th>
                                <th style='width:8%;'>手机号</th>
                                <th style='width:10%;'>申请时间</th>
                                <th style='width:15%;'>详情</th>
                                <th style='width:8%'>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list['data'] as $row)
                                <tr>
                                    <td style="text-align: center;">   {{$row['uid']}}</td>

                                    <td style="text-align: center;"
                                        @if(!empty($row['yz_member']['parent_id']))
                                        title='ID: {{$row['yz_member']['parent_id']}}'
                                            @endif
                                    >
                                        @if(empty($row['yz_member']['parent_id']))
                                            @if($row['yz_member']['is_agent']==1)
                                                <label class='label label-primary'>总店</label>
                                            @else
                                                <label class='label label-default'>暂无</label>
                                            @endif
                                        @else

                                            @if(!empty($row['yz_member']['agent']['avatar']))
                                                <img src='{{$row['yz_member']['agent']['avatar']}}'
                                                     style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                                                <br/>
                                            @endif
                                            @if(empty($row['yz_member']['agent']['nickname']))
                                                未更新
                                            @else
                                                {{$row['yz_member']['agent']['nickname']}}
                                            @endif
                                        @endif
                                    </td>

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
                                    <td>{{$row['realname']}}</td>
                                    <td>
                                        {{$row['mobile']}}
                                    </td>
                                    <td>
                                        {{date('Y.m.d',$row['yz_member']['apply_time'])}}</td>
                                    <td><a href="{{yzWebUrl('member.member.detail', ['id'=>$row['uid']])}}">查看会员详情</a>
                                    </td>
                                    <td style="overflow:visible;">
                                        <div class="btn-group btn-group-sm">
                                            <a class="btn btn-default pass" href="javascript:;"
                                               data-id="{{$row['uid']}}">通过 <span class="caret"></span></a>
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
                $('#route').val("member.member-relation.export");
                $('#form1').submit();
                $('#route').val("member.member-relation.apply");
            });

            $('.pass').click(function () {
                var id = $(this).data('id');
                var url = '{!! yzWebUrl('member.member-relation.chkApply') !!}';

                if (confirm('确定审核通过吗？')) {
                    $.ajax({
                        url: url,
                        type: 'get',
                        data: {id: id},
                        dataType: 'json',
                        success: function (json) {
                            if (1 == json.result) {
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection