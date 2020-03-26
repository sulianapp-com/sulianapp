@extends('layouts.base')
@section('title', '查看详情')
@section('content')

    <div class="panel panel-default">
        <div class='panel-heading'>
            余额明细／查看详情
        </div>
        <div class='panel-body'>
            <div style='height:auto;width:120px;float:left;'>
                <img src='{{tomedia($detailModel->member->avatar)}}' style='width:100px;height:100px;border:1px solid #ccc;padding:1px'/>
            </div>
            <div style='float:left;height:auto;overflow: hidden'>
                <p>
                    <b>昵称:</b>
                    {{ $detailModel->member->nickname }}
                    <b>姓名:</b>
                    {{ $detailModel->member->realname }}
                    <b>手机号:</b>
                    {{ $detailModel->member->mobile }}
                </p>
                <p>
                    <b>变动余额: </b><span style='color:red'>{{ $detailModel->change_money }}</span> 元
                <p>
                <p>
                    <b>剩余余额: </b>{{ $detailModel->new_money }}
                </p>
                <p>
                    <b>业务类型: </b>{{ $detailModel->type_name }}
                <p>
                <p>
                    <b>变动时间: </b>{{ $detailModel->created_at }}
                </p>
                <p>
                    <b>订单编号: </b>{{ $detailModel->serial_number or '' }}
                </p>
            </div>
        </div>

        <div class='panel-heading'>
        </div>


        <form action="{{yzWebUrl("finance.withdraw.dealt",['id'=>$item['id']])}}" method='post' class='form-horizontal'>
            <div class='panel-body'>
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <td>会员ID</td>
                        <th>变动前余额</th>
                        <th>改变余额值</th>
                        <th>变动后余额值</th>
                        <th>类型</th>
                        <th>业务类型</th>
                        <th>备注</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr style="background: #eee">
                            <td>{{ $detailModel->member_id }}</td>
                            <td>{{ $detailModel->old_money }}</td>
                            <td>{{ $detailModel->change_money }}</td>
                            <td>{{ $detailModel->new_money }}</td>
                            <td>{{ $detailModel->type_name }}</td>
                            <td>{{ $detailModel->service_type_name }}</td>
                            <td>{{ $detailModel->remark }}</td>

                        </tr>
                </table>
            </div>


            <div class="form-group col-sm-12">
                <input type="button" class="btn btn-default" name="submit" onclick="goBack()" value="返回" style='margin-left:10px;'/>
            </div>
        </form>

    </div>

    <script language='javascript'>
        function goBack() {
            window.location.href = "{!! yzWebUrl('finance.balance-records.index') !!}";
        }
    </script>

@endsection