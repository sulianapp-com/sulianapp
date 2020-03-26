@extends('layouts.base')
@section('title', '余额提现')
@section('content')

    <div class="panel panel-default">
        <div class='panel-body'>
            <div style='height:auto;width:120px;float:left;'>
                <img src='{{ $member->avatar }}' style='width:100px;height:100px;border:1px solid #ccc;padding:1px'/>
            </div>
            <div style='float:left;height:auto;overflow: hidden'>
                <p>
                    <b>昵称:</b>
                    {{ $member->nickname }}
                </p>
                <p>
                    <b>姓名:</b>
                    {{ $member->realname }}
                </p>
                <p>
                    <b>手机号:</b>
                    {{ $member->mobile }}
                </p>
            </div>
        </div>

        <form action="" method='post' class='form-horizontal'>
            <div class='panel-body'>
                <table class="table table-hover">
                    <thead class="navbar-inner">
                    <tr>
                        <th>ID</th>
                        <th>收货人</th>
                        <th>电话</th>
                        <th>省份</th>
                        <th>城市</th>
                        <th>区域</th>
                        @if(\Setting::get('shop.trade.is_street'))
                            <th>街道</th>
                        @endif
                        <th>详细地址</th>
                    </tr>
                    </thead>
                    <tbody>


                    @foreach($address as $key => $addres)
                        <tr style="background: #eee">
                            <th>{{ $addres['id'] }}</th>
                            <th>{{ $addres['username'] }}</th>
                            <th>{{ $addres['mobile'] }}</th>
                            <th>{{ $addres['province'] }}</th>
                            <th>{{ $addres['city'] }}</th>
                            <th>{{ $addres['district'] }}</th>
                            @if(\Setting::get('shop.trade.is_street'))
                                <th>{{ $addres['street'] }}</th>
                            @endif
                            <th>{{ $addres['address'] }}</th>
                        </tr>
                    @endforeach
                </table>
            </div>


            <div class="form-group col-sm-12">

                <input type="button" class="btn btn-default" name="submit" onclick="history.go(-1)" value="返回" style='margin-left:10px;'/>

            </div>
        </form>

    </div>

@endsection