<style >
    .form-group{overflow:hidden;margin-bottom: 0 !important;}
    .line{margin: 10px;border-bottom:1px solid #ddd}
</style>
<div class="panel panel-default">
    <div class="panel-heading">
        退款申请
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款状态 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">@if ($order['has_one_refund_apply']['is_refunding'])
                        <span class='label label-info'>{{$order['has_one_refund_apply']['status_name']}}</span>
                    @elseif ($order['has_one_refund_apply']['is_refund_fail'])
                        <span class='label label-danger'>{{$order['has_one_refund_apply']['status_name']}}</span>

                    @elseif($order['has_one_refund_apply']['is_refunded'])
                        <span class='label label-success'>{{$order['has_one_refund_apply']['status_name']}}</span>
                    @endif</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">申请时间 :</label>
            <div class="col-sm-9 col-xs-12">

            <p class="form-control-static">{{$order['has_one_refund_apply']['create_time']}}
            </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款类型 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">{{$order['has_one_refund_apply']['refund_type_name']}}</p>
            </div>
        </div>

        @if ($order['has_one_refund_apply']['refund_way_type'] != 2)
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款金额 :</label>
                <div class="col-sm-9 col-xs-12">
                    <p class="form-control-static">{{$order['has_one_refund_apply']['price']}}</p>
                </div>
            </div>
        @endif

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                {{$order['has_one_refund_apply']['refund_way_type_name']}}
                原因 :
            </label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">{{$order['has_one_refund_apply']['reason']}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">
                {{$order['has_one_refund_apply']['refund_way_type_name']}}
                说明 :
            </label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    {!! empty($order['has_one_refund_apply']['content'])?'无':$order['has_one_refund_apply']['content'] !!}</p>
            </div>
        </div>
        @if (!empty($order['has_one_refund_apply']['images']))
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">图片凭证 :</label>
                <div class="col-sm-9 col-xs-12">
                    <p class="form-control-static">
                        @foreach ($order['has_one_refund_apply']['images'] as $k1 => $v1)
                            <a target="_blank" href="{{tomedia($v1)}}"><img
                                        style='width:100px;;padding:1px;border:1px solid #ccc'
                                        src="{{tomedia($v1)}}"></a>
                        @endforeach
                    </p>
                </div>
            </div>
        @endif
        @if ($order['has_one_refund_apply']['is_refunding'])
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label"> </label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    <a class="btn btn-danger btn-sm" href="javascript:;"
                       onclick="$('#modal-refund').find(':input[name=id]').val('{{$order['id']}}')"
                       data-toggle="modal"
                       data-target="#modal-refund">处理{{$order['has_one_refund_apply']['refund_type_name']}}申请</a>
                </p>
            </div>
        </div>
        @endif
        @if ($order['has_one_refund_apply']['status'] == \app\common\models\refund\RefundApply::WAIT_REFUND)
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">退款时间 :</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="form-control-static">
                        {{$order['has_one_refund_apply']['refund_time']}}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">到账时间 :</label>
                <div class="col-sm-9 col-xs-12">
                    <div class="form-control-static" style="color: red">3-15个工作日</div>
                </div>
            </div>
        @endif
        @if (isset($order['has_one_refund_apply']['return_express']))
            <div class="line"></div>
                <div class="form-group">

                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">客户寄出快递信息 </label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static">
                            </br>
                        </div>

                    </div>
                </div>

                @if (!empty($order['has_one_refund_apply']['return_express']['express_company_name']))
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递名称 :</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">{{$order['has_one_refund_apply']['return_express']['express_company_name']}}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递单号 :</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">{{$order['has_one_refund_apply']['return_express']['express_sn']}}
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <button type='button' class='btn btn-default'
                                        onclick='refundexpress_find(this,"{{$order['id']}}",1)'>查看物流
                                </button>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-12 col-sm-3 col-md-2 control-label">填写快递单号时间 :</label>
                        <div class="col-sm-9 col-xs-12">
                            <div class="form-control-static">{{$order['has_one_refund_apply']['return_express']['created_at']}}</div>
                        </div>
                    </div>
                @endif


        @endif

        @if (!empty($order['has_one_refund_apply']['resend_express']))
            <div class="line"></div>
            <div class="form-group">

                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">店家寄出快递信息 </label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static">
                            </br>
                        </div>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递名称 :</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static">
                            {{$order['has_one_refund_apply']['resend_express']['express_company_name']}}
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">快递单号 :</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static">{{$order['has_one_refund_apply']['resend_express']['express_sn']}}
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <button type='button' class='btn btn-default'
                                    onclick='refundexpress_find(this,"{{$order['id']}}",2)'>查看物流
                            </button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 control-label">确认发货时间 :</label>
                    <div class="col-sm-9 col-xs-12">
                        <div class="form-control-static">{{$order['has_one_refund_apply']['resend_express']['created_at']}}</div>
                    </div>
                </div>
        @endif

    </div>
</div>