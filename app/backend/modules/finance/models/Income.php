<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/31
 * Time: 下午3:05
 */
namespace app\backend\modules\finance\models;

class Income extends \app\common\models\Income
{
    public static function getIncomeList($search = [])
    {
        $Model = static::getIncomes();
        
        return $Model;
    }
}