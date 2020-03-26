@extends('layouts.base')
@section('title', '微信模板管理')
@section('content')
    <script src="https://cdn.static.runoob.com/libs/angular.js/1.4.6/angular.min.js"></script>
    <div class="page-content">
        <div class="alert alert-success">
            <b>注意：</b>
            <p>请将公众平台模板消息所在行业选择为：<b>IT科技/互联网|电子商务&nbsp;&nbsp;&nbsp;其他/其他</b>，所选行业不一致将会导致模板消息不可用。
            </p>
            <p>您的公众平台模板消息目前所属行业为：<b>{{$industry_text}}</b></p>
            <p>当前列表内的模板消息为您已申请的模板消息，您可以点击查看详情或者删除处理。</p>
        </div>

        <form action="" method="post">
            <form action="" method="get" class="form-horizontal form-search" role="form1">
                <input type="hidden" name="c" value="site"/>
                <input type="hidden" name="a" value="entry"/>
                <input type="hidden" name="m" value="yun_shop"/>
                <input type="hidden" name="do" value="temp" id="form_do"/>
                <input type="hidden" name="route" value="setting.diy-temp.index" id="route" />
                <div class="page-toolbar">
            <span class=''>
                 <a class='btn btn-info btn-sm' href="{!! yzWebUrl('setting.small-program.notice') !!}"><i class="fa fa-plus-square"></i> 消息通知设置</a>
             </span>
                </div>
            </form>
        </form>
        <body ng-app="">
        <form id="tmp-list"
              action="{!! yzWebUrl('setting.small-program.del') !!}"
              method="post">
            @if (count($list)>0)
                <table class="table table-responsive table-hover" style="width:99%">
                    <thead>
                    <tr>
                        {{--<td style='width:6%;text-align: center;'>--}}
                            {{--<input type="checkbox" ng-model="all">--}}
                        {{--</td>--}}
                        <th style='width:6%;text-align: center;'>序号</th>
                        <th style='width:50%;text-align: center;'>模板名称</th>
                        <th style="width:38%;">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $key => $row)
                        <tr>
                            {{--<td style='text-align: center;'>--}}
                                {{--<input type="checkbox" ng-checked="all"--}}
                                       {{--name="tmp_id[]"--}}
                                       {{--value="{{$row['template_id']}}">--}}
                            {{--</td>--}}
                            <td style="text-align: center;">
                                {!! $key + 1 !!}
                            </td>
                            <td style="text-align: center;">{{$row['title']}}</td>
                            <td style="text-align: center;">
                                <a class="btn btn-info btn-sm disbut"
                                   href="{!! yzWebUrl('setting.small-program.see', array('tmp_id' => $row['template_id'])) !!}">查看</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td colspan="2">

                        </td>
                        <td colspan="2">
                            <span class="pull-right" style="line-height: 28px;">共{!! count($list) !!}
                                条记录</span>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            @else
                <div class='panel panel-default'>
                    <div class='panel-body'
                         style='text-align: center;padding:30px;'>
                        暂时没有任何微信模板
                        !
                    </div>
                </div>
            @endif
            {{--<div class='panel-footer'>--}}
                {{--<input name="submit" type="submit" class="btn btn-danger"--}}
                       {{--value="删除">--}}
            {{--</div>--}}
        </form>
        </body>
    </div>


    <script language='javascript'>
        function addtempoption() {
            var tempcode = $("#tempcode").val();
            var data = {
                templateidshort: tempcode
            };
            var url = "{!! yzWebUrl('setting.small-program.add-tmp') !!}";
            $.ajax({
                "url": url,
                "data": data,
                success: function (ret) {
                    if (ret.result == 1) {
                        alert("加入成功");
                        location.reload();
                    } else {
                        alert("加入失败,请检查模板数量是否达到上限(25个)以及模板编码是否输入正确!");
                    }
                }
            });
        }
    </script>
@endsection