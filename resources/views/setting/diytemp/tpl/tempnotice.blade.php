<style>
    .form-horizontal .form-group{margin-right: -50px;}
    .col-sm-9{padding-right: 0;}
    .tm .btn { margin-bottom:5px;}

    .panel-heading{
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;
    }
    .panel-default{
        color: #8c8c8c;
        border-color: #efefef;
    }
    .panel-default .panel-heading{
        background: #fdfdfd;
        border-color:#efefef;
    }
    .panel-primary{
        border-color: #efefef;
    }
    .panel-primary .panel-heading{
        background: #44abf7;
        border-color:#efefef;
        background-color: rgba(22, 161, 199, 0.82);
    }
    .panel-success .panel-heading{
        color:#fff;
        background: #54c952;
        border-color:#efefef;
    }
    .panel-info .panel-heading {
        color:#fff;
        background:#8987d7;
        border-color:#efefef;
    }
    .panel-body ~ .panel-heading {
        border-top: 1px solid #efefef;
    }
    .panel-danger .panel-heading {
        color:#fff;
        background: #eb6060;
        border-color:#efefef;
    }
    .panel-warning .panel-heading {
        color:#fff;
        background: #ffc000;
        border-color:#efefef;
    }

</style>


<div class="row">
    <div class="col-sm-8" style="padding-right: 50px;">
        <input type="hidden" name="id" value="{{$temp['id']}}" />

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" >模板名称</label>
            <div class="col-sm-9 col-xs-12">
                <input type="text"  id="title" name="temp[title]"  class="form-control" value="{{$temp['title']}}" placeholder="模版名称，例：订单完成模板（自定义）" data-rule-required='true' />
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" >模板消息ID</label>
            <div class="col-sm-9 ">
                    <input type="text" readonly="readonly" id="template_id" name="temp[template_id]" class="form-control" value="{{$temp['template_id']}}" placeholder="模版消息ID，例：P8MxRKmW7wdejmZl14-swiGmsJVrFJiWYM7zKSPXq4I" data-rule-required='true' />
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" >头部标题</label>

            <div class="col-sm-8 title" style='padding-right:0' >

                <textarea name="temp[first]" class="form-control" value="" data-rule-required='true' placeholder="@{{first.DATA}}" rows="5">{{$temp['first']}}</textarea>
                <span class='help-block'>对填充模板 @{{first.DATA}} 的值 </span>
            </div>
            <div class="col-sm-1" style='padding-left:0;' >

                <input type="color" name="temp[first_color]" value="{{$temp['first_color']}}" style="width:32px;height:32px;" />

            </div>

        </div>

        @foreach($temp->data as $temp2)
            @include('setting.diytemp.tpl.common')
        @endforeach
        <div id="type-items"></div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" ></label>
            <div class="col-sm-9 col-xs-12">
                <a class="btn btn-default btn-add-type" href="javascript:;" onclick="addType();"><i class="fa fa-plus" title=""></i> 增加一条键</a>
                <span class='help-block'>
                        </span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" >尾部描述</label>
            <div class="col-sm-8 title" style='padding-right:0' >
                <textarea name="temp[remark]" class="form-control" placeholder="@{{remark.DATA}}" rows="5" >{{$temp['remark']}}</textarea>
                <span class='help-block'>填充模板 @{{remark.DATA}} 的值</span>
            </div>
            <div class="col-sm-1" style='padding-left:0' >

                <input type="color" name="temp[remark_color]" value="{{$temp['remark_color']}}" style="width:32px;height:32px;" />

            </div>

        </div>
        <div class="form-group">
            <label class="col-xs-12 col-sm-3 col-md-2 control-label" >跳转链接地址</label>
            <div class="col-sm-9 col-xs-12">
                <div class="input-group ">
                    <input class="form-control" type="text" data-id="PAL-00010" placeholder="请填写指向的链接 (请以http://开头, 不填则不跳转)" value="{{ $temp['news_link'] }}" name="temp[news_link]">
                    <span class="input-group-btn">
                                <button class="btn btn-default nav-link" type="button" data-id="PAL-00010">选择链接</button>
                            </span>
                </div>
                {{--<input type="text"  id="title" name="temp[link]"  class="form-control" value="{{$temp['link']}}" placeholder="模版名称，例：订单完成模板（自定义）" data-rule-required='true' />--}}
            </div>
        </div>
    </div>
    <div class="col-sm-4" style="max-width:350px;">
        <div class=""  >
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <span style="font-size: 15px">步骤一：</span>添加我的模板
                </div>
                <div class="panel-body">
                    <input type="text" id="tempcode" class="form-control" placeholder="模板编号,例:TM00015" style="margin-bottom: 5px;"  value="" />
                    <a class="btn btn-default" href="javascript:;" onclick="addtempoption();"> 添加快速模板</a>
                </div>
            </div>
        </div>

        <div class="" >
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <span style="font-size: 15px">步骤二：</span>选择模板
                </div>
                <div class="panel-body">
                    <select id="selecttemplate"  class=" form-control" style="margin-bottom: 5px;">
                    </select>
                    <a class="btn btn-default" href="javascript:;"  onclick="selecttemp();"> 选择模板</a>
                </div>
            </div>
        </div>

        <div class="example-div"  >
            <div class="panel panel-default">
                <div class="panel-heading">
                    模板展示:
                </div>
                <div class="panel-body">
                    <div id="example" class="text">
                    </div>
                </div>
            </div>
        </div>

        <div class="">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <select class="form-control diy-notice" onchange="$('.tm').hide();$('.tm-'+$(this).val()).show()">
                        <option value="" >选择模板变量类型</option>
                        @foreach(\app\common\modules\template\Template::current()->getItems() as $item)
                            <option value="{{$item['value']}}">{{$item['title']}}</option>
                        @endforeach
                    </select>
                </div>
                @foreach(\app\common\modules\template\Template::current()->getItems() as $item)
                    <div class="panel-heading tm tm-{{$item['value']}}" style="display:none">
                        {{$item['subtitle']}}
                    </div>
                    <div class="panel-body tm tm-{{$item['value']}}" style="display:none">
                        @foreach($item['param'] as $row)
                            <a href='JavaScript:' class="btn btn-default btn-sm">{{$row}}</a>
                        @endforeach
                    </div>
                @endforeach

                <div class="panel-body">
                    点击变量后会自动插入选择的文本框的焦点位置，在发送给粉丝时系统会自动替换对应变量值
                    <div class="text text-danger">
                        注意：请选择对应模板变量, 否则消息通知内容有误 .
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@include('public.admin.mylink')
<script language='javascript'>

    var kw = 1;
    var temps;
    var contents;
    restempoption();

    function selecttemp()
    {
        var tid = $("#selecttemplate").val();
        var temp;

        for(var i=0;i<temps.length;i++){
            if(temps[i].template_id == tid)
            {
                temp =temps[i];
                break;
            }
        }

        $(document).on("click",".nav-link",function(){
            var id = $(this).data("id");
            if(id){
                $("#modal-mylink").attr({"data-id":id});
                $("#modal-mylink").modal();
            }
        });

        if(temp == null) {
            return;
        } else {
            contents = temp.contents;

            if(contents[0] != 'first' || contents[contents.length-1] != 'remark') {
                alert("此模板不可用!");
                return;
            }
            $("#example").html(temp.content);

            $(".example-div").show();
            $("#title").val(temp.title);
            $("#template_id").val(temp.template_id);

            $('.key_item').remove();

            setcontents(0);
        }
    }


    function setcontents(i){

        if(contents.length == i) {
            return;
        }
        if(contents[i]!='first'&&contents[i]!='remark') {
            var url = "{!! yzWebUrl('setting.diy-temp.tpl') !!}";
            $.ajax({
                "url": url,
                "data":{tpkw:contents[i]},
                success: function (html) {
                    $(".btn-add-type").button("reset");
                    $("#type-items").append(html);
                    i++
                    setcontents(i);
                }
            });

        } else {
            i++
            setcontents(i);
        }
    }



    function addtempoption() {
        var tempcode = $("#tempcode").val();
        var data = {
            templateidshort: tempcode
        };
        var url = "{!! yzWebUrl('setting.wechat-notice.addTmp') !!}";
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

    function restempoption() {
        var url = "{!! yzWebUrl('setting.wechat-notice.returnJson') !!}";
        $.ajax({
            "url": url,
            success: function (ret) {
                if (typeof ret === "string") {
                    var ret = $.parseJSON(ret);
                }
                
                if (ret.result == 1) {
                    $("#selecttemplate option").remove();
                    temps = ret.data.tmp_list;
                    for(var i=0;i<temps.length;i++){
                        $("#selecttemplate").append("<option value='"+temps[i].template_id+"'>"+temps[i].title+"</option>");
                    }
                }
            }
        });
    }

    function addType() {
        $(".btn-add-type").button("loading");
        var url = "{!! yzWebUrl('setting.diy-temp.tpl') !!}";
        $.ajax({
            "url": url,
            "data":{kw:kw},
            success: function (html) {
                $(".btn-add-type").button("reset");
                $("#type-items").append(html);
            }
        });
        kw++;
    }

    $('.diy-notice').select2();

</script>
