<?php

namespace  app\common\services\goods;

use app\common\components\BaseController;
use Yunshop\LeaseToy\models\LeaseToyGoodsModel;
use Setting;

/**
* Create 2018/3/5
* Author 芸众商城 www.yunzshop.com
*/
class LeaseToyGoods extends BaseController
{

    public static function whetherEnabled()
    {

        $leaseToy = Setting::get('plugin.lease_toy');
        if (app('plugins')->isEnabled('lease-toy')) {
            if ($leaseToy['is_lease_toy']) {
                return $leaseToy['is_lease_toy'];
            }
        }

        return 0;
    }

    public static function getDate($goodsId)
    {
        return LeaseToyGoodsModel::whereGoodsId($goodsId)->first();

    }
}