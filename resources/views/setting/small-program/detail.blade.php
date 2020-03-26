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
                                <div class="tab-pane active" id="tab_setting">
                                    @include('setting.small-program.tpl.tempnotice')
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

</script>
@endsection
