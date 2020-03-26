<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 上午11:32
 */

namespace app\backend\widgets\goods;

use app\common\components\Widget;
use app\common\facades\Setting;
use app\common\models\Coupon;
use app\common\models\goods\GoodsCoupon;

class CouponWidget extends Widget
{

    public function run()
    {
        $couponModel = GoodsCoupon::ofGoodsId($this->goods_id)->first();
        return view('goods.widgets.coupon', [
            'coupon' => $couponModel,
            //'coupon' => $coupon,
        ])->render();
    }
}

