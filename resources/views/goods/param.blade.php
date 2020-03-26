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
            @foreach ($params as $p)
            <tr>
                <td>
                    <a href="javascript:;" class="fa fa-move" title="拖动调整此显示顺序"><i class="fa fa-arrows"></i></a>&nbsp;
                    <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;" title="删除"><i class='fa fa-remove'></i></a>
                </td>
                <td>
                    <input name="param_title[]" type="text" class="form-control param_title" value="{$p['title']}"/>
                    <input name="param_id[]" type="hidden" class="form-control" value="{{$p['id']}}"/>
                </td>
                <td>
                    <input name="param_value[]" type="text" class="form-control param_value" value="{{$p['value']}}"/>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tbody>
            <tr>
                <td>&nbsp;</td>
                <td colspan="2">
                    <a href="javascript:;" id='add-param' onclick="addParam()" style="margin-top:10px;" class="btn btn-primary"  title="添加属性"><i class='fa fa-plus'></i> 添加属性</a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script language="javascript">
    $(function() {
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
        var url = "{!! yzWebUrl('goods.goods.getParamTpl') !!}";
        $.ajax({
            "url": url,
            success: function(data) {
                $('#param-items').append(data);
            }
        });
        return;
    }
    function deleteParam(o) {
        $(o).parent().parent().remove();
    }
</script>