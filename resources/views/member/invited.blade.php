@extends('layouts.base')
@section('title', '邀请码记录')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <!-- <div class="w1200 m0a"  style="padding-bottom:80px"> -->
        <div class="rightlist" style="padding-bottom:100px">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('member.member_invited.index')}}">邀请码记录</a></li>
                    <li><a href="javascript:void"> &nbsp; <i class="fa fa-angle-double-right"></i> &nbsp;全部记录</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--<div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                    <form action="" method="get" class="form-horizontal" role="form" id="form1">
                        <input type="hidden" name="c" value="site"/>
                        <input type="hidden" name="a" value="entry"/>
                        <input type="hidden" name="m" value="yun_shop"/>
                        <input type="hidden" name="do" value="member" id="form_do"/>
                        <input type="hidden" name="route" value="member.member_invited.index" id="route"/>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2 ">
                            <!--<label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">ID</label>-->
                            <div class="">
                                <input type="text" placeholder="邀请码" class="form-control" name="search[code]"
                                       @if($search['code'])  value="{{$search['code']}}" @endif/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-2 col-md-2 col-lg-2">
                            <!-- <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>-->
                            <div class="">
                                <input type="text" onkeyup="(this.v=function(){this.value=this.value.replace(/[^0-9-]+/,'');}).call(this)" onblur="this.v();" class="form-control" name="search[mid]"
                                       @if($search['mid'])  value="{{$search['search']['mid']}}" @endif  placeholder="可搜索邀请人id或被邀请人id"/>
                            </div>
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg">
                            <div class="time">
                                <select name='search[searchtime]' class='form-control'>
                                    <option value='0'
                                            @if($search['searchtime']=='0')
                                            selected
                                            @endif>注册时间不限
                                    </option>
                                    <option value='1'
                                            @if($search['searchtime']=='1')
                                            selected
                                            @endif>搜索注册时间
                                    </option>
                                </select>
                            </div>
                            <div class="search-select">

                                    {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[times]', [
                                    'starttime'=>date('Y-m-d H:i'),
                                    'endtime'=>date('Y-m-d H:i'),
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
            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-heading">总数：{{$total}}   </div>
                    <div class="panel-body" style="margin-bottom:200px">
                        <table class="table table-hover" style="overflow:visible">
                            <thead class="navbar-inner">
                            <tr>
                                <th style='width:20%;text-align: center;'>ID</th>
                                <th style='width:20%;text-align: center;'>使用人</th>
                                <th style='width:20%;text-align: center;'>推荐人</th>
                                <th style='width:20%;text-align: center;'>邀请码</th>
                                <th style='width:20%;text-align: center;'>使用时间</th>
                            </tr>
                            </thead>
                            <tbody>
                        @foreach($list['data'] as $row)
                            <tr>
                                <td style="text-align: center; width: 20%;">{{$row['id']}}</td>
                                <td style="text-align: center; width: 20%;">
                                    <!-- {{$row['member_id']}} -->
                                    @if(!empty($row['yz_member']['has_one_member']['avatar']))
                                        <img src='{{$row['yz_member']['has_one_member']['avatar']}}'
                                             style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                                        <br/>
                                    @endif
                                    @if(empty($row['yz_member']['has_one_member']['nickname']))
                                        暂未获取到数据
                                    @else
                                            @if(empty($row['yz_member']['inviter']))
                                                (暂定)
                                            @endif
                                            {{$row['yz_member']['has_one_member']['nickname']}}
                                     @endif
                                </td>

                                <td style="text-align: center; width: 20%;">
                                    @if(!empty($row['has_one_mc_member']['has_one_member']['avatar']))
                                        <img src='{{$row['has_one_mc_member']['has_one_member']['avatar']}}'
                                             style='width:30px;height:30px;padding:1px;border:1px solid #ccc'/>
                                        <br/>
                                    @endif
                                    @if(empty($row['has_one_mc_member']['has_one_member']['nickname']))
                                        暂未获取到数据
                                    @else
                                            @if(empty($row['has_one_mc_member']['inviter']))
                                                (暂定)
                                            @endif
                                            {{$row['has_one_mc_member']['has_one_member']['nickname']}}
                                     @endif
                                </td>
                                <td style="text-align: center; width: 20%;">{{$row['invitation_code']}}</td>
                                <td style="text-align: center; width: 20%;">{{$row['created_at']}}</td>
                            </tr>
                        @endforeach
                            </tbody>
                        </table>
                           {!!$pager!!}
                    </div>
                </div>
            </div>
        </div>
    <!-- </div> -->
    <script type="text/javascript">
        $(function () {
            $('#export').click(function () {
                $('#route').val("member.member_invited.export");
                $('#form1').submit();
                $('#route').val("member.member.index");
            });
        });
    </script>
@endsection

