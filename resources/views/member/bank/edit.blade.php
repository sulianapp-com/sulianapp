@extends('layouts.base')
@section('title', '会员管理')
@section('content')
    <link href="{{static_url('yunshop/css/member.css')}}" media="all" rel="stylesheet" type="text/css"/>
    <div class="w1200 m0a">
        <div class="rightlist">
            <!-- 新增加右侧顶部三级菜单 -->
            <div class="right-titpos">
                <ul class="add-snav">
                    <li class="active"><a href="{{yzWebUrl('member.member.index')}}">会员管理</a></li>
                    <li><a href="#">&nbsp;<i class="fa fa-angle-double-right"></i> &nbsp;银行卡管理</a></li>
                </ul>
            </div>
            <!-- 新增加右侧顶部三级菜单结束 -->
            <form action="{{yzWebUrl('member.bank-card.edit', ['member_id'=> $member->uid])}}" method='post' class='form-horizontal'>
                <div class='panel panel-default'>
                    <div class='panel-body'>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">粉丝</label>
                            <div class="col-sm-9 col-xs-12">
                                <img src='{{ $member->avatar }}'
                                     style='width:100px;height:100px;padding:1px;border:1px solid #ccc'/>
                                {{ $member->nickname }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="bank[member_name]" class="form-control"
                                       value="{{ $member->bankCard->member_name or ''}}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开户行</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="bank[bank_name]" class="form-control"
                                       value="{{ $member->bankCard->bank_name or '' }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开户行省份</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="bank[bank_province]" class="form-control"
                                       value="{{ $member->bankCard->bank_province or '' }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开户城市</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="bank[bank_city]" class="form-control"
                                       value="{{ $member->bankCard->bank_city or '' }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">开户支行</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="bank[bank_branch]" class="form-control"
                                       value="{{ $member->bankCard->bank_branch or '' }}"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">银行卡号</label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="text" name="bank[bank_card]" class="form-control"
                                       value="{{ $member->bankCard->bank_card or '' }}"/>
                            </div>
                        </div>

                    </div>



                    <div class='panel-body'>
                        <div class="form-group">
                            <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                            <div class="col-sm-9 col-xs-12">
                                <input type="submit" name="submit" value="更新" class="btn btn-success" onclick="return confirm('确定更新会员银行卡信息吗？');" />
                                <input type="hidden" name="token" value="{{$var['token']}}"/>
                                <button class="btn btn-default">
                                    <a href="{{ yzWebUrl('member.member.index') }}">返回</a>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection
