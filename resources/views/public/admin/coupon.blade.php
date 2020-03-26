<div id="modal-module-menus-coupon" class="modal fade" tabindex="-1">
    <div class="modal-dialog" style="width: 980px;">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h3>选择优惠券</h3></div>
            <div class="modal-body">
                <div class="row">
                    <div class="input-group">
                        <input type="text" class="form-control" name="keyword" value="" id="search-kwd-coupon" placeholder="请输入优惠券名称"/>
                        <span class='input-group-btn'>
                            <button type="button" class="btn btn-default" onclick="search_coupons();">搜索</button>
                        </span>
                    </div>
                </div>
                <div id="module-menus-coupon"></div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
            </div>
        </div>

    </div>
</div>

<script language='javascript'>



    //优惠券模态框
    function showCouponModel(obj) {
        $('#modal-module-menus-coupon').modal();
    }


    //优惠券搜索
    function search_coupons() {
        if ($('#search-kwd-coupon').val() == '') {
            Tip.focus('#search-kwd-coupon', '请输入关键词');
            return;
        }
        $("#module-menus-coupon").html("正在搜索....");
        $.get("{!! yzWebUrl('coupon.coupon.get-search-coupons') !!}", {
            keyword: $.trim($('#search-kwd-coupon').val())
        }, function (dat) {
            $('#module-menus-coupon').html(dat);
        });
    }

    //选择优惠券
    function select_coupon(o) {
        //$("#coupon_id").val(o.id);
        //$("#coupon").val(o.name);
        $('.select_coupon_id').val(o.id);
        $('.select_coupon_name').val(o.name);
        $("#modal-module-menus-coupon .close").click();
        //console.log($(document).find('.recharge-item'));
        $(document).find('input').removeClass('select_coupon_id');
        $(document).find('input').removeClass('select_coupon_name');
    }

</script>