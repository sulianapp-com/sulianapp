<tr>
    <td>
        <a href="javascript:;" onclick="deleteParam(this)" style="margin-top:10px;" title="删除"><i class='fa fa-times'></i></a>
    </td>
    <td colspan="2">
        <input type="hidden" class="form-control" name="goods_id[]"  data-id="{$id}" data-name="goodsid" value="" placeholder="按钮名称" style="width:200px;float:left"  />
        <input class="form-control" type="text" data-id="{$id}" data-name="goodsname" placeholder="" value="" name="goods_name[]" style="width:200px;float:left" readonly="true">
        <span class="input-group-btn">
            <button class="btn btn-default nav-link-goods" type="button" data-id="{$id}" onclick="$('#modal-module-menus-goods-exchange').modal();$(this).parent().parent().addClass('focusgood')">选择兑换商品</button>
        </span>
    </td>
</tr>