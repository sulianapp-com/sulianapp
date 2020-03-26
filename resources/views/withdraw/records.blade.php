@extends('layouts.base')

@section('content')
@section('title', trans('提现列表'))
<style>
    .loadEffect{
        display:none;
        width: 100px;
        height: 100px;
        position: absolute;
        left: 50%;
        margin: 0 auto;
        margin-top:100px;
    }
    .loadEffect span{
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: lightgreen;
        position: absolute;
        -webkit-animation: load 1.04s ease infinite;
    }
    @-webkit-keyframes load{
        0%{
            opacity: 1;
        }
        100%{
            opacity: 0.2;
        }
    }
    .loadEffect span:nth-child(1){
        left: 0;
        top: 50%;
        margin-top:-8px;
        -webkit-animation-delay:0.13s;
    }
    .loadEffect span:nth-child(2){
        left: 14px;
        top: 14px;
        -webkit-animation-delay:0.26s;
    }
    .loadEffect span:nth-child(3){
        left: 50%;
        top: 0;
        margin-left: -8px;
        -webkit-animation-delay:0.39s;
    }
    .loadEffect span:nth-child(4){
        top: 14px;
        right:14px;
        -webkit-animation-delay:0.52s;
    }
    .loadEffect span:nth-child(5){
        right: 0;
        top: 50%;
        margin-top:-8px;
        -webkit-animation-delay:0.65s;
    }
    .loadEffect span:nth-child(6){
        right: 14px;
        bottom:14px;
        -webkit-animation-delay:0.78s;
    }
    .loadEffect span:nth-child(7){
        bottom: 0;
        left: 50%;
        margin-left: -8px;
        -webkit-animation-delay:0.91s;
    }
    .loadEffect span:nth-child(8){
        bottom: 14px;
        left: 14px;
        -webkit-animation-delay:1.04s;
    }

</style>
<div class="right-titpos">
    @include('layouts.tabs')
</div>

{{--<form action="" method="post" class="form-horizontal">--}}
<form action="" method="get" class="form-horizontal" id="form1">
    <input type="hidden" name="c" value="site" />
    <input type="hidden" name="a" value="entry" />
    <input type="hidden" name="m" value="yun_shop" />
    <input type="hidden" name="do" value="5201" />
    <input type="hidden" name="export" value="0" id="isExport"/>
    <input type="hidden" name="route" value="{{ \YunShop::request()->route }}" id="route" />
    <div class="panel panel-info">
        <div class="panel-body">
            <input type="hidden" name="search[status]" value="{{$search['status']}}">

            <div class="form-group col-xs-12 col-sm-8 col-lg-11">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员ID</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <input class="form-control" name="search[member_id]" id="" type="text"
                           value="{{$search['member_id']}}" placeholder="会员ID">
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-8 col-lg-11">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">会员信息</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <input class="form-control" name="search[member]" id="" type="text"
                           value="{{$search['member']}}" placeholder="昵称/姓名/手机">
                </div>
            </div>

            <div class="form-group col-xs-12 col-sm-8 col-lg-11">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现编号</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <input class="form-control" name="search[withdraw_sn]" id="" type="text"
                           value="{{$search['withdraw_sn']}}" placeholder="提现编号">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">收入类型：</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <select name='search[type]' class='form-control'>
                        <option value='' @if($search['type']=='') selected @endif>全部</option>
                        @foreach($types as $type)
                            <option value='{{$type['class']}}'
                                    @if($search['type']==$type['class']) selected @endif>{{$type['title']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现方式：</label>
                <div class="col-xs-12 col-sm-8 col-lg-9">
                    <select name='search[pay_way]' class='form-control'>
                        <option value='' @if(empty($search['pay_way'])) selected @endif>不搜索</option>
                        <option value='wechat' @if($search['pay_way']=='wechat') selected @endif >提现到微信</option>
                        <option value='alipay' @if($search['pay_way']=='alipay') selected @endif >提现到支付宝</option>
                        <option value='balance' @if($search['pay_way']=='balance') selected @endif >提现到余额</option>

                        @if(app('plugins')->isEnabled('eup_pay'))
                            <option value='eup_pay' @if($search['pay_way']=='eup_pay') selected @endif >提现到EUP</option>
                        @endif
                        @if(app('plugins')->isEnabled('huanxun'))
                            <option value='huanxun' @if($search['pay_way']=='huanxun') selected @endif >提现到银行卡</option>
                        @endif
                        
                        <option value='manual' @if($search['pay_way']=='manual') selected @endif >提现到手动打款</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">提现时间：</label>
                <div class="col-sm-2">
                    <select name='search[searchtime]' class='form-control'>
                        <option value='' @if(empty($search['searchtime'])) selected @endif>不搜索</option>
                        <option value='1' @if($search['searchtime']==1) selected @endif >搜索</option>
                    </select>
                </div>
                <div class="col-sm-7 col-lg-7 col-xs-12">
                    {!! app\common\helpers\DateRange::tplFormFieldDateRange('search[time]', [
                        'starttime'=>date('Y-m-d H:i', strtotime($search['time']['start']) ?: strtotime('-1 month')),
                        'endtime'=>date('Y-m-d H:i',strtotime($search['time']['end']) ?: time()),
                        'start'=>0,
                        'end'=>0
                    ], true) !!}
                </div>

            </div>

            <div class="form-group col-xs-12 col-sm-4">
                <div style="width: 110px;margin: auto;">
                    <input type="button" class="btn btn-success" id="export" value="导出">
                    <input type="button" class="btn btn-success pull-right" id="search" value="搜索">
                </div>

            </div>

        </div>
    </div>
</form>

@if (1 == YunShop::request()->search['status'])
    <div class='panel panel-default'>
        <div class="loadEffect"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
        <div class='panel-body' style="padding-bottom: 15px">
            <input type="hidden" name="pay_way" value="2">
            <input type="button" class="btn btn-success" id="batch_alipay" value="支付宝批量打款">
            <label style="color:#ff0000;">批量打款所选的订单收入类型必须保持一致，收入类型分为余额提现和收入提现</label>
        </div>

        <div class='panel-body' style="padding-bottom: 15px">
            <input type="hidden" name="pay_way" value="2">
            <input type="submit" class="btn btn-success batch_withdraw_alipay" value="支付宝余额提现一键打款" data-type="1">
            <input type="submit" class="btn btn-success batch_withdraw_alipay" value="支付宝收入提现一键打款" data-type="2">
            <label style="color:#ff0000;">一键打款每次最大批次数上限为800</label>
        </div>


    </div>
@endif
<div class='panel panel-default'>
    <div class='panel-body'>
        <table class="table">
            <thead>
            <tr>
                <th style='width:8%;'><input id="all" type="checkbox" value="0"> 全选</th>
                <th style='width:20%;'>提现编号</th>
                <th style='width:10%;'>粉丝</th>
                <th style='width:10%;'>姓名</br>手机</th>
                <th style='width:10%;'>收入类型</th>
                <th style='width:10%;'>提现方式</th>
                <th style='width:10%;'>申请金额</th>
                <th style='width:15%;'>申请时间</th>
                <th style='width:10%;'>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($records as $row)
                <tr>
                    <td><input type="checkbox" name="chk_withdraw" value="{{$row->id}}"></td>
                    <td title="{{$row->withdraw_sn}}" class="tip">{{$row->withdraw_sn}}</td>
                    <td><img src="{{tomedia($row->hasOneMember['avatar'])}}"
                             style="width: 30px; height: 30px;border:1px solid #ccc;padding:1px;">
                        <br/>
                        {{$row->hasOneMember['nickname']}}</td>
                    <td>{{$row->hasOneMember['realname']}}<br/>{{$row->hasOneMember['mobile']}}</td>
                    <td>{{$row->type_name}}</td>
                    <td>{{$row->pay_way_name}}</td>
                    <td>{{$row->amounts}}</td>
                    <td>{{$row->created_at}}</td>
                    <td>
                        @if($row->type == 'balance')
                            <a class='btn btn-default'
                               href="{{yzWebUrl('finance.balance-withdraw.detail', ['id' => $row->id])}}">详情</a>
                        @else
                            <a class='btn btn-default'
                               href="{{yzWebUrl('withdraw.detail.index', ['id' => $row->id])}}">详情</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $page !!}

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">支付宝提现</h4>
            </div>
            <div class="modal-body">
                提现打款是否成功？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">失败</button>
                <button type="button" class="btn btn-primary withdraw_success">成功</button>
            </div>
        </div>
    </div>
</div>

<script language='javascript'>
    $(function () {
        $('#export').click(function () {
            $('#isExport').val("1");
            $('#form1').submit();
            $('#isExport').val("0");
        });
        $('#search').click(function () {
            $('#form1').submit();
        });

        $('#all').change(function() {
            $(this).parents('.table').find('input[type="checkbox"]').prop('checked',$(this).prop('checked'));
        });

        //批量打款
        $(document).on('click', '#batch_alipay', function () {
            var total    = 0;
            var balance  = 0;
            var error    = 0;
            var ids     = []

            $(document).find('.batch_alipay').remove();

            $('input[type="checkbox"]').each(function() {
                if ($(this).prop('checked') && $(this).val() != 0){
                    total++;
                    ids.push($(this).val());

                    if ($(this).parent().siblings().eq(4).text() != '提现到支付宝') {
                        error++;
                    }

                    if ($(this).parent().siblings().eq(3).text() == '余额提现') {
                        balance++;
                    }
                }
            });

            if (error > 0) {
                alert('提现方式错误');
                return false;
            }

            if (balance == 0 || balance == total) {
                var myform = $('<form class="batch_alipay" method="post" target="_blank"><input type="hidden" name="ids" value="' + ids + '" /></form>');

                $(document.body).append(myform);

                if (balance == 0) {  //收入提现
                    myform.attr('action', '{!! yzWebUrl("finance.withdraw.batchAlipay") !!}');
                } else { //余额提现
                    myform.attr('action', '{!! yzWebUrl("finance.balance-withdraw.batchAlipay") !!}');
                }

                myform.submit();
            } else {
                alert('订单收入类型不一致');
                return false;
            }
        });

        //一键打款
        $(document).on('click', '.batch_withdraw_alipay', function () {
            $(document).find('.batch_alipay_form').remove();

            var _that  = this;
            var myform = '';
            var action = '';
            var withdraw_type = $(this).data('type');

            switch (withdraw_type) {
                case 1:
                    action = '{!! yzWebUrl("finance.balance-withdraw.batchAlipay") !!}';
                    break;
                case 2:
                    action = '{!! yzWebUrl("finance.withdraw.batchAlipay") !!}';
                    break;
            }

            $.ajax({
                url: '{!! yzWebUrl('finance.withdraw.getAllWithdraw') !!}',
                type: 'POST',
                data: {type:withdraw_type},
                dataType: 'json',
                beforeSend: function () {
                    $('.loadEffect').show();
                    $(_that).attr('disabled',"true");
                }
            }).done(function (json) {
                if (json.status == 0 || json.status == -1) {
                    //alert(json.msg)

                    return false;
                }

                if (json.status == 1) {
                    myform = $('<form class="batch_alipay_form" method="post" target="_blank"><input type="hidden" name="ids" value="' + json.ids + '" /></form>');

                    $(myform).attr('action', action);
                    $(document.body).append(myform);

                    $(myform).submit();
                }
            }).fail(function (message) {
                console.log('fail:', message)
            }).always(function () {
                $('.loadEffect').hide();
                $('#myModal').modal();
                $(_that).removeAttr('disabled');
            });
        });

        $('.withdraw_success').click(function () {
            var process_id = $(document).find('.batch_alipay_form input[name="ids"]').val();

            if (typeof(process_id) != 'undefined' || process_id != '' || process_id != 0) {
                $('#myModal').modal('hide');

                $.ajax({
                    url: '{!! yzWebUrl('finance.withdraw.updateWidthdrawOrderStatus') !!}',
                    type: 'get',
                    data: {ids:process_id},
                    dataType: 'json',
                    beforeSend: function () {
                        $('.loadEffect').show();
                    }
                }).done(function (json) {
                    if (json.status == 1) {
                        //  alert('更新订单状态成功');
                        location.reload();
                    } else {
                        alert('更新订单状态失败');
                    }
                }).fail(function (message) {
                    console.log(message);
                }).always(function () {
                    $('.loadEffect').hide();
                })
            }
        });
    });
</script>
@endsection
