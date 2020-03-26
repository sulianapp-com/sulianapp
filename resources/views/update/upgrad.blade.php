@extends('layouts.base')

@section('title', trans('系统升级'))

@section('css')
    <link href="{{static_url('resource/css/upgrade.css')}}" rel="stylesheet">
    <style>
        .version-box{border-left:1px solid #e9e9e9;margin:0;}
        .version-radius{background:#409EFF;width:10px;height:10px;border-radius: 50%;float:left;position:relative;left:-5px;}
        .version-margin-left{margin-left:30px;}
        .version-num{font-weight:900;font-size:22px;line-height:24px;margin-bottom:20px;}
        .version-margin-bottom{margin-bottom:20px;}
        .version-time{background:#f9f9f9;border:1px solid #e9e9e9;padding:5px 10px;font-weight:500;}
        .version-content{font-weight:500;line-height:28px;}
    </style>
@endsection

@section('content')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="margin-left: 0px">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 style="display: inline-block; font-size: 16px">
                系统升级
            </h1>
        </section>
        <!-- Main content -->
        <section class="content">

            <div class="modal" id="showMiddleModal"
                 data-backdrop="false" data-keyboard="false"
                 role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <h5 class="center-block">正在加载中...</h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box">

                <div class="box-body">
                    <div class="alert"></div>

                    <div class="row">
                        <div class="col-md-6">
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6  scrollbox  left-menu-border content-height-scroll" >
                            <ul class="nav nav-sidebar" id="upgrad_file">
                            </ul>
                        </div>
                        <div class="col-md-6 main content-height-scroll">

                                <div id="upgrade" class="display" style="display: none;">
                                    <div class="form-group button_title"></div>

                                    <div class="form-group">
                                        <button id="upgradebtn" class="btn btn-success" style="height: 32px">
                                            <i class="fa fa-download"></i> <label> 立即更新 </label>
                                        </button>
                                        <span id="process"></span>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-description"> 最新版本号：</label>
                                        <span class="interval" id="versionNumber" style="color: #ff0d0d">00</span>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-description"> 版本说明：</label><br/>
                                        <div class="interval" id="versionDetail" style="padding-top:30px;">
                                           
                                        </div>
                                    </div>

                                </div>

                        </div>
                    </div>
                </div>
            </div>

        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <script>
        var front_upgrade = '{{$count}}';
        
        $(function() {
            $.ajax({
                url: '{!! yzWebUrl('update.verifyheck') !!}',
                type: 'get',
                dataType: 'json',
                beforeSend: function(){
                    var html = '<li><br/>正在检查更新文件</li>';
                    $("#upgrad_file").html(html);
                }
            }).done(function (ret) {
                console.log("Downloading finished");
                console.log(ret);

                if (-1 == ret.result) {
                    window.location.href = "{!! yzWebFullUrl('update.pirate') !!}";
                }

                if (0 == ret.result) {
                    $("#upgrad_file").html('<li><br/>' + ret.msg + '</li>');
                }

                if (98 == ret.result) {
                    var msg = '';
                    msg+="<li><br/><span style='color: #dd1116'>" + ret.msg +"</span></li>"

                    $("#upgrad_file").html('<li><br/>' + msg + '</li>');
                }

                if (99 == ret.result) {
                    var msg = '';
                    msg+="<li><br/>当前版本：<span style='color: #dd4b39'>" + ret.last_version +"</span></li>"
                    msg+="<li><br/>恭喜您，您现在是最新版本！</li>"

                    $("#upgrad_file").html('<li><br/>' + msg + '</li>');
                }

                if (1 == ret.result) {
                    var html = "";

                    if(ret.filecount<=0 && !ret.upgrade){
                        if (0 == front_upgrade) {
                            html+="<li><br/>当前版本：<span style='color: #dd4b39'>" + ret.version +"</span></li>"
                            html+="<li><br/>恭喜您，您现在是最新版本！</li>"
                        } else {
                            var version     = '{{$version}}';
                            var new_version = '{{$list[$count-1]['version']}}';

                           //单独更新前端
                            html+="<li><br/>当前版本：<span style='color: #dd4b39'>" + version + "</span></li>"
                        }
                    } else{
                        if(ret.filecount > 0){
                            html+="<br/><b style='color:#dd4b39'>更新之前请注意数据备份!</b><br/><br/>";
                            html += "更新文件(选中则不更新文件):<br>";
                            var data = ret.files;

                            for(var o in data){
                                html += '<li><label class="checkbox-inline"><input type="checkbox" value="'+data[o].path+'" name="files"> ' + data[o].path+"</label></li>";
                            }
                        }
                    }

                    $("#upgrad_file").html(html);

                    if (front_upgrade > 0) {
                        $('#versionNumber').html(new_version);
                        $('#versionDetail').html('');
                        $('#upgrade').show();

                        $("#upgradebtn").unbind('click').click(function(){
                            if($(this).attr('updating')=='1'){
                                return;
                            }

                            $(this).attr('updating',1);
                            $(this).find('label').html('正在更新中...');

                            $('#process').html("前端文件下载更新");
                            frontUpgrade();
                        });
                    }

                    if(ret.filecount > 0 || ret.upgrade){
                        $('#versionNumber').html(ret.version);
                        if (ret.log.length != 0) {
                            var count = ret.log.length;
                            for (i=0; i < count; i++) {
                                let created_at = ret.log[i]['created_at'];
                                created_at = created_at.split(" ");
                                console.log(created_at[0])
                                let content_text = ret.log[i]['content'].split("\n");
                                console.log(content_text)
                                var content = [];
                                var content_log = ``;
                                for(let j=0;j<content_text.length;j++) {
                                    content[j] = `
                                        <div class="version-content">
                                            `+content_text[j]+`
                                        </div>
                                    `
                                    content_log = content.join("")
                                }
                                var html = `<div class="version-box">
                                                <div class="version-radius"></div>
                                                <div class="version-margin-left">
                                                    <div class="version-num">`+ret.log[i]['title']+`</div>
                                                    <div class="version-margin-bottom">
                                                        <span class="version-time">`+created_at[0]+`</span>
                                                    </div>
                                                        <div style="padding-bottom:30px">`
                                                            +content_log+
                                                        `</div>
                                                </div>
                                            </div>`
                                $(html).appendTo('#versionDetail');
                                // $('<div>' + ret.log[i]['content'] + '</div>').appendTo('#versionDetail');
                            }
                        }
                        $('#upgrade').show();

                        $("#upgradebtn").unbind('click').click(function(){
                            if($(this).attr('updating')=='1'){
                                return;
                            }

                            $(this).attr('updating',1);
                            $(this).find('label').html('正在更新中...');

                            upgrade();
                        });
                    }
                }

            }).fail(function (message) {
                console.log('update.start-download:', message)
            });

            $("#updateVersion").click(function () {
                var $btn = $(this);
                console.log($btn);
                $btn.button('loading');
                $.ajax({
                    url: '{!! yzWebUrl('update.start-download') !!}',
                    type: 'POST',
                    dataType: 'json'
                })
                    .done(function (json) {

                        console.log("Downloading finished");
                        console.log(json);
                        $btn.button('reset');
                    })
                    .fail(function (message) {
                        console.log('update.start-download:', message)
                    });

            });
        });

        //文件更新
        function upgrade(){
            var $check_boxes = $('input[name="files"]:checked');
            var fileIds = new Array();

            $check_boxes.each(function(){
                fileIds.push($(this).val());
            });

            $.ajax({
                url: '{!! yzWebUrl('update.fileDownload') !!}',
                data:{'nofiles': fileIds},
                //traditional :true,
                type:'post',
                dataType:'json',
                success:function(ret){
                    if(ret.result==1)      {
                        $('#process').html("后台文件已更新 " + ret.success + "个文件 / 共 " + ret.total +  " 个文件！");
                        //循环更新
                        upgrade();
                    }
                    else if(ret.result==2){
                        if (front_upgrade > 0) {
                            $('#process').html("前端文件下载更新");
                            frontUpgrade();
                        } else {
                            $('#upgradebtn').find('label').html('更新完成');
                            $('#process').html('');

                            location.reload();
                        }
                    }
                    else if(ret.result==3){
                        //跳过计数，3是不更新的
                        upgrade();
                    }
                }
            });
        }

        function frontUpgrade(){
            $.ajax({
                url: '{!! yzWebUrl('update.startDownload') !!}',
                dataType:'json',
                success:function(ret){
                    if(ret.status==1)      {
                        $('#upgradebtn').find('label').html('更新完成');
                        $('#process').html('');

                        location.reload();
                    } else {
                        $('#process').html('网络请求超时');
                    }
                }
            });
        }
    </script>
@endsection

