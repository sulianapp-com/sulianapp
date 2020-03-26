@extends('layouts.base')
@section('title', '余额提现')
@section('content')

    <div class="panel panel-default">
        <div class='panel-heading'>
            提现者信息
        </div>
        <div class='panel-body'>
            <div style='height:auto;width:120px;float:left;'>
                <img src='{{tomedia($item['has_one_member']['avatar'])}}'
                     style='width:100px;height:100px;border:1px solid #ccc;padding:1px'/>
            </div>
            <div style='float:left;height:auto;overflow: hidden'>
                <p>
                    <b>昵称:</b>
                    {{$item['has_one_member']['nickname']}}
                    <b>姓名:</b>
                    {{$item['has_one_member']['realname']}}
                    <b>手机号:</b>
                    {{$item['has_one_member']['mobile']}}
                </p>
                <p>
                    <b>会员等级:</b> {{$item['has_one_member']['yz_member']['group']['group_name']}}
                </p>
                <p>
                    <b>提现金额: </b><span style='color:red'>{{$item['amounts']}}</span> 元
                <p>
                <p>
                    <b>提现类型: </b>{{$item['type_name']}}
                <p>
                <p>
                    <b>提现方式: </b>{{$item['pay_way_name']}}
                </p>
                @if($item['pay_way'] == 'manual')
                    <p>
                        <b>手动打款方式：</b>
                        @if($item['manual_type'] == 1 || empty($item['manual_type']))
                            银行卡
                    </p>
                    <p>
                        <b>姓名：</b>{{$item['bank_card']['member_name']}}
                    </p>
                    <p>
                        <b>开户行：</b>{{$item['bank_card']['bank_name']}}
                    </p>
                    <p>
                        <b>开户省市：</b>{{$item['bank_card']['bank_province']}} | {{$item['bank_card']['bank_city']}}
                    </p>
                    <p>
                        <b>开户支行：</b>{{$item['bank_card']['bank_branch']}}
                    </p>
                    <p>
                        <b>银行卡：</b>{{$item['bank_card']['bank_card']}}
                    </p>

                    </p>
                @elseif($item['manual_type'] == 2)
                    微信
                    </p>
                    <p>
                        <b>微信：</b>{{$item['has_one_member']['yz_member']['wechat']}}
                    </p>
                @elseif($item['manual_type'] == 3)
                    支付宝
                    </p>
                    <p>
                        <b>支付宝：</b>{{$item['has_one_member']['yz_member']['alipay']}}
                    </p>
                @endif
                @endif
                <p>
                    <b>状态: </b>{{$item['status_name']}}
                </p>
                <p>
                    <b>申请时间: </b>{{$item['created_at']}}
                </p>
                @if($item['audit_at'])
                    <p>
                        <b>审核时间: </b>{{date('Y-m-d H:i:s',$item['audit_at'])}}
                    </p>
                @endif
                @if($item['pay_at'])
                    <p>
                        <b>打款时间: </b>{{date('Y-m-d H:i:s',$item['pay_at'])}}
                    </p>
                @endif
                @if($item['arrival_at'])
                    <p>
                        <b>到账时间: </b>{{date('Y-m-d H:i:s',$item['arrival_at'])}}
                    </p>
                @endif

            </div>
        </div>

        <div class='panel-heading'>
            提现申请信息
        </div>
        <form action="{{yzWebUrl("finance.balance-withdraw.examine",['id'=>$item['id']])}}" method='post'
              class='form-horizontal'>
            <div class='panel-body'>
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <td></td>
                        <th>ID</th>
                        <th>提现类型</th>
                        <th>提现金额</th>
                        <th>提现状态</th>
                        <th>提现时间</th>
                    </tr>
                    </thead>
                    <tbody>


                    <tr style="background: #eee">
                        <td>
                            @if($item['status'] == '0' || $item['status'] == '-1')
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="1"
                                           @if($item['status'] == 0 || $item['status'] == 1 )checked="checked"@endif/>
                                    通过
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="-1"
                                           @if($item['status'] == -1 )checked="checked"@endif/> 无效
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="3"
                                           @if($item['status'] == 3 )checked="checked"@endif/> 驳回
                                </label>
                            @endif
                            @if($item['status'] == '1' || $item['status'] == '2')
                                {{$row['pay_status_name']}}
                            @endif

                        </td>
                        <td>{{$item['id']}}</td>
                        <td>{{$item['type_name']}}</td>
                        <td>{{$item['amounts']}}</td>
                        <td>{{$item['status_name']}}</td>
                        <td>{{$item['created_at']}}</td>

                    </tr>


                </table>
            </div>
            <div class='panel-body'>
                打款信息【
                审核金额: <span style='color:red'>{{ $item['amounts'] }}</span> 元
                手续费: <span style='color:red'>{{ $item['actual_poundage'] }}</span> 元
                应打款：<span style='color:red'>{{ $item['actual_amounts'] }}</span>元】

            </div>

            <div class="form-group col-sm-12">
                @if($item['status'] == '0')
                    <input type="submit" name="submit_check" value="提交审核" class="btn btn-primary col-lg-1"
                           onclick='return check()'/>
                @endif

                @if($item['status'] == '1')

                    @if($item['pay_way'] == 'balance')
                        <input type="hidden" name="pay_way" value="3">
                        <input type="submit" name="submit_pay" value="打款到余额" class="btn btn-primary col-lg-1"
                               style='margin-left:10px;' onclick='return '/>
                    @elseif($item['pay_way'] == 'wechat')
                        <input type="hidden" name="pay_way" value="1">
                        <input type="submit" name="submit_pay" value="打款到微信钱包" class="btn btn-primary col-lg-1"
                               style='margin-left:10px;' onclick='return '/>
                    @elseif($item['pay_way'] == 'alipay')
                        <input type="hidden" name="pay_way" value="2">
                        <input type="submit" name="submit_pay" value="打款到支付宝" class="btn btn-primary col-lg-1"
                               style='margin-left:10px;' onclick='return '/>
                    @elseif($item['pay_way'] == 'manual')
                        <input type="hidden" name="pay_way" value="4">
                        <input type="submit" name="submit_pay" value="手动打款" class="btn btn-primary "
                               style='margin-left:10px;' onclick='return '/>
                    @elseif($item['pay_way'] == 'eup_pay')
                        <input type="hidden" name="pay_way" value="5">
                        <input type="submit" name="submit_pay" value="EUP提现" class="btn btn-primary "
                               style='margin-left:10px;' onclick='return '/>
                    @elseif($item['pay_way'] == 'huanxun')
                        <input type="hidden" name="pay_way" value="6">
                        <input type="submit" name="submit_pay" value="打款到银行卡" class="btn btn-primary "
                               style='margin-left:10px;' onclick='return '/>
                    @elseif($item['pay_way'] == 'yop_pay')
                        <input type="hidden" name="pay_way" value="7">
                        <input type="submit" name="submit_pay" value="易宝提现" class="btn btn-primary "
                               style='margin-left:10px;' onclick='return '/>
                    @elseif($item['pay_way'] == 'converge_pay')
                        <input type="hidden" name="pay_way" value="8">
                        <input type="submit" name="submit_pay" value="汇聚提现" class="btn btn-primary "
                               style='margin-left:10px;' onclick='return '/>
                    @endif
                    <input type="submit" name="confirm_pay" value="线下确认打款" class="btn btn-success "
                           style='margin-left:10px;'
                           onclick="{if (confirm('本打款方式需要线下打款，系统只是完成流程!') == true){return true;}return false}"/>
                @endif

                @if($item['status'] == '-1')
                    <input type="submit" name="submit_cancel" value="重新审核" class="btn btn-default col-lg-1"
                           onclick='return '/>
                @endif
                <a class="btn btn-default" href="{{yzWebUrl('withdraw.records')}}" style='margin-left:10px;'>返回列表</a>
            </div>
        </form>
    </div>
@endsection