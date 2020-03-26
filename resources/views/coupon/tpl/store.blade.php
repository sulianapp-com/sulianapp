<tr>
    <td>
        <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
    </td>
    <td colspan="2">
        <input type="hidden" class="form-control" name="store_ids[]"  data-id="{$id}" data-name="storeids" value="" placeholder="按钮名称" style="width:200px;float:left"  />
        <input class="form-control" type="text" data-id="{$id}" data-name="storenames" placeholder="" value="" name="store_names[]" style="width:200px;float:left" readonly="true">
        <span class="input-group-btn">
            <button class="btn btn-default nav-link" type="button" data-id="{$id}" onclick="$('#modal-module-menus-store').modal();$(this).parent().parent().addClass('focusstore')">选择门店</button>
        </span>
    </td>
</tr>
