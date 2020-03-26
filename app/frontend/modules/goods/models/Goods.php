<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/2
 * Time: 18:31
 */

namespace app\frontend\modules\goods\models;

class Goods extends \app\common\models\Goods
{
    public $appends = ['status_name','estimated_commission','vip_price'];

    public function getEstimatedCommissionAttribute()
    {
        $price = round($this->price * $this->getSalesCommission() / 100,2);
        return $price;
    }

    public function getSalesCommission()
    {
        if (app('plugins')->isEnabled('sales-commission')) {
            $set = \Setting::get('plugin.sales-commission');
            if ($set['switch']) {
                $salesCommissionGoods = \Yunshop\SalesCommission\models\GoodsSalesCommission::getGoodsByGoodsId($this->id)->first();
                if ($salesCommissionGoods) {
                    if ($salesCommissionGoods->has_dividend == '1') {
                        return $salesCommissionGoods->dividend_rate;
                    } else {
                        return $set['default_rate'];
                    }
                }
            }
        }
        return 0;
    }

}