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
    <form class="form-horizontal" method="post" action="/index.php/admin/appuser/{{$item['id']}}">
        <input type="hidden" name="id" value="{{$item['id']}}">
        @else
            <form class="form-horizontal" method="post" action="/index.php/admin/appuser/">
                @endif

                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">名称</label>

                    <div class="col-sm-5">
                        <input type="text" name="name" class="form-control" id="name" placeholder="请输入名称" value="{{$item['name']}}">
                    </div>
                    <div class="col-sm-5">
                    <!-- <p class="form-control-static text-danger">{{ $errors->first('Student.name') }}</p> -->
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