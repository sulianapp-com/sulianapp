<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午4:47
 */

namespace app\common\models;

use app\common\exceptions\AppException;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MemberCart
 * @package app\common\models
 * @property int plugin_id
 * @property int option_id
 * @property int total
 * @property int member_id
 * @property int goods_id
 * @property Goods goods
 * @property GoodsOption goodsOption
 * @property Member member
 */
class MemberCart extends BaseModel
{
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $table = 'yz_member_cart';

    public function isOption()
    {
        return !empty($this->option_id);
    }

    public function goods()
    {
        return $this->belongsTo(app('GoodsManager')->make('Goods'));
    }

    /**
     * 购物车验证
     * @throws AppException
     */
    public function validate()
    {
        if (!isset($this->goods)) {
            throw new AppException('(ID:' . $this->goods_id . ')未找到商品或已经删除');
        }

        //$this->getAllMemberCarts()->validate();
        //商品基本验证

        $this->goods->generalValidate($this->member, $this->total);

        if ($this->isOption()) {
            $this->goodsOptionValidate();
        } else {
            $this->goodsValidate();
        }

    }

    /**
     * 商品购买验证
     * @throws AppException
     */
    public function goodsValidate()
    {

        if (!$this->goods->stockEnough($this->total)) {
            throw new AppException('(ID:' . $this->goods_id . ')商品库存不足');
        }
    }

    /**
     * 规格验证
     * @throws AppException
     */
    public function goodsOptionValidate()
    {
        if (!$this->goods->has_option) {
            throw new AppException('(ID:' . $this->option_id . ')商品未启用规格');
        }
        if (!isset($this->goodsOption)) {
            throw new AppException('(ID:' . $this->option_id . ')未找到商品规格或已经删除');
        }
        if (!$this->goodsOption->stockEnough($this->total)) {
            throw new AppException('(ID:' . $this->goods_id . ')商品库存不足');
        }
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'uid');
    }

    /**
     * 获取购物车分组id
     * @return int
     */
    public function getGroupId()
    {
        // 判断是否拆单。如果开启商品拆单，则将每种商品拆成不同订单，不考虑规格数量.只拆商城的商品订单
        if ($this->goods->plugin_id == 0) {
            if (\Setting::get('shop.order.order_apart')) {
                return $this->goods_id;
            }
        }
        if (!$this->goods->getPlugin()) {
            return 0;
        }
        if (!$this->goods->getPlugin()->app()->bound(MemberCart::class)) {
            return 0;
        }
        return $this->goods->getPlugin()->app()->make(MemberCart::class, [$this->getAttributes()])->getGroupId();
    }
}