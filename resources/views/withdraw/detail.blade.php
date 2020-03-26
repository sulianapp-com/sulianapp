@extends('layouts.base')

@section('content')
@section('title', trans('提现详情'))
<div class="panel panel-default">
    <div class='panel-heading'>
        提现者信息
    </div>
    <div class='panel-body'>
        <div style='height:auto;width:120px;float:left;'>
            <img src='{{tomedia($item->hasOneMember->avatar)}}'
                 style='width:100px;height:100px;border:1px solid #ccc;padding:1px'/>
        </div>
        <div style='float:left;height:auto;overflow: hidden'>
            <p>
                <b>昵称:</b>
                {{$item->hasOneMember->nickname}}
                <b>姓名:</b>
                {{$item->hasOneMember->realname}}
                <b>手机号:</b>
                {{$item->hasOneMember->mobile}}
            </p>
            <p>
                <b>累计收入: </b><span style='color:red'>{{$item->hasOneAgent->commission_total}}</span> 元
            </p>
            <p>
                <b>提现金额: </b><span style='color:red'>{{$item->amounts}}</span> 元
            <p>
            <p>
                <b>收入类型: </b>{{$item->type_name}}
            <p>
            <p>
                <b>提现方式: </b>{{$item->pay_way_name}}
            </p>
            @if($item->pay_way == 'manual')
                <p>
                    <b>手动打款方式：</b>
                    @if($item->manual_type == 1 || empty($item->manual_type))
                        银行卡
                </p>
                <p>
                    <b>姓名：</b>{{$item->bankCard->member_name}}
                </p>
                <p>
                    <b>开户行：</b>{{$item->bankCard->bank_name}}
                </p>
                <p>
                    <b>开户省市：</b>{{$item->bankCard->bank_province}} | {{$item->bankCard->bank_city}}
                </p>
                <p>
                    <b>开户支行：</b>{{$item->bankCard->bank_branch}}
                </p>
                <p>
                    <b>银行卡：</b>{{$item->bankCard->bank_card}}
                </p>
            @elseif($item->manual_type == 2)
                微信
                </p>
                <p>
                    <b>微信：</b>{{$item->hasOneYzMember->wechat}}
                </p>
            @elseif($item->manual_type == 3)
                支付宝
                </p>
                <p>
                    <b>支付宝：</b>{{$item->hasOneYzMember->alipay}}
                </p>
            @endif
            @endif
            <p>
                <b>状态: </b>{{$item->status_name}}
            </p>
            <p>
                <b>申请时间: </b>{{$item->created_at}}
            </p>
            @if($item->audit_at)
                <p>
                    <b>审核时间: </b>{{date('Y-m-d H:i:s',$item->audit_at)}}
                </p>
            @endif
            @if($item->pay_at)
                <p>
                    <b>打款时间: </b>{{date('Y-m-d H:i:s',$item->pay_at)}}
                </p>
            @endif
            @if($item->arrival_at)
                <p>
                    <b>到账时间: </b>{{date('Y-m-d H:i:s',$item->arrival_at)}}
                </p>
            @endif

        </div>
    </div>

    <div class='panel-heading'>
        收入提现申请信息 共计 <span style="color:red; ">{{$item->type_data['income_total']}}</span> 条收入
    </div>
    <form action="{{yzWebUrl("finance.withdraw.dealt",['id'=>$item->id])}}" method='post' class='form-horizontal'>
        <div class='panel-body'>
            <table class="table table-hover">
                <thead class="navbar-inner">
                <tr>
                    <td style="width: 200px;"></td>
                    <th>收入ID</th>
                    <th>收入类型</th>
                    <th>收入金额</th>
                    <th>收入状态</th>
                    <th>打款状态</th>
                    <th>收入时间</th>
                    <td>收入详情</td>
                </tr>
                </thead>
                <tbody>
                @foreach($item->type_data['incomes'] as $k=>$row)
                    <tr style="background: #eee">
                        <td>
                            @if($item->status == '0' || $item->status == '-1')
                                <label class="radio-inline">
                                    <input type="radio" name="audit[{{$row['id']}}]" value="1" checked="checked"/>
                                    通过
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="audit[{{$row['id']}}]" value="-1"/> 无效
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="audit[{{$row['id']}}]" value="3"/> 驳回
                                </label>
                            @endif
                            @if($item->status == '1' || $item->status == '2')
                                {{$row['pay_status_name']}}
                            @endif

                        </td>
                        <td>{{$row['id']}}</td>
                        <td>{{$row['type_name']}}</td>
                        <td>{{$row['amount']}}</td>
                        <td>{{$row['status_name']}}</td>
                        <td>{{$row['pay_status_name']}}</td>
                        <td>{{$row['created_at']}}</td>
                        <td>
                            <a class="btn btn-danger btn-sm" href="javascript:;" data-toggle="modal"
                               data-target="#modal-refund{{$k}}">详情</a>
                        </td>
                    </tr>

                    <div id="modal-refund{{$k}}" class="modal fade" tabindex="-1" role="dialog"
                         style="width:600px;margin:0px auto;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×
                                    </button>
                                    <h3>收入信息</h3>

                                    @foreach(json_decode($row['detail'],true) as $data)
                                        <div class="form-group">{{$data['title']}}</div>
                                        @foreach($data['data'] as $value)

                                            @if(!isset($value['title']))
                                                @foreach($value as $v)
                                                    <div class="modal-body" style="background: #eee">
                                                        <div class="form-group">
                                                            <label class="col-xs-10 col-sm-3 col-md-3 control-label">{{$v['title']}}</label>
                                                            <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                                                {{$v['value']}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="modal-body" style="background: #eee">
                                                    <div class="form-group">
                                                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">{{$value['title']}}</label>
                                                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                                                            @if($value['title'] === '订单号')
                                                                {{$value['value']}}
                                                                <a target="_blank"
                                                                   href="{{yzWebUrl('order.list',['search'=>['ambiguous'=>['field'=>'order','string'=>$value['value']]]])}}">订单详情</a>
                                                            @else
                                                                {{$value['value']}}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                        @endforeach
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach
            </table>
        </div>
        <div class='panel-heading'>
            打款信息
        </div>
        @if($item->status == '0')
            <div class='panel-body'>
                审核金额: <span style='color:red'>{{ $item->amounts }}</span> 元
                预计手续费: <span style='color:red'>{{ $item->poundage }}</span> 元
                预计劳务税:<span style='color:red'>{{ $item->servicetax }}</span> 元
                预计应打款：<span style='color:red'>{{ $item->amounts - $item->poundage - $item->servicetax }}</span>元
            </div>

        @else
            <div class='panel-body'>
                审核金额: <span style='color:red'>{{$item->actual_amounts + $item->actual_poundage + $item->actual_servicetax}}</span>元
                手续费: <span style='color:red'>{{$item->actual_poundage}}</span> 元
                劳务税:<span style='color:red'>{{$item->actual_servicetax}}</span> 元
                应打款：<span style='color:red'>{{$item->actual_amounts}}</span>元
            </div>
        @endif
        <div class="form-group col-sm-12">
            @if($item->status == '0')
                <input type="submit" name="submit_check" value="提交审核" class="btn btn-primary col-lg-1"
                       onclick='return check()'/>
            @endif
·
            @if($item->status == '1')

                @if($item->pay_way == 'balance')
                    <input type="submit" name="submit_pay" value="打款到余额" class="btn btn-primary col-lg-1" style='margin-left:10px;' onclick='return '/>
                @elseif($item->pay_way == 'wechat')
                    <input type="submit" name="submit_pay" value="打款到微信钱包" class="btn btn-primary col-lg-1" style='margin-left:10px;' onclick='return '/>
                @elseif($item->pay_way == 'alipay')
                    <input type="submit" name="submit_pay" value="打款到支付宝" class="btn btn-primary " style='margin-left:10px;' onclick='return '/>
                @elseif($item->pay_way == 'huanxun')
                    <input type="submit" name="submit_pay" value="打款到银行卡" class="btn btn-primary " style='margin-left:10px;' onclick='return '/>
                @elseif($item->pay_way == 'eup_pay')
                    <input type="submit" name="submit_pay" value="EUP打款" class="btn btn-primary " style='margin-left:10px;' onclick='return '/>
                @elseif($item->pay_way == 'yop_pay')
                    <input type="submit" name="submit_pay" value="易宝打款" class="btn btn-primary " style='margin-left:10px;' onclick='return '/>
                @elseif($item->pay_way == 'manual')
                    <input type="submit" name="submit_pay" value="手动打款" class="btn btn-primary " style='margin-left:10px;' onclick='return '/>
                @elseif($item->pay_way == 'converge_pay')
                    <input type="submit" name="submit_pay" value="打款到汇聚" class="btn btn-primary " style='margin-left:10px;' onclick='return '/>
                @endif
            @endif

            @if($item->status == '4')
                <input type="submit" name="again_pay" value="重新打款" class="btn btn-warning " style='margin-left:10px;' onclick='return '/>
            @endif

            @if($item->status == '1' || $item->status == '4')
                <input type="submit" name="confirm_pay" value="线下确认打款" class="btn btn-success " style='margin-left:10px;' onclick="{if (confirm('本打款方式需要线下打款，系统只是完成流程!') == true){return true;}return false}"/>
                <input type="submit" name="audited_rebut" value="驳回记录" class="btn btn-danger " style='margin-left:10px;' onclick="{if (confirm('驳回后，需要会员重新申请提现（仅驳回审核通过提现）') == true){return true;}return false}"/>
            @endif

            @if($item->status == '-1')
                <input type="submit" name="submit_cancel" value="重新审核" class="btn btn-default " onclick='return '/>
            @endif
                <a class="btn btn-default" href="{{yzWebUrl('withdraw.records')}}" style='margin-left:10px;'>返回列表</a>
        </div>
    </form>

</div>

@endsection
