@extends('layouts.base')
@section('title', '关系链升级')
@section('content')

    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist" style="padding-bottom:100px">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('member.member.index')}}">会员管理</a></li>
                    <li></li> <i class="fa fa-angle-double-right"></i> &nbsp;关系链升级</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <div class="panel panel-info"><!--
                <div class="panel-heading">筛选</div>-->
                <div class="panel-body">
                </div>
            </div>
            <div class="clearfix">
                <div class="panel panel-default">
                    <div class="panel-body" style="margin-bottom:200px">
                     数据正在导入中,如会员数据过多请耐心等待...
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script language='javascript'>

@endsection