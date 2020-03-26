<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/14
 * Time: 下午10:57
 */

namespace app\frontend\models;

use app\common\modules\discount\GoodsMemberLevelDiscount;
use app\common\modules\goodsOption\GoodsOptionPriceManager;

/**
 * Class GoodsOption
 * @package app\frontend\models
 * @property int id
 * @property int goods_id
 * @property string title
 * @property float weight
 * @property float product_price
 * @property float market_price
 * @property float cost_price
 * @property float deal_price
 * @property Goods goods
 */
class GoodsOption extends \app\common\models\GoodsOption
{
    private $dealPrice;
    protected $vipDiscountAmount;
    public $vipDiscountLog;

    private $priceManager;

    public function getPriceManager()
    {
        if (!isset($this->priceManager)) {
            $this->priceManager = new GoodsOptionPriceManager($this);
        }
        return $this->priceManager;
    }
    /**
     * 获取交易价(实际参与交易的商品价格)
     * @return float|int
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    public function getDealPriceAttribute()
    {
        if (!isset($this->dealPrice)) {
            $this->dealPrice = $this->getPriceManager()->getDealPrice();
        }

        return $this->dealPrice;
    }


    /**
     * @var GoodsMemberLevelDiscount
     */
    private $memberLevelDiscount;

    /**
     * @return GoodsMemberLevelDiscount
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    public function memberLevelDiscount()
    {
        if (!isset($this->memberLevelDiscount)) {
            $this->memberLevelDiscount = new GoodsMemberLevelDiscount($this->goods, Member::current());
        }
        return $this->memberLevelDiscount;
    }


    /**
     * 缓存等级折金额
     *  todo 如何解决等级优惠种类记录的问题
     * @param $price
     * @return float
     * @throws \app\common\exceptions\MemberNotLoginException
     */

    public function getVipDiscountAmount($price)
    {
        if (isset($this->vipDiscountAmount)) {

            return $this->vipDiscountAmount;
        }
        $this->vipDiscountAmount = $this->memberLevelDiscount()->getAmount($price);
        $this->vipDiscountLog = $this->memberLevelDiscount()->getLog($this->vipDiscountAmount);
        return $this->vipDiscountAmount;
    }


    /**
     * 获取商品的会员价格
     * @return float|int|mixed
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    public function getVipPriceAttribute()
    {
        return sprintf('%.2f', $this->deal_price - $this->getVipDiscountAmount($this->deal_price));;

    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
}