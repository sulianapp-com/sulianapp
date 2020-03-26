<tr>
    <td>
        <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;"  title="删除"><i class='fa fa-times'></i></a>
    </td>
    <td colspan="2">
        <input type="hidden" class="form-control" name="hotel_ids[]"  data-id="{$id}" data-name="hotelids" value="" placeholder="按钮名称" style="width:200px;float:left"  />
        <input class="form-control" type="text" data-id="{$id}" data-name="hotelnames" placeholder="" value="" name="hotel_names[]" style="width:200px;float:left" readonly="true">
        <span class="input-group-btn">
            <button class="btn btn-default nav-link" type="button" data-id="{$id}" onclick="$('#modal-module-menus-hotel').modal();$(this).parent().parent().addClass('focushotel')">选择酒店</button>
        </span>
    </td>
</tr>
