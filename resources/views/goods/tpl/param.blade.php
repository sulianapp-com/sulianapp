<div class="panel-body table-responsive" style="padding:0px;">
    <table class="table">
        <thead>
            <tr>
                <th style='width:50px;'></th>
                <th>属性名称</th>
                <th>属性值</th>
            </tr>
        </thead>
        <tbody id="param-items">
            @if (isset($params))
            @foreach ($params as $p)
            <tr>
                <td>
                    <a href="javascript:;" class="fa fa-move" title="拖动调整此显示顺序"><i class="fa fa-arrows"></i></a>&nbsp;
                    <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;" title="删除"><i class='fa fa-remove'></i></a>
                </td>
                <td>
                    <input name="param_title[]" type="text" class="form-control param_title" value="{{$p['title'] or ''}}"/>
                    <input name="param_id[]" type="hidden" class="form-control" value="{{$p['id'] or ''}}"/>
                </td>
                <td>
                    <input name="param_value[]" type="text" class="form-control param_value" value="{{$p['value'] or ''}}"/>
                </td>
            </tr>
            @endforeach
                @endif
        </tbody>
        <tbody>
            <tr>
                <td>&nbsp;</td>
                <td colspan="2">
                    <a href="javascript:;" id='add-param' onclick="addParam()" class="btn btn-success"  title="添加属性"><i class='fa fa-plus'></i> 添加属性</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script type="text/x-template" id="paramTpl">

<tr>
    <td>
        <a href="javascript:;" class="fa fa-move" title="拖动调整此显示顺序"><i class="fa fa-arrows"></i></a>&nbsp;
        <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;" title="删除"><i class='fa fa-remove'></i></a>
    </td>
    <td>
        <input name="param_title[]" type="text" class="form-control param_title" value=""/>
        <input name="param_id[]" type="hidden" class="form-control" value=""/>
    </td>
    <td>
        <input name="param_value[]" type="text" class="form-control param_value" value=""/>
    </td>
</tr>
</script>

<script>
    $(function() {
        require(['jquery.ui'], function () {
            $("#param-items").sortable({handle: '.fa-move'});
        });
        $("#chkoption").click(function() {
            var obj = $(this);
            if (obj.get(0).checked) {
                $("#tboption").show();
                $(".trp").hide();
            }
            else {
                $("#tboption").hide();
                $(".trp").show();
            }
        });
    })
    function addParam() {
        var paramHtml = $("#paramTpl").html();
        $('#param-items').append(paramHtml);
        /*var url = "{!! yzWebUrl('goods.goods.getParamTpl') !!}";
        $.ajax({
            "url": url,
            success: function(data) {
                $('#param-items').append(data);
            }
        });*/
        return;
    }
    function deleteParam(o) {
        $(o).parent().parent().remove();
    }
</script>