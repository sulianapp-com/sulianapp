<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>角色 | Yunshop</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="/libs/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="main animsition">
    <div class="container-fluid">

        <div class="row">
            <div class="">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">添加角色</h3>
                    </div>
                    <div class="panel-body">


                        <form class="form-horizontal" role="form" method="POST" action="/index.php/admin/role/create">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="cove_image"/>


                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">角色名称</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="name" id="tag" value="{{ $name }}" autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">角色概述</label>
                                <div class="col-md-5">
                                    <textarea name="description" class="form-control" rows="3">{{ $description }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tag" class="col-md-3 control-label">权限列表</label>
                            </div>
                            <div class="form-group">
                                <div class="form-group">
                                    @if($permissionAll)
                                        @foreach($permissionAll[0] as $v)
                                            <div class="form-group">
                                                <label class="control-label col-md-3 all-check">
                                                    {{$v['label']}}：
                                                </label>
                                                <div class="col-md-6">
                                                    @if(isset($permissionAll[$v['id']]))

                                                        @foreach($permissionAll[$v['id']] as $vv)
                                                            <div class="col-md-4" style="float:left;padding-left:20px;margin-top:8px;">
                        <span class="checkbox-custom checkbox-default">
                        <i class="fa"></i>
                            <input class="form-actions"
                                   @if(in_array($vv['id'],$permissions))
                                   checked
                                   @endif
                                   id="inputChekbox{{$vv['id']}}" type="Checkbox" value="{{$vv['id']}}"
                                   name="permissions[]"> <label for="inputChekbox{{$vv['id']}}">
                                {{$vv['label']}}
                            </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-md-7 col-md-offset-3">
                                    <button type="submit" class="btn btn-primary btn-md">
                                        <i class="fa fa-plus-circle"></i>
                                        添加
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.0 -->
<script src="/plugins/jQuery/jQuery-2.2.0.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>