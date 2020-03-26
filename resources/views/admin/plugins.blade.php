@extends('layouts.base')
@section('title', trans('插件管理'))
@section('content')
    <div class="w1200 m0a">
        <script language="javascript" src="{{static_url('js/dist/nestable/jquery.nestable.js')}}"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('js/dist/nestable/nestable.css')}}"/>

        <!-- 新增加右侧顶部三级菜单 -->
        <section class="content-header">
            <h3 style="display: inline-block;    padding-left: 10px;">
                {{ trans('插件管理') }}
            </h3>
            <a href="{{yzWebUrl('plugin.plugins-market.Controllers.new-market.show')}}" class="btn btn-success"
               style="font-size: 13px;float: right;margin-top: 70px;">插件安装/升级</a>
        </section>
        <div style="position: fixed; right: 20px; top: 60px;z-index:999">
            <input id="key" type="text" class="el-input__inner" style="width: 150px;height: 32px;line-height: 32px;"
                   placeholder="请输入关键字"/>
            <input type="button" class="el-button el-button--small" value="下一个" onclick="next()"/>
            <input type="button" class="el-button el-button--small" value="上一个" onclick="previous()"/>
        </div>

        <div style="color:#ff2620">
            （更新插件后，请在插件管理页面，将已更新了的插件禁用后再启用）
        </div>
        <div class='panel panel-default'>
            <div class='panel-body'>
                <button class="btn btn-success checkall">全选</button>
                <button class="btn btn-success checkrev">反选</button>
                <button class="btn btn-success batchenable" type="submit">批量启用</button>
                <button class="btn btn-danger batchdisable" type="submit">批量禁用</button>
                <table class="table row">
                    <thead>
                    <tr>
                        <th style="width: 3%;">选择</th>
                        <th style='width:10%;'>版本</th>
                        <th style='width:10%;'>名称</th>
                        <th style='width:50%;'>描述</th>
                        <th style='width:10%;'>状态</th>
                        <th style='width:20%;'>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($installed as $plugin)
                        <tr>
                            <td><input type="checkbox" name="check1" value="{{$plugin->name}}"></td>
                            <td>[{{$plugin->version}}]</td>
                            <td class="tip" title="{{$plugin->title}}">
                                {{$plugin->title}}
                            </td>
                            <td style="color:#f39c12" class="tip" title="{{$plugin->description}}">
                                {{$plugin->description}}
                            </td>
                            <td>@if($plugin->isEnabled())
                                    启用
                                @else
                                    禁用
                                @endif
                            </td>
                            <td>
                                <a class='btn btn-default btn-sm'
                                   onclick="{{$plugin->isEnabled() ? 'disable' : 'enable'}}('{{$plugin->name}}')"
                                   href="javascript:void(0)"
                                   title='{{($plugin->isEnabled() ? '禁用' : '启用')}}'>
                                    @if($plugin->isEnabled())
                                        <i class="fa fa-power-off"></i>
                                    @else
                                        <i class="fa fa-check-circle-o"></i>
                                    @endif

                                </a>
                                {{--<a class='btn btn-default btn-sm'
                                   href="{{yzWebUrl('plugins.manage', ['name'=>$plugin['name'],'action'=>'delete'])}}"
                                   title='删除' onclick="return confirm('确认删除此插件吗？');return false;">
                                    <i class="fa fa-remove"></i>
                                </a>--}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <button class="btn btn-success checkall">全选</button>
                <button class="btn btn-success checkrev">反选</button>
                <button class="btn btn-success batchenable" type="submit">批量启用</button>
                <button class="btn btn-danger batchdisable" type="submit">批量禁用</button>
            </div>
        </div>

        <script>
            $(function () {
                $(".checkall").click(function () {
                    //全选
                    if ($(this).html() == '全选') {
                        $(this).html('全不选');
                        $('[name=check1]:checkbox').prop('checked', true);
                    } else {
                        $(this).html('全选');
                        $('[name=check1]:checkbox').prop('checked', false);
                    }
                });
                $(".checkrev").click(function () {
                    //反选
                    $('[name=check1]:checkbox').each(function () {
                        this.checked = !this.checked;
                    });
                });

                var arr = new Array();
                var url = "{!! yzWebUrl('plugins.batchMange') !!}"

                $(".batchenable").click(function () {
                    $(this).html('启用中...');
                    $("input[type='checkbox']:checked").each(function (i) {
                        arr[i] = $(this).val();
                    });
                    var vals = arr.join(",");
                    var postdata = {
                        names: vals,
                        action: 'enable',
                    };
                    $.post(url, postdata, function (data) {
                        if (data) {
                            alert('操作失败，请重新选择');
                            return false;
                        }
                        $(".batchenable").html('启用成功');
                        setTimeout(location.reload(), 3000);
                    });
                });

                $(".batchdisable").click(function () {
                    $(this).html('禁用中...');
                    $("input[type='checkbox']:checked").each(function (i) {
                        arr[i] = $(this).val();
                    });
                    var vals = arr.join(",");
                    var postdata = {
                        names: vals,
                        action: 'disable',
                    };
                    $.post(url, postdata, function (data) {
                        if (data) {
                            alert('操作失败，请重新选择');
                            return false;
                        }
                        $(".batchdisable").html('禁用成功');
                        setTimeout(location.reload(), 3000);
                    });
                });

            });

            function enable($plugin) {
                $(this).html('启用中...');

                var postdata = {
                    name: $plugin,
                    action: 'enable',
                };
                $.post("{!! yzWebUrl('plugins.manage') !!}", postdata, function (data) {

                    if (data.result == 1) {
                        alert('启用成功');
                        setTimeout(location.reload(), 1000);
                    }else{

                        alert('操作失败，请重新选择');
                        return false;
                    }

                });
            }

            function disable($plugin) {
                $(this).html('启用中...');

                var postdata = {
                    name: $plugin,
                    action: 'disable',
                };
                $.post("{!! yzWebUrl('plugins.manage') !!}", postdata, function (data) {
                    if (data.result == 1) {
                        alert('禁用成功');
                        setTimeout(location.reload(), 1000);
                    }else{
                        alert('操作失败，请重新选择');
                        return false;
                    }
                });
            }
        </script>

        @endsection
        <style type="text/css">
            .res {
                color: red;
            }

            .result {
                background: yellow;
            }
        </style>

        <script type="text/javascript">
            var oldKey = "";
            var index = -1;
            var pos = new Array();//用于记录每个关键词的位置，以方便跳转
            var oldCount = 0;//记录搜索到的所有关键词总数

            function previous() {
                index--;
                index = index < 0 ? oldCount - 1 : index;
                search();
            }

            function next() {
                ++index;
                //index = index == oldCount ? 0 : index;
                if (index == oldCount) {
                    index = 0;
                }
                search();
            }

            function search() {
                $(".result").removeClass("res");//去除原本的res样式
                var key = $("#key").val(); //取key值
                if (!key) {
                    console.log("key为空则退出");
                    $(".result").each(function () {//恢复原始数据
                        $(this).replaceWith($(this).html());
                    });
                    oldKey = "";
                    return; //key为空则退出
                }
                if (oldKey != key) {
                    console.log("进入重置方法");
                    //重置
                    index = 0;
                    $(".result").each(function () {
                        $(this).replaceWith($(this).html());
                    });
                    pos = new Array();
                    var regExp = new RegExp(key + '(?!([^<]+)?>)', 'ig');//正则表达式匹配
                    $(".row").html($(".row").html().replace(regExp, "<span id='result" + index + "' class='result'>" + key + "</span>")); // 高亮操作
                    $("#key").val(key);
                    oldKey = key;
                    $(".result").each(function () {
                        pos.push($(this).offset().top);
                    });
                    oldCount = $(".result").length;
                    console.log("oldCount值：", oldCount);
                }

                $(".result:eq(" + index + ")").addClass("res");//当前位置关键词改为红色字体

                // $("body").scrollTop(pos[index]);//跳转到指定位置
                window.scrollTo(0, pos[index] - 60);
                console.log(pos[index]);
                console.log(index);
            }
        </script>
