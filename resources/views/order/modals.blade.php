<style>
    .bootstrap-select{width:0;padding:0;margin:0;}
    .dropdown-toggle .pull-left{margin:0;line-height: 20px;height:20px;}
    .main .form-horizontal .form-group {
        margin-bottom: 15px !important;
    }
</style>
<!-- 关闭订单 -->
<div id="modal-close" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:0px auto;">
    <form class="form-horizontal form" action="{!! yzWebUrl('order.operation.close') !!}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="route" value="order.operation.close">
        <input type='hidden' name='order_id' value=''/>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>关闭订单</h3>
                </div>
                <div class="modal-body">
                    <label>关闭订单原因</label>
                    <textarea style="height:150px;" class="form-control" name="reson" autocomplete="off"></textarea>
                    <div id="module-menus"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="close" value="yes">关闭订单</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- 手动退款 -->
<div id="modal-manual-refund" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:0px auto;">
    <form class="form-horizontal form" action="{!! yzWebUrl('order.operation.manualRefund') !!}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="route" value="order.operation.manualRefund">
        <input type='hidden' name='order_id' value=''/>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>退款并关闭订单</h3>
                </div>
                <div class="modal-body">
                    <label>退款原因</label>
                    <textarea style="height:150px;" class="form-control" name="reson" autocomplete="off"></textarea>
                    <div id="module-menus"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="close" value="yes">退款</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </form>
</div>


<!-- 确认发货 -->
<div id="modal-confirmsend" class="modal fade" role="dialog" style="width:600px;margin:0px auto;">
    <form class="form-horizontal form" action="" method="get"
          enctype="multipart/form-data">
        <input type='hidden' name='c' value='site'/>
        <input type='hidden' name='a' value='entry'/>
        <input type='hidden' name='m' value='yun_shop'/>
        <input type='hidden' name='do' value='{{YunShop::request()->do}}'/>
        <input type='hidden' name='order_id' value=''/>
        <input type='hidden' name='route' value='order.operation.send' id="send_form"/>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>快递信息</h3>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">收件人信息</label>
                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                            <div class="form-control-static">
                                收 件 人: <span class="realname">{{$order['belongs_to_member']['realname']}}</span> / <span class="mobile">{{$order['belongs_to_member']['mobile']}}</span><br>
                                收货地址: <span class="address"></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="overflow: visible !important;">
                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">快递公司</label>
                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                            <select class="form-control selectpicker" data-live-search="true" name="express_code" id="express_company" >
                                <option value="" data-name="">其他快递</option>

                                @include('express.companies')
                            </select>
                            <input type='hidden' name='express_company_name' id='expresscom'/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-xs-10 col-sm-3 col-md-3 control-label">快递单号</label>
                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                            <input type="text" id="express_sn" name="express_sn" class="form-control" style="margin:0;width:100%;"/>
                        </div>
                    </div>
                    <div id="module-menus"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary span2" name="confirmsend" onclick="confirmSend()"
                            value="yes">确认发货
                    </button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- 取消发货 -->
<div id="modal-cancelsend" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"
     style="width:600px;margin:0px auto;">
    <form class="form-horizontal form" action="{!! yzWebUrl('order.operation.cancel-send') !!}" method="post"
          enctype="multipart/form-data">
        <input type='hidden' name='order_id' value=''/>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>取消发货</h3>
                </div>
                <div class="modal-body">
                    <label>取消发货原因</label>
                    <textarea style="height:150px;" class="form-control" name="cancelreson"
                              autocomplete="off"></textarea>
                    <div id="module-menus"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary span2" name="cancelsend" value="yes">取消发货</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </form>
</div>


</form>
</div>
<div id='changeprice_container'>

</div>

@include('refund.modal')

<script language='javascript'>


    function changePrice(orderid) {
        $.post("{!! yzWebUrl('order.change-order-price') !!}", {order_id: orderid}, function (html) {
            if (html == -1) {
                alert('订单不能改价!');
                return;
            }
            $('#changeprice_container').html(html);
            $('#modal-changeprice').modal().on('shown.bs.modal', function () {
                mc_init();
            })
        });
    }
    var order_price = 0;
    var dispatch_price = 0;
    function mc_init() {
        order_price = parseFloat($('#changeprice-orderprice').val());
        dispatch_price = parseFloat($('#changeprice-dispatchprice').val());
        $('input', $('#modal-changeprice')).blur(function () {
            if ($.isNumber($(this).val())) {
                mc_calc();
            }
        });

    }

    function mc_calc() {

        var change_dispatchprice = parseFloat($('#changeprice_dispatchprice').val());
        if (!$.isNumber($('#changeprice_dispatchprice').val())) {
            change_dispatchprice = dispatch_price;
        }
        var dprice = change_dispatchprice;
        if (dprice <= 0) {
            dprice = 0;
        }
        $('#dispatchprice').html(dprice.toFixed(2));

        var oprice = 0;
        $('.changeprice_orderprice').each(function () {
            var p = 0;
            if ($.trim($(this).val()) != '') {
                p = parseFloat($.trim($(this).val()));
            }
            oprice += p;
        });
        if (Math.abs(oprice) > 0) {
            if (oprice < 0) {
                $('#changeprice').css('color', 'red');
                $('#changeprice').html(" - " + Math.abs(oprice));
            } else {
                $('#changeprice').css('color', 'green');
                $('#changeprice').html(" + " + Math.abs(oprice));
            }
        }
        var lastprice = order_price + dprice + oprice;

        $('#lastprice').html(lastprice.toFixed(2));

    }
    function mc_check() {
        var can = true;
        var lastprice = 0;
        $('.changeprice').each(function () {
            if ($.trim($(this).val()) == '') {
                return true;
            }
            var p = 0;
            if (!$.isNumber($(this).val())) {
                $(this).select();
                alert('请输入数字!');
                can = false;
                return false;
            }
            var val = parseFloat($(this).val());
            if (val <= 0 && Math.abs(val) > parseFloat($(this).parent().prev().html())) {
                $(this).select();
                alert('单个商品价格不能优惠到负数!');
                can = false;
                return false;
            }
            lastprice += val;
        });
        var op = order_price + dispatch_price + lastprice;
        if (op < 0) {
            alert('订单价格不能小于0元!');
            return false;
        }
        if (!can) {
            return false;
        }
        return true;
    }

</script>

<script language="javascript">
    function confirmSend() {
        var numerictype = /^[a-zA-Z0-9]+$/;;

        if ($('#express_sn').val() == '' && $('#express_company').val() != '') {
            $('#send_form').val("order.list");
            return confirm('请填写快递单号！');
        }

        $('#expresscom').val($('#express_company option:selected').attr('data-name'));

        if ($('#express_sn').val() != '') {

            if (!numerictype.test($('#express_sn').val())) {
                $('#send_form').val("order.list");
                return confirm('快递单号格式不正确！');
            }
        }


        //todo 当未选择其他快递的时候,不允许提交
    }

    function send(btn) {
        var modal = $('#modal-confirmsend');
        var itemid = $(btn).parent().find('.itemid').val();
        $(".id").val(itemid);

        modal.find(':input[name=order_id]').val(itemid);
        if ($(btn).parent().find('.addressdata').val()) {
            var addressdata = JSON.parse($(btn).parent().find('.addressdata').val());
            if (addressdata) {
                modal.find('.realname').html(addressdata.realname);
                modal.find('.mobile').html(addressdata.mobile);
                modal.find('.address').html(addressdata.address);
            }
        }
    }
</script>

<!-- 查看物流 -->
<div id="modal-express" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"
     style="margin:0px auto; width:100%">

    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content" style="">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h3>查看物流</h3>
            </div>
            <div class="modal-body" style='max-height:500px;width:100%'>
                <div class="" id="data" >
                    {{--<p class='form-control-static' id="module-menus-express"></p>--}}
                    {{--<div style=" white-space:normal; width:100%;">--}}
                            {{--20191111-08-01 18:24:48] ]【佛山市】  快件已在 【顺德乐从】 签收, 签收人: 同事/同学, 如有疑问请电联:18688290757 / 0757-28857568, 您的快递已经妥投。风里来雨里去, 只为客官您满意--}}
                    {{--</div>--}}
                    {{--<p  style="white-space:normal; width:100%">[20191111-08-01 18:24:48] ]【佛山市】  快件已在 【顺德乐从】 签收, 签收人: 同事/同学, 如有疑问请电联:18688290757 / 0757-28857568, 您的快递已经妥投。风里来雨里去, 只为客官您满意。上有老下有小, 赏个好评好不好？【请在评价快递员处帮忙点亮五颗星星哦~】]</p>--}}
                </div>
            </div>
            <div class="modal-footer"><a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>
    </div>

</div>
<script language='javascript'>
    function express_find(btn, orderid) {
        $(btn).button('loading');
        $.ajax({
            url: "{php echo $this->createWebUrl('order/list',array('op'=>'deal','to'=>'express'))}&id=" + orderid,
            cache: false,
            success: function (html) {
                $('#module-menus-express').html(html);
                $('#modal-express').modal();
                $(btn).button('reset');
            }
        })
    }

    function refundexpress_find(btn, orderid, flag) {
        {{--console.log("{!! yzWebUrl('order.list',array('op'=>'deal','to'=>'refundexpress')) !!}&id=" + orderid + "&flag=" + flag)--}}
        {{--console.log(orderid)--}}

        $(btn).button('loading');
        $.ajax({
            url: "{!! yzWebUrl('order.detail.express') !!}" +"&id="+orderid,//"{php echo $this->createWebUrl('order/list',array('op'=>'deal','to'=>'refundexpress'))}&id=" + orderid + "&flag=" + flag,
            cache: false,
            success: function (html) {
                console.log(html.data.data)
                if (html.data.data != null && html.data.data.length > 0){
                    html.data.data.forEach(v=>{
                        $('#data').append(` <p style="white-space:normal; width:100%">[`+v.time+`] ]`+ v.context+`]</p>`);
                    });
                }
                console.log(html)
                $('#module-menus-express').html(html);
                $('#modal-express').modal();
                $(btn).button('reset');
            }
        })
    }


    // function refundexpress_find(btn, orderid, flag) {
    //     $(btn).button('loading');
    //     $.ajax({
    //         url: "{php echo $this->createWebUrl('order/list',array('op'=>'deal','to'=>'refundexpress'))}&id=" + orderid + "&flag=" + flag,
    //         cache: false,
    //         success: function (html) {
    //             $('#module-menus-express').html(html);
    //             $('#modal-express').modal();
    //             $(btn).button('reset');
    //         }
    //     })
    // }
</script>