<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/favicon.ico">
    <title> 密码找回工具 - 广州市芸众信息科技有限公司 - 商城系统 -  Powered by Yunzhong </title>
    <link href="/static/resource/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/resource/css/font-awesome.min.css" rel="stylesheet">
    <link href="/static/resource/css/common.css" rel="stylesheet">
    <script src="/static/resource/js/require.js"></script>
</head>
<body>
<div class="main">
    <form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
        <div class="panel panel-default" style="margin:10px;">
            <div class="panel-heading">
                重置密码 <span class="text-muted">如果你的管理密码意外遗失, 请使用此工具重置密码, 重置成功后请尽快将此文件从服务器删除, 避免造成安全隐患</span>
            </div>
            <div class="panel-body">
                @if ($is_ok)
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">用户名:</label>
                    <div class="col-sm-9">
                        <input name="auth" type="hidden" value="{{$auth}}" />
                        <input name="user[username]" type="text" class="form-control" placeholder="请输入你要重置密码的用户名">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">新的登录密码:</label>
                    <div class="col-sm-9">
                        <input name="user[password]" type="password" class="form-control" placeholder="">
                    </div>
                </div>
                @else
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">请输入访问密码</label>
                    <div class="col-sm-9">
                        <input name="auth" type="password" class="form-control" placeholder="">
                    </div>
                </div>
                @endif
                <div class="form-group">
                    <label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label"></label>
                    <div class="col-sm-9">
                        <button type="submit" class="btn btn-primary btn-block" name="submit" value="提交">提交</button>
                        <input type="hidden" name="token" value="{$_W['token']}" />
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</body>
</html>