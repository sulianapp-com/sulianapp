@extends('layouts.base')
@section('title','批量发货')

@section('content')
<div class="page-heading">
    <h2>批量发货</h2>
</div>
<div class="alert alert-info">
    功能介绍: 使用excel快速导入进行订单发货, 文件格式<b style="color:red;">[xls]</b>
    <span style="padding-left: 60px;">如重复导入数据将以最新导入数据为准，请谨慎使用</span>
    <span style="padding-left: 60px;">数据导入订单状态自动修改为已发货</span>
    <span style="padding-left: 60px;">一次导入的数据不要太多,大量数据请分批导入,建议在服务器负载低的时候进行</span>
    <br>
    使用方法: <span style="padding-left: 60px;">1. 下载Excel模板文件并录入信息</span>
    <span style="padding-left: 60px;">2. 选择快递公司</span>
    <span style="padding-left: 60px;">3. 上传Excel导入</span>
    <br>
        格式要求：  Excel第一列必须为订单编号，第二列必须为快递单号，请确认订单编号与快递单号的备注

</div>

<form id="importform" class="form-horizontal form" action="" method="post" enctype="multipart/form-data">

    <div class='form-group'>
        <div class="form-group">
            <label class="col-sm-2 control-label must">快递公司</label>
            <div class="col-sm-5 goodsname"  style="padding-right:0;" >
                <select class="form-control" name="send[express_code]" id="express">
                    @include('express.companies')
                </select>
                <input type="hidden" name='send[express_company_name]' id='expresscom' value="顺丰"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label must">EXCEL</label>

            <div class="col-sm-5 goodsname"  style="padding-right:0;" >
                <input type="file" name="send[excelfile]" class="form-control" />
                <span class="help-block">如果遇到数据重复则将进行数据更新</span>
            </div>
        </div>

    </div>

    <div class='form-group'>
        <div class="col-sm-12">
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" name="cancelsend" value="yes">确认导入</button>

                <a class="btn btn-primary" href="{{yzWebUrl('order.batch-send.get-example')}}" style="margin-right: 10px;" ><i class="fa fa-download" title=""></i> 下载Excel模板文件</a>
            </div>
        </div>
    </div>
    </div>
</form>


<script language='javascript'>
    $("#express").change(function () {
        var sel = $(this).find("option:selected").text();
        // var sel = $(this).find("option:selected").attr("data-name");
        $("#expresscom").val(sel);
    });


            $('#express').select2();


</script>
@endsection('content')

