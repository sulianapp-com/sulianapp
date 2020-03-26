<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/22
 * Time: 11:58 AM
 */

namespace app\common\modules\memberCart;

use app\common\exceptions\AppException;
use app\common\models\BaseModel;
use app\common\models\Member;
use app\common\models\MemberCart;
use app\common\modules\trade\models\Trade;
use app\common\services\Plugin;
use app\framework\Database\Eloquent\Collection;
use app\framework\Http\Request;
use app\frontend\modules\order\models\PreOrder;
use app\frontend\modules\order\services\OrderService;

class MemberCartCollection extends Collection
{
    /**
     * @var [MemberCart]
     */
    protected $items;
    private $validated = false;

    /**
     * 验证商品有效性
     * @throws AppException
     */
    public function validate()
    {
        if ($this->validated) {
            return true;
        }

        if ($this->unique('member_id')->count() != 1) {
            throw new AppException("操作无效,购物车记录属于{$this->unique('member_id')->count()}个用户");
        }
        $this->unique('goods_id')->each(function (MemberCart $memberCart) {

            if (isset($memberCart->goods->hasOnePrivilege)) {
                // 合并规格商品数量,并校验
                $total = $this->where('goods_id', $memberCart->goods_id)->sum('total');

                $memberCart->goods->hasOnePrivilege->validate($memberCart->member, $total);
            }
        });
        $this->each(function (Membercart $memberCart) {
            $memberCart->validate();
        });
        $this->validated = true;
    }

    /**
     * 载入管理模型
     * @return $this
     */
    public function loadRelations()
    {
        $with = ['goods' => function ($query) {
            $query->select(['id','uniacid','brand_id','type','status','display_order','title','thumb','thumb_url','sku','goods_sn','product_sn','market_price','price','cost_price','stock','reduce_stock_method','show_sales','real_sales','weight','has_option','is_new','is_hot','is_discount','is_recommand','is_comment','is_deleted','created_at','deleted_at','updated_at','comment_num','is_plugin','plugin_id','virtual_sales','no_refund','need_address','type2']);
        }, 'goods.hasOnePrivilege', 'goods.hasOneOptions', 'goods.hasManyGoodsDiscount', 'goods.hasOneGoodsDispatch', 'goods.hasOneSale', 'goodsOption'];
        $with = array_merge($with, \app\common\modules\shop\ShopConfig::current()->get('shop-foundation.member-cart.with'));
        if(is_array($with)){
            $this->expansionLoad($with);
        }
        $this->each(function (MemberCart $memberCart) {
            if (isset($memberCart->goodsOption)) {
                $memberCart->goodsOption->setRelation('goods', $memberCart->goods);
            }
        });
        return $this;
    }

    /**
     * 将购物车集合按groupId分组
     * @return static
     */
    public function groupByGroupId()
    {
        $groups = $this->groupBy(function (MemberCart $memberCart) {
            return $memberCart->getGroupId();
        });

        $groups->map(function (MemberCartCollection $memberCartCollection) {
            return $memberCartCollection;
        });
        return $groups;
    }

    /**
     * 获取交易对象
     * @param null $request
     * @return Trade|\Illuminate\Foundation\Application|mixed
     */
    public function getTrade($member = null, $request = null)
    {
        $request = $request ?: request();

        $trade = app(Trade::class);
        /**
         * @var Trade $trade
         */
        $trade->init($this, $member, $request);
        return $trade;
    }

    /**
     * 根据自身创建plugin_id对应类型的订单,当member已经实例化时传入member避免重复查询
     * @param Member|null $member
     * @param Plugin|null $plugin
     * @param Request $request
     * @return PreOrder|bool
     * @throws AppException
     * @throws \Exception
     */
    public function getOrder(Plugin $plugin = null, Member $member = null, $request = null)
    {
        $request = $request ?: request();
        if ($this->isEmpty()) {
            return false;
        }
        if (!isset($member)) {
            $member = $this->getMember();
        }
//        if ($member->uid != $this->getUid()) {
//            throw new AppException("用户({$member->uid})与购物车所属用户({$this->getUid()})不符");
//        }
        $this->validate();


        $orderGoodsCollection = OrderService::getOrderGoods($this);
        /**
         * @var PreOrder $order
         */
        $app = $plugin && $plugin->app()->bound('OrderManager') ? $plugin->app() : app();

        $order = $app->make('OrderManager')->make('PreOrder');

        $order->init($member, $orderGoodsCollection, $request);

        return $order;
    }

    /**
     * 所属uid
     * @return mixed
     */
    public function getUid()
    {
        return $this->first()->member_id;
    }

    /**
     * 所属用户对象
     * @return Member
     */
    public function getMember()
    {
        return $this->first()->member;
    }

    /**
     * 第一个调购物车记录
     * @param callable|null $callback
     * @param null $default
     * @return MemberCart
     */
    public function first(callable $callback = null, $default = null)
    {
        return parent::first($callback, $default);
    }

    /**
     * @return null
     * @throws AppException
     */
    public function getPlugin()
    {
        $this->validate();
        return $this->first()->goods->getPlugin();
    }
}