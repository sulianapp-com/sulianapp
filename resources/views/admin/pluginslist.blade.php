@extends('layouts.base')

@section('content')

@section('title', trans('应用中心'))
<style>
    .el-button+.el-button { margin-left:0px; }
    .plugin-span {
        /* display:inline-block; */
        overflow: hidden; 
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width:150px;
    }

</style>
    <div class="w1200 m0a">
        <script language="javascript" src="{{static_url('js/dist/nestable/jquery.nestable.js')}}"></script>
        <link rel="stylesheet" type="text/css" href="{{static_url('js/dist/nestable/nestable.css')}}"/>
        
        <!--应用列表样式-->
        <link rel="stylesheet" type="text/css" href="{{static_url('yunshop/plugins/list-icon/css/list-icon.css')}}">

        <!-- 新增加右侧顶部三级菜单 -->
        <section class="content-header">
            <h3 style="display: inline-block;padding-left: 10px;">
                {{ trans('应用中心') }}<i class="pl api-group-purchase"></i>
            </h3>
        </section>
        
        <div style="position: fixed; right: 20px; top: 60px;z-index:999">
            <input id="key" type="text" class="el-input__inner" style="width: 150px;height: 32px;line-height: 32px;" placeholder="请输入关键字" />
            <input type="button" class="el-button el-button--small" value="下一个" onclick="next()" />
            <input type="button" class="el-button el-button--small" value="上一个" onclick="previous()" />
        </div>

        <div class="row">
            @foreach( $class as $key1 => $value)
                @if(is_array($data[$key1]))
                    <div class="panel panel-default">
                        <div class="panel-heading" style="background-color: #f6f6f6">
                            <h3 class="panel-title">{{ $value['name'] }}</h3>
                        </div>
                        <div class="panel-body">
                            @foreach($data[$key1] as $key => $plugin)
                                @if(can($key))
                                <div class=" col-lg-3 col-md-4 col-sm-4" style="display:flex; align-items: center;">
                                    <a href="{{yzWebFullUrl($plugin['url'])}}" class="plugin-a col-md-10 col-sm-12"  style="display:flex;">
                                        <div class="plugin-i-div" style="flex: 0 0 50px;">
                                            <i class="plugin-i" style="background-color: {{$value['color']}}; background-image: url({{ $plugin['icon_url'] }})"></i>
                                        </div>
                                        <div class="plugin-span">{{$plugin['name']}}</div>
                                        <object style="height:0px" style="flex: 0 0 20px;">
                                            <a class="top_show"
                                               style="display: none;position:relative;top:15px;padding-left:5px;"
                                               href="{{yzWebUrl('plugins.setTopShow',['name'=>$key,'action'=>(app('plugins')->isTopShow($key) ? 1 : 0)])}}">
                                                <i class="fa fa-tags" @if(app('plugins')->isTopShow($key))style="color: red" @endif
                                                data-toggle="tooltip"  data-placement="top"
                                                   @if(app('plugins')->isTopShow($key))title="取消顶部显示?" @else title="选择顶部显示?"@endif></i>
                                            </a>
                                        </object>
                                        {{--<span class="plugin-span-down">{{$plugin['description']}}</span>--}}
                                    </a>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        <div>
    </div>

    <!-- <script src="{{static_url('js/pluginslist.js')}}"></script> -->
    <script src="{{resource_get('resources/views/admin/pluginslist.js')}}"></script>
    
@endsection
<style type="text/css">
    .res
    {
        color: Red;
    }
    .result{
        background: yellow;
    }
</style>
<script type="text/javascript">
        var oldKey = "";
        var index = -1;
        var pos = new Array();//用于记录每个关键词的位置，以方便跳转
        var oldCount = 0;//记录搜索到的所有关键词总数
		
		function previous(){
			index--;
            index = index < 0 ? oldCount - 1 : index;
			search();
		}
		function next(){
			++index;
            //index = index == oldCount ? 0 : index;
			if(index==oldCount){
				index = 0 ;
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
				var regExp = new RegExp(key+'(?!([^<]+)?>)', 'ig');//正则表达式匹配
                $(".row").html($(".row").html().replace(regExp, "<span id='result" + index + "' class='result'>" + key + "</span>")); // 高亮操作
                $("#key").val(key);
                oldKey = key;
                $(".result").each(function () {
                    pos.push($(this).offset().top);
                });
                oldCount = $(".result").length;
				console.log("oldCount值：",oldCount);
            }
 
            $(".result:eq(" + index + ")").addClass("res");//当前位置关键词改为红色字体
 
            // $("body").scrollTop(pos[index]);//跳转到指定位置
            window.scrollTo(0, pos[index]-60);
            console.log(pos[index]);
            console.log(index);
        }
    </script>
        
