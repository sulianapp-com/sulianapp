<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{$title}}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="_token" content="{{ csrf_token() }}"/>
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{static_url('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{static_url('yunshop/libs/font-awesome/4.5.0/css/font-awesome.css')}}">
</head>
<body style="background-color: #e5e5e5">
<div class="container">
    <div class="row" style="text-align: center; padding-top: 100px;">

        <div><i class="fa fa-exclamation-triangle" style="font-size: 100px;color: #ff3d00"></i></div>
        <div class="col-xs-6 col-md-6" style="float: inherit; margin: 20px auto; font-size: 16px;color: rgba(29,28,45,0.4)">{{$content}}</div>

    </div>
</div>
</body>
</html>


