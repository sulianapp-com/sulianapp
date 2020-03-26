<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 19:41
 */

namespace app\backend\modules\goods\services;



class GoodsPrivilegeService
{

    public static function tpl_form_field_date($name, $value = '', $withtime = false)
    {
        $html = '';
        if (!defined('TPL_INIT_DATA')) {
            $html = '
			<script type="text/javascript">
				require(["datetimepicker"], function(){
					$(function(){
						$(".datetimepicker").each(function(){
							var option = {
								lang : "zh",
								step : "30",
								timepicker : ' . (!empty($withtime) ? "true" : "false") .
                ',closeOnDateSelect : true,
			format : "Y-m-d' . (!empty($withtime) ? ' H:i:s"' : '"') .
                '};
			$(this).datetimepicker(option);
		});
	});
});
</script>';
            define('TPL_INIT_DATA', true);
        }
        $withtime = empty($withtime) ? false : true;
        if (!empty($value)) {
            $value = strexists($value, '-') ? strtotime($value) : $value;
        } else {
            $value = TIMESTAMP;
        }
        $value = ($withtime ? date('Y-m-d H:i:s', $value) : date('Y-m-d', $value));
        $html .= '<input type="text" name="' . $name . '"  value="' . $value . '" placeholder="请选择日期时间" class="datetimepicker form-control" style="padding-left:12px;" />';
        return $html;
    }

}