@extends('layouts.base')
@section('title','EXCEL 批量充值')

@section('content')
    <div class="panel panel-info">
        <div class="panel-heading">
            <span>当前位置：</span>
            <a href="#">
                <span>批量充值</span>
            </a>
        </div>
    </div>
    <div class="alert alert-info alert-important">
        <span>功能介绍:</span>
        <span style="padding-left: 60px;">1. 使用excel快速导入进行会员充值（积分、余额）, 文件格式<b style="color:red;">[xls]</b></span>
        <span style="padding-left: 60px;">2. 一次导入的数据不要太多,大量数据请分批导入,建议在服务器负载低的时候进行</span>
        <br>
        <span>使用方法:</span>
        <span style="padding-left: 60px;">1. 下载Excel模板文件并录入信息</span>
        <span style="padding-left: 60px;">2. 选择充值类型</span>
        <span style="padding-left: 60px;">3.选择第一列值的类型 </span>
        <span style="padding-left: 60px;">4. 上传Excel导入</span>
        <br>
        <span>格式要求： Excel第一列可以为会员ID或者手机号(根据选择的上传第一列值得类型决定)，第二列必须为充值数量</span>
    </div>
    <form id="importform" class="form-horizontal form" action="{{yzWebUrl('excelRecharge.confirm.index')}}" method="post" enctype="multipart/form-data">
        <div class='form-group'>
            <div class="form-group">
                <label class="col-sm-2 control-label must">充值类型</label>
                <div class="col-sm-5 goodsname" style="padding-right:0;">
                    <select class="form-control batch_type" name="batch_type">
                        <option value="balance">充值余额</option>
                        <option value="point">充值积分</option>
                        @if($loveOpen)
                            <option value="love">充值{{ $loveName }}</option>
                        @endif
                    </select>
                </div>
                <div class="col-sm-1 love_type" style="padding-right:0;display:none;width: 75px">
                    <select class="form-control" name="love_type">
                        <option value="usable">可用</option>
                        <option value="froze">冻结</option>
                    </select>
                </div>
            </div>

            <div class='form-group'>
                <div class="form-group">
                    <label class="col-sm-2 control-label must">上传第一列的值</label>
                    <div class="col-sm-5 goodsname" style="padding-right:0;">
                        <select class="form-control batch_type" name="genre">
                            <option value="1">会员ID</option>
                            <option value="2">手机号</option>
                        </select>
                    </div>
                </div>

            <div class="form-group">
                <label class="col-sm-2 control-label must">EXCEL文件</label>
                <div class="col-sm-5 goodsname" style="padding-right:0;">
                    <input type="file" name="batch_recharge" class="form-control"/>
                </div>
            </div>
        </div>
        <div class='form-group'>
            <div class="col-sm-12">
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="cancelsend" value="yes">确认导入</button>
                    <a class="btn btn-primary" href="{{yzWebUrl('excelRecharge.records.index')}}" style="margin-right: 10px;">
                        <i class="fa fa-pencil"></i>
                        <span>充值记录</span>
                    </a>
                    <a class="btn btn-primary" href="{{yzWebUrl('excelRecharge.example.index')}}&id=1" style="margin-right: 10px;">
                        <i class="fa fa-download"></i>
                        <span>下载Excel模板文件(会员ID)</span>
                    </a>

                    <a class="btn btn-primary" href="{{yzWebUrl('excelRecharge.example.index')}}&id=2" style="margin-right: 10px;">
                        <i class="fa fa-download"></i>
                        <span>下载Excel模板文件(手机号)</span>
                    </a>
                </div>
            </div>
        </div>
        </div>
    </form>
    <script language="javascript">
        $('.batch_type').change(function () {
            var type = $(this).children('option:selected').val();
            if (type == 'love') {
                $('.love_type').show();
            } else {
                $('.love_type').hide();
            }
        });
    </script>

@endsection('content')
