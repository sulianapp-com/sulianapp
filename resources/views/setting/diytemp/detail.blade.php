@extends('layouts.base')
@section('title', '模板消息设置')
@section('content')
    <div class="right-addbox"><!-- 此处是右侧内容新包一层div -->
        <div class="panel panel-default">
            <div class="panel-body">
                <form id="store_form" action="" method="post" class="form-horizontal form">
                    <div class="top">
                        <ul class="add-shopnav" id="myTab">
                            <li ><a href="#tab_setting">模板消息设置</a></li>
                        </ul>
                    </div>

                    <div class="info">
                        <div class="panel-body">
                            <div class="tab-content">
                                {{--<div class="tab-pane active" id="tab_base">
                                    @include('setting.diytemp.tpl.base')
                                </div>--}}
                                {{--<div class="tab-pane active" id="tab_customnotice">
                                    @include('setting.diytemp.tpl.customnotice')
                                </div>--}}
                                <div class="tab-pane active" id="tab_setting">
                                    @include('setting.diytemp.tpl.tempnotice')
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg control-label" ></label>
                                <div class="col-sm-9 col-xs-12">
                                    <div class="help-block">
                                        <button class="btn btn-primary" type="submit">提交</button>
                                        <input type="button" name="back" onclick='history.back()' style=''
                                               value="返回列表"
                                               class="btn btn-default back"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>


<script>



    require(['bootstrap'],function(){
        $('#settingTab a').click(function (e) {
            e.preventDefault();
            $('#tab').val( $(this).attr('href'));
            $(this).tab('show');
        })
    });

    $(function () {
        require(['jquery.caret'],function(){
            var jiaodian;
            $(document).on('focus', 'input,textarea',function () {
                jiaodian = this;
            });

            $("a[href='JavaScript:']").click(function () {
                if (jiaodian) {
                    $(jiaodian).insertAtCaret("["+this.innerText+"]" );
                }
            })

        })
    })

    $('form').submit(function(){
        if($('#title').val() == ''){
            Tip.focus($('#title'),'请填写模板名称!');
            return false;
        }
        if($('.key_item').length <= 0){
            Tip.focus($('.key_item'),'请添加一条键!');
            return false;
        }
        var checkkw = true;
        $(":input[name='tp_kw[]']").each(function(){
            if ( $.trim( $(this).val() ) ==''){
                checkkw = false;
                tip.msgbox.err('请输入键名!');
                $(this).focus();
                $('form').attr('stop',1);
                return false;
            }
        });
        if( !checkkw){
            return false;
        }
        return true;
    })
</script>
@endsection
