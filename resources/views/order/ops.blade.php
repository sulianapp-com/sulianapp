    <script language="javascript">
        function pay(order_id)
        {
            if (confirm('确认此订单已付款吗？')) {
                $.get("{!! yzWebUrl('order.operation.pay') !!}",{order_id:order_id}, function(json){
                    if (json.result == 1) {
                        alert('付款成功');
                        location.href = location.href;
                    } else {
                        alert(json.msg);
                    }

                });
            }
        }
    </script>
@if ($order['status'] == 0)
<a class="btn btn-success b tn-sm disbut"
   href="javascript:;"
   onclick="pay({{$order['id']}})">确认付款</a>
@endif

@if ($order['status'] == 1)
<div>
    <input class='addressdata' type='hidden' value='{!! json_encode($order['address']) !!}' />
    <input class='itemid' type='hidden' value="{{$order['id']}}"/>
    <a class="btn btn-success btn-sm disbut" href="javascript:;" onclick="send(this)"  data-toggle="modal"
       data-target="#modal-confirmsend">确认发货</a>
</div>
@endif

@if ($order['status'] == 2)
<a class="btn btn-danger btn-sm disbut" href="javascript:;"
   onclick="$('#modal-cancelsend').find(':input[name=order_id]').val('{{$order['id']}}')" data-toggle="modal"
   data-target="#modal-cancelsend">取消发货</a>
<a class="btn btn-primary btn-sm disbut"
   href="{!! yzWebUrl('order.operation.receive', array('order_id' => $order['id'])) !!}"
   onclick="return confirm('确认订单收货吗？');return false;">确认收货</a>
@endif



