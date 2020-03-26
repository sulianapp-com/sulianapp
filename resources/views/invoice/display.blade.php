<div class="panel panel-default">
    <div class="panel-heading">
        发票
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">发票类型 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    @if (1==$order['invoice_type'])
                        纸质发票
                    @elseif(0==$order['invoice_type'])
                        电子发票
                    @endif
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label">发票抬头 :</label>
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    @if(1==$order['rise_type'])
                        个人
                    @elseif(0==$order['rise_type'])
                        单位
                    @endif
                </p>
            </div>
        </div>
        <div class="form-group">
            @if(1==$order['rise_type'])
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">抬头 :</label>
            @elseif(0==$order['rise_type'])
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">单位名称 :</label>
            @endif
            <div class="col-sm-9 col-xs-12">
                <p class="form-control-static">
                    {{$order['collect_name']}}
                </p>
            </div>
        </div>
        @if(0==$order['rise_type'])
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">纳税人识别号 :</label>
                <div class="col-sm-9 col-xs-12">
                    <p class="form-control-static">
                        {{$order['company_number']}}
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>