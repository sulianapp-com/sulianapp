<?php
namespace app\backend\modules\goods\services;
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/2
 * Time: 上午17:26
 */
class DispatchService
{
    public static function getDispatch($dispatchData)
    {
        $dispatchData['weight_data'] = serialize($dispatchData['weight']);
        $dispatchData['piece_data'] = serialize($dispatchData['piece']);
        unset($dispatchData['weight']);
        unset($dispatchData['piece']);
        return $dispatchData;
    }
}