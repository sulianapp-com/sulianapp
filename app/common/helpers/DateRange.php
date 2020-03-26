<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午9:38
 */

namespace app\common\helpers;


class DateRange
{
    /**
     * 【表单控件】: 范围日期选择器
     * @param string $name 表单input名称
     * @param array $value 表单input值
     * 		array('start' => 开始日期,'end' => 结束日期)
     * @param boolean $time 是否显示时间
     * @return string
     */
    public static function tplFormFieldDateRange($name, $value = array(), $time = false) {
        $s = '';

        if (empty($time) && !defined('TPL_INIT_DATERANGE_DATE')) {
            $s = '
            <script type="text/javascript">
                require(["daterangepicker"], function($){
                    $(function(){
                        $(".daterange.daterange-date").each(function(){
                            var elm = this;
                            $(this).daterangepicker({
                                startDate: $(elm).prev().prev().val(),
                                endDate: $(elm).prev().val(),
                                format: "YYYY-MM-DD"
                            }, function(start, end){
                                $(elm).find(".date-title").html(start.toDateStr() + " 至 " + end.toDateStr());
                                $(elm).prev().prev().val(start.toDateStr());
                                $(elm).prev().val(end.toDateStr());
                            });
                        });
                    });
                });
            </script>
            ';
            define('TPL_INIT_DATERANGE_DATE', true);
        }

        if (!empty($time) && !defined('TPL_INIT_DATERANGE_TIME')) {
            $s = '
            <script type="text/javascript">
                require(["daterangepicker"], function($){
                    $(function(){
                        $(".daterange.daterange-time").each(function(){
                            var elm = this;
                            $(this).daterangepicker({
                                startDate: $(elm).prev().prev().val(),
                                endDate: $(elm).prev().val(),
                                format: "YYYY-MM-DD HH:mm",
                                timePicker: true,
                                timePicker12Hour : false,
                                timePickerIncrement: 1,
                                minuteStep: 1
                            }, function(start, end){
                                $(elm).find(".date-title").html(start.toDateTimeStr() + " 至 " + end.toDateTimeStr());
                                $(elm).prev().prev().val(start.toDateTimeStr());
                                $(elm).prev().val(end.toDateTimeStr());
                            });
                        });
                    });
                });
            </script>
            ';
            define('TPL_INIT_DATERANGE_TIME', true);
        }
        if (isset($value['starttime']) && isset($value['start'])) {
            if($value['start']) {
                $value['starttime'] = empty($time) ? date('Y-m-d',strtotime($value['start'])) : date('Y-m-d H:i',strtotime($value['start']));
            }
            $value['starttime'] = empty($value['starttime']) ? (empty($time) ? date('Y-m-d') : date('Y-m-d H:i') ): $value['starttime'];
        } else {
            $value['starttime'] = '请选择';
        }

        if (isset($value['endtime']) && isset($value['end'])) {
            if($value['end']) {
                $value['endtime'] = empty($time) ? date('Y-m-d',strtotime($value['end'])) : date('Y-m-d H:i',strtotime($value['end']));
            }
            $value['endtime'] = empty($value['endtime']) ? $value['starttime'] : $value['endtime'];
        } else {
            $value['endtime'] = '请选择';
        }
        $s .= '
        <input name="'.$name . '[start]'.'" type="hidden" value="'. $value['starttime'].'" />
        <input name="'.$name . '[end]'.'" type="hidden" value="'. $value['endtime'].'" />
        <button class="btn btn-info daterange '.(!empty($time) ? 'daterange-time' : 'daterange-date').'" type="button"><span class="date-title">'.$value['starttime'].' 至 '.$value['endtime'].'</span> <i class="fa fa-calendar"></i></button>
        ';
        return $s;
    }

    /**
     * 【表单控件】: 日期选择器
     * @param string $name 表单input名称
     * @param array $value 表单input值
     * 		array('start' => 开始日期,'end' => 结束日期)
     * @param boolean $time 是否显示时间
     * @return string
     */
    public static function tplFormFieldDate($name, $value = array(), $time = false) {
        $s = '';

        if (empty($time) && !defined('TPL_INIT_DATERANGE_DATE')) {
            $s = '
            <script type="text/javascript">
                require(["daterangepicker"], function($){
                    $(function(){
                        $(".daterange.daterange-date").each(function(){
                            var elm = this;
                            $(this).daterangepicker({
                                startDate: $(elm).prev().prev().val(),
                                format: "YYYY-MM-DD"
                            }, function(start){
                                $(elm).find(".date-title").html(start.toDateStr());
                                $(elm).prev().prev().val(start.toDateStr());
                            });
                        });
                    });
                });
            </script>
            ';
            define('TPL_INIT_DATERANGE_DATE', true);
        }

        if (!empty($time) && !defined('TPL_INIT_DATERANGE_TIME')) {
            $s = '
            <script type="text/javascript">
                require(["daterangepicker"], function($){
                    $(function(){
                        $(".daterange.daterange-time").each(function(){
                            var elm = this;
                            $(this).daterangepicker({
                                startDate: $(elm).prev().prev().val(),
                                endDate: $(elm).prev().val(),
                                format: "YYYY-MM-DD HH:mm",
                                timePicker: true,
                                timePicker12Hour : false,
                                timePickerIncrement: 1,
                                minuteStep: 1
                            }, function(start, end){
                                $(elm).find(".date-title").html(start.toDateTimeStr() + " 至 " + end.toDateTimeStr());
                                $(elm).prev().prev().val(start.toDateTimeStr());
                                $(elm).prev().val(end.toDateTimeStr());
                            });
                        });
                    });
                });
            </script>
            ';
            define('TPL_INIT_DATERANGE_TIME', true);
        }
        if (isset($value['starttime']) && isset($value['start'])) {
            if($value['start']) {
                $value['starttime'] = empty($time) ? date('Y-m-d',strtotime($value['start'])) : date('Y-m-d H:i',strtotime($value['start']));
            }
            $value['starttime'] = empty($value['starttime']) ? (empty($time) ? date('Y-m-d') : date('Y-m-d H:i') ): $value['starttime'];
        } else {
            $value['starttime'] = '请选择';
        }
        $s .= '
        <input name="'.$name . '[start]'.'" type="hidden" value="'. $value['starttime'].'" />
        <input name="'.$name . '[end]'.'" type="hidden" value="'. $value['endtime'].'" />
        <button class="btn btn-info daterange '.(!empty($time) ? 'daterange-time' : 'daterange-date').'" type="button"><span class="date-title">'.$value['starttime'].'</span> <i class="fa fa-calendar"></i></button>
        ';
        return $s;
    }

}