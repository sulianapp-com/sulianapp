
<!-- 订单改价 -->
<div id="modal-changeprice" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form class="form-horizontal form" action="{{yzWebUrl('order.change-order-price.store')}}" method="post" enctype="multipart/form-data">
        <div class="modal-dialog"  style="width:750px;margin:0px auto;">
            <div class="modal-content" >
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                    <h3>订单改价</h3>
                </div>
                <div class="modal-body">

                    <div class="form-group">

                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-12">
                            <table class='table'>
                                <tr>
                                    <th style='width:30%;'>商品名称</th>
                                    <th style='width:15%;'>单价</th>
                                    <th style='width:10%;'>数量</th>
                                    <th style='width:20%;'>小计</th>
                                    <th style='width:10%;'>加价或减价</th>
                                    <th style='width:15%;'>运费</th>
                                </tr>
                                @foreach($order_goods_model as $key => $order_goods)

                                    <input type='hidden' name="order_id" value="{{$order_model->id}}"  />

                                    <tr>
                                        <td>{{$order_goods->hasOneGoods->title}}</td>
                                        <td class='realprice'>
                                            {{number_format($order_goods->price/$order_goods->total,2)}}
                                        </td>
                                        <td>{{$order_goods->total}}</td>
                                        <td>
                                            {{$order_goods->price}}
                                            @if($order_goods->change_price !=0)
                                            <label class='label label-danger'>改价</label>
                                            @endif
                                        </td>

                                        <td valign="top" >
                                            <input type='hidden' name="order_goods[{{$key}}][order_goods_id]" value="{{$order_goods->id}}"  />
                                            <input type='text' class='form-control changeprice_orderprice' name="order_goods[{{$key}}][change_price]"  />
                                        </td>
                                        @if($key == 0)
                                        <td valign="top" rowspan='{{$order_goods->hasOneGoods->goods_sn[$order_goods->hasOneGoods->order_id]}}' style='vertical-align: top' >
                                            <input type='text' class='form-control'  value="{{$order_model->dispatch_price}}" name='dispatch_price' />
                                            <a href='javascript:;' onclick="$(this).prev().val('0');mc_calc()">直接免运费</a>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan='2'></td>
                                    <td colspan='' style='color:green'>应收款</td>
                                    <td colspan='' style='color:green'>{{number_format($order_model->price)}}</td>
                                    <td colspan='2'  style='color:red'>改价后价格不能小于0元</td>
                                </tr>

                            </table>
                        </div>
                    </div>
                    <div class="form-group">

                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
                            <div class="form-control-static">

                            </div>
                        </div>
                    </div>

                    <div class="form-group">

                        <div class="col-xs-12 col-sm-9 col-md-8 col-lg-12">
                            <div class="form-control-static">

                                <b>购买者信息</b>  {{$order_model->address->address}} {{$order_model->address->realname}} {{$order_model->address->mobile}}<br/>
                                <b>买家实付</b>： <span id='orderprice'>{{$order_model->price-$order_model->dispatch_price}}</span> + <span id='dispatchprice'>{{$order_model->dispatch_price}}</span> <span id='changeprice'></span> = <span id='lastprice'>{{$order_model->price}}</span><br/>
                                <b>买家实付</b> = 原价 + 运费 + 涨价或减价<br/><br/>
                            </div>
                        </div>
                    </div>

                    <div id="module-menus"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary span2" name="confirmchange" value="yes" onclick='return mc_check()'>确认改价</button>
                    <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
                </div>
            </div>
        </div>
    </form>
</div>
<script>

    var order_price = 0;
    var dispatch_price = 0;

    mc_init();


    function mc_init() {

        order_price = parseFloat($('#changeprice-orderprice').val());
        dispatch_price = parseFloat($('#changeprice-dispatchprice').val());
        $('input', $('#ajaxModal')).blur(function () {
            if(judgeSign($(this).val())){
                mc_calc();
            }
        });

    }

    function judgeSign(num) {
        var reg = new RegExp("^-?[0-9]*.?[0-9]*$");
        if ( reg.test(num) ) {
            var absVal = Math.abs(num);
            return num==absVal?'是正数':'是负数';
        }
        else {
            return -1;
        }
    }

    function mc_check(){
        var can = true;
        var lastprice = 0;
        $('.changeprice_orderprice').each(function () {
            if( $.trim( $(this).val())==''){
                alert('请输入改价金额!');
                can = false;
                return can;
            }
            var p = 0;
            console.log(judgeSign($(this).val()));
            if (judgeSign($(this).val()) == -1) {
                $(this).select();
                alert('请输入数字!');
                can = false;
                return can;
            }
            var val  = parseFloat( $(this).val() );
            if(val<=0 && Math.abs(val) > parseFloat( $(this).parent().prev().html())) {
                $(this).select();
                alert('单个商品价格不能优惠到负数!');
                can =false;
                return false;
            }
            lastprice+=val;
        });
        var op = order_price + dispatch_price+ lastprice;
        if( op <0){
            alert('订单价格不能小于0元!');
            return false;
        }
        if(!can){
            return false;
        }
        return true;
    }

    function mc_calc() {

        var change_dispatchprice = parseFloat($('#changeprice_dispatchprice').val());
        if(!judgeSign($('#changeprice_dispatchprice').val())){
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
        if(Math.abs(oprice)>0){
            if (oprice < 0) {
                $('#changeprice').css('color', 'red');
                $('#changeprice').html( " - " + Math.abs(oprice));
            } else {
                $('#changeprice').css('color', 'green');
                $('#changeprice').html( " + " + Math.abs(oprice));
            }
        }
        var lastprice =  order_price + dprice + oprice;

        $('#lastprice').html( lastprice.toFixed(2) );

    }
</script>
