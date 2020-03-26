<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>添加平台</title>
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/dist/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="/plugins/iCheck/square/blue.css">
</head>
<body>
    <div class="login-logo">
        <a>添加平台</a>
    </div>

    {{--<form action="/index.php/admin/application/" enctype="" method="post">--}}
        {{--{!! csrf_field() !!}--}}
        {{--平台名称<input class="form-control" type="text" name="name"> <br>--}}
        {{--平台标题<input class="form-control" type="text" name="title"> <br>--}}
        {{--平台简介<input class="form-control" type="text" name="descr"> <br>--}}
        {{--平台图片<input class="form-control" type="file" name="img"> <br>--}}
        {{--状态<input class="form-control" type="radio" name="status" value="0"> 禁用--}}
        {{--<input class="form-control" type="radio" name="status" value="1"> 启用--}}
        {{--<input type="submit">--}}
        {{--<button type="submit">提交</button>--}}
    {{--</form>--}}

    @if($item)
        <form class="form-horizontal" enctype="multipart/form-data" method="post" action="/index.php/admin/application/{{$item['id']}}">
            <!-- <input type="hidden" name="id" value="{{$item['id']}}"> -->
        <!-- <form class="form-horizontal" method="post" enctype="multipart/form-data" action="/index.php/admin/application/upload"> -->

    @else
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="/index.php/admin/application/upload/">
    @endif

        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">应用名称</label>

            <div class="col-sm-5">
                <input type="text" name="name" class="form-control" id="name" placeholder="请输入应用名称" value="{{$item['name']}}">
            </div>
            <div class="col-sm-5">
                <!-- <p class="form-control-static text-danger">{{ $errors->first('Student.name') }}</p> -->
            </div>
        </div>
        <div class="form-group">
            <label for="validity_time" class="col-sm-2 control-label">有效期</label>

            <div class="col-sm-5">
                <input type="text" name="validity_time" class="form-control" id="validity_time" placeholder="请输入有效期" value="{{$item['validity_time']}}">
            </div>
            <div class="col-sm-5">
                <!-- <p class="form-control-static text-danger">{{ $errors->first('Student.validity_time') }}</p> -->
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">应用状态</label>

            <div class="col-sm-5">
                <label class="radio-inline">
                    <input type="radio" name="status" value="1" @if($item['status']==1) checked @endif> 启用
                </label>
                <label class="radio-inline">
                    <input type="radio" name="status" value="0" @if($item['status']==0) checked @endif> 禁用
                </label>
            </div>
            <div class="col-sm-5">
                <!-- <p class="form-control-static text-danger">{{ $errors->first('status') }}</p> -->
            </div>
        </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">img</label>

            <div class="col-sm-5">
                <label class="radio-inline">
                    <input type="file" name="img"> 图片
                </label>
            </div>
            <div class="col-sm-5">
                <!-- <p class="form-control-static text-danger">{{ $errors->first('status') }}</p> -->
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">提交</button>
            </div>
        </div>
    </form>
</body>
</html>