<div class="panel panel-default">
    <div class="panel-heading">
        收货信息
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">收件人 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    {{$order['address']['realname']}}
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">联系电话 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    {{$order['address']['mobile']}}
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">收货地址 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    {{$order['address']['address']}}
                </p>
            </div>
        </div>
        @if(isset($dispatch))
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">物流信息 :</label>
                <div class="col-sm-9 col-xs-12">
                    <p class="form-control-static">公司:{{$dispatch['company_name']}}</p>
                    <p class="form-control-static">运单号:{{$dispatch['express_sn']}}</p>
                    <div>

                    @foreach($dispatch['data'] as $item)
                        <p class="form-control-static">[{{$item['time']}}] {{$item['context']}}</p>
                    @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>