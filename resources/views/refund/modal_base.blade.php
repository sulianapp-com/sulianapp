<div id="modal-refund" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"
     style="width:920px;margin:0px auto;">
    <form class="form-horizontal form" id="form-refund" action="" method="post" enctype="multipart/form-data">
        <input type='hidden' name='refund_id' value='{{$order['has_one_refund_apply']['id']}}'/>
        <div class="modal-dialog" style="width:920px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>处理{{$order['has_one_refund_apply']['refund_type_name']}}申请</h3></div>
                <div class="modal-body">


                    <div class="form-group">
                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">处理方式</label>
                        <div class="col-sm-9 col-xs-12">
                            <p class="form-control-static">{{$order['has_one_refund_apply']['refund_type_name']}}</p>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">处理结果</label>
                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">


                            @if($order['has_one_refund_apply']['status'] < 4)
                                <label class='radio-inline' style="float: left;margin-left: 0px;margin-right: 10px;">
                                    <input type='radio' class="refund-action"
                                           data-action="{{yzWebUrl('refund.operation.reject')}}" value="-1"
                                           name='refund_status' checked>驳回申请
                                </label>
                            @endif
                            @section('operation_pass')@show

                            @section('operation_consensus')@show

                            @section('operation_resend')@show


                        </div>
                    </div>
                    @if($order['has_one_refund_apply']['refund_type'] > 0)
                        <div class="form-group refund-group" style="display: none;">
                            <label class="col-xs-10 col-sm-3 col-md-3 control-label">退货地址</label>
                            <div class="col-sm-9 col-xs-12">
                                <select class="form-control tpl-category-parent" id="raid" name="raid"
                                        style="width: 200px;">
                                    <option value="0">默认地址</option>
                                    {{--{loop $refund_address $refund_address_item}--}}
                                    {{--<option value="{$refund_address_item['id']}" {if $refund[--}}
                                    {{--'refundaddressid'] ==--}}
                                    {{--$refund_address_item['id']}selected="true"{/if}>{$refund_address_item['title']}</option>--}}
                                    {{--{/loop}--}}
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="form-group refund-group" style="display: none;">
                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">留言</label>
                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                            <textarea class="form-control"
                                      name="message">{{$order['has_one_refund_apply']['message']}}</textarea>
                        </div>
                    </div>

                    <div class="form-group refuse-group" style="display: none;">
                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">驳回原因</label>
                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                            <textarea class="form-control" name="reject_reason"></textarea>
                        </div>
                    </div>

                    <div class="form-group express-group"
                         @if($order['has_one_refund_apply']['status'] != 5)style="display: none;" @endif>
                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">快递公司</label>
                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                            <select class="form-control" name="express_code" id="resend_express_code">
                                <option value="" data-name="">其他快递</option>
                                @include('express.companies')
                            </select>
                            <input type='hidden' name='express_company_name' id='resend_express_company_name' value="{{$order['has_one_refund_apply']['resend_express']['express_code']}}"/>
                        </div>
                    </div>
                    <div class="form-group express-group"
                         @if($order['has_one_refund_apply']['status'] < 5)style="display: none;" @endif>
                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">快递单号</label>
                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                            <input type="text" name="express_sn" class="form-control"
                                   value="{{$order['has_one_refund_apply']['resend_express']['express_sn']}}"/>
                        </div>
                    </div>

                    <div id="module-menus"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary span2 " id="refund_submit" name="refund" value="yes">
                        确认
                    </button>
                    <a href="#" class="btn" data-dismiss="modal" aria-hidden="true">关闭</a></div>
            </div>
        </div>
    </form>
</div>
<script>
    $('#form-refund').submit(function () {
        var route = $('input[name="refund_status"]:checked').attr('data-action');
        $(this).attr('action', route);

        return true;
    });

    $.each($(":radio[name=refund_status]"),function() {
        var refund_status_radio = $(this).val();
        var flag = $(this)[0].checked;
        if (refund_status_radio == -1 && flag) {//显示驳回
            $(".refuse-group").show();
            $(".refund-group").hide();
            $(".express-group").hide();
            $(".help-group").hide();
        } else if (refund_status_radio == 1 && flag) {//显示帮助
            $(".refuse-group").hide();
            $(".refund-group").hide();
            $(".express-group").hide();
            $(".help-group").show();
        } else if (refund_status_radio == 3 && flag) {//显示退款
            $(".refuse-group").hide();
            $(".refund-group").show();
            $(".express-group").hide();
            $(".help-group").hide();
        } else if (refund_status_radio == 5 && flag) {//显示快递
            $(".refuse-group").hide();
            $(".refund-group").hide();
            $(".express-group").show();
            $(".help-group").hide();
        }
    });

    $(function () {
        $(":radio[name=refund_status]").change(function () {
            var refund_status = $(this).val();

            if (refund_status == -1) {//显示驳回
                $(".refuse-group").show();
                $(".refund-group").hide();
                $(".express-group").hide();
                $(".help-group").hide();
            } else if (refund_status == 1) {//显示帮助
                $(".refuse-group").hide();
                $(".refund-group").hide();
                $(".express-group").hide();
                $(".help-group").show();
            } else if (refund_status == 3) {//显示退款
                $(".refuse-group").hide();
                $(".refund-group").show();
                $(".express-group").hide();
                $(".help-group").hide();
            } else if (refund_status == 5) {//显示快递
                $(".refuse-group").hide();
                $(".refund-group").hide();
                $(".express-group").show();
                $(".help-group").hide();
            } else {//全部隐藏
                $(".refuse-group").hide();
                $(".refund-group").hide();
                $(".express-group").hide();
                $(".help-group").hide();
            }


        });


        $("#express_company").change(function () {
            var obj = $(this);
            var sel = obj.find("option:selected").attr("data-name");
            $("#express_company_name").val(sel);
        });

        $("#resend_express_code").change(function () {
            var obj = $(this);
            var sel = obj.find("option:selected").attr("data-name");
            $("#resend_express_company_name").val(sel);
        });
    });
</script>