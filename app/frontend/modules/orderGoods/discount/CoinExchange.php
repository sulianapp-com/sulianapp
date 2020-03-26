<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/6/12
 * Time: 下午1:51
 */

namespace app\frontend\modules\orderGoods\discount;

use app\common\modules\orderGoods\models\PreOrderGoods;

/**
 * 积分兑换
 * Class CoinExchange
 * @package app\frontend\modules\order\discount
 */
class CoinExchange extends BaseDiscount
{
    protected $code = 'coinExchange';
    protected $name = '积分兑换';

    /**
     * 获取金额
     * @return float|int|null
     */
    protected function _getAmount()
    {
        // 如果开启了积分兑换,
        return ;
    }
}