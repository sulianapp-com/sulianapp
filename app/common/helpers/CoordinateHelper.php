<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/1
 * Time: 下午6:34
 */

namespace app\common\helpers;


class CoordinateHelper
{
    public static function tpl_form_field_coordinate($field, $value = array())
    {
        $s = '';
        if(!defined('TPL_INIT_COORDINATE')) {
            $s .= '<script type="text/javascript">
                    function showCoordinate(elm) {
                        require(["util"], function(util){
                            var val = {};
                            val.lng = parseFloat($(elm).parent().prev().prev().find(":text").val());
                            val.lat = parseFloat($(elm).parent().prev().find(":text").val());
                            util.map(val, function(r){
                                $(elm).parent().prev().prev().find(":text").val(r.lng);
                                $(elm).parent().prev().find(":text").val(r.lat);
                            });
    
                        });
                    }
    
                </script>';
            define('TPL_INIT_COORDINATE', true);
        }
        $s .= '
                <div class="row row-fix">
                    <div class="col-xs-4 col-sm-4">
                        <input type="text" name="' . $field . '[lng]" value="'.$value['lng'].'" placeholder="地理经度"  class="form-control" />
                    </div>
                    <div class="col-xs-4 col-sm-4">
                        <input type="text" name="' . $field . '[lat]" value="'.$value['lat'].'" placeholder="地理纬度"  class="form-control" />
                    </div>
                    <div class="col-xs-4 col-sm-4">
                        <button onclick="showCoordinate(this);" class="btn btn-default" type="button">选择坐标</button>
                    </div>
                </div>';
        return $s;
    }
}