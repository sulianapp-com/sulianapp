<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 下午3:01
 */

namespace app\frontend\modules\coin;

use app\frontend\modules\finance\models\PointCoin;
use Illuminate\Container\Container;

class CoinManager extends Container
{
    public function __construct()
    {
        /**
         * 爱心值虚拟币模型
         */

        $this->bind('point', function ($coinManager, $attributes = []) {
            return new PointCoin($attributes);
        });
        $this->singleton('MemberCoinManager', function ($coinManager, $attributes = []) {
            return new MemberCoinManager($attributes);
        });
    }
}