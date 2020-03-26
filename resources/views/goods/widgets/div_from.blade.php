<div class='panel panel-default'>

    <div class='panel-heading'>表单设置</div>
    <div class='panel-body'>
        <div class='panel-body'>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label">商品表单：</label>
                <div class="col-sm-4 col-xs-6">
                    <label class="radio-inline">
                        <input type="radio" name="widgets[div_from][status]" value="1" @if ($div_from['status'] == 1) checked="checked" @endif />
                        开启
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="widgets[div_from][status]" value="0" @if (empty($div_from['status'])) checked="checked" @endif />
                        关闭
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
                <div class="col-sm-9 col-xs-12">
                    <span>表单用途：用于跨境商品报税使用，开启后首次购买跨境商品的会员需要填写身份证号和真实姓名</span>
                </div>
           </div>
        </div>
    </div>


</div>