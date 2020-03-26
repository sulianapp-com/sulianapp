<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午5:09
 */

namespace app\frontend\models;


use app\common\exceptions\AppException;
use app\frontend\modules\member\services\MemberService;
use app\frontend\modules\memberCart\MemberCartCollection;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class MemberCart
 * @package app\frontend\models
 * @property Goods goods
 * @property GoodsOption goodsOption

 */
class MemberCart extends \app\common\models\MemberCart
{
    protected $fillable = [];

    protected $guarded = ['id'];
    protected $hidden = ['member_id', 'uniacid'];

    /**
     * 根据购物车id数组,获取购物车记录数组
     * @param $cartIds
     * @return mixed
     */
    public static function getCartsByIds($cartIds)
    {
        if (!is_array($cartIds)) {
            $cartIds = explode(',', $cartIds);
        }
        $result = static::whereIn('id', $cartIds)
            ->get();

        return $result;
    }

    public function scopeCarts(Builder $query)
    {
        $query
            ->uniacid()
            ->with(['goods' => function ($query) {
                return $query->withTrashed()->select('id', 'thumb', 'price', 'market_price', 'title', 'deleted_at');
            }])
            ->with(['goodsOption' => function ($query) {
                return $query->select('id', 'title', 'thumb', 'product_price', 'market_price');
            }]);
    }


    public function goodsOption()
    {
        return $this->belongsTo(app('GoodsManager')->make('GoodsOption'), 'option_id');
    }

    /**
     * Get a list of members shopping cart through cart IDs
     *
     * @param array $cartIds
     *
     * @return array
     * */
    public static function getMemberCartByIds($cartIds)
    {
        return static::uniacid()->whereIn('id', $cartIds)->get()->toArray();
    }

    /**
     * Add merchandise to shopping cart
     *
     * @param array $data
     *
     * @return 1 or 0
     * */
    public static function storeGoodsToMemberCart($data)
    {
        //需要监听事件，购物车存在的处理方式
        return static::insert($data);
    }

    /**
     * 检测商品是否存在购物车
     *
     * @param array $data ['member_id', 'goods_id', 'option_id']
     *
     * @return self | false
     * */
    public static function hasGoodsToMemberCart($data)
    {
        $hasGoods = self::uniacid()
            ->where([
                'member_id' => $data['member_id'],
                'goods_id' => $data['goods_id'],
                'option_id' => $data['option_id']
            ])
            ->first();
        return $hasGoods ? $hasGoods : false;
    }

    /**
     * 定义字段名
     *
     * @return array
     */
    public function atributeNames()
    {
        return [
            'goods_id' => '未获取到商品',
            'total' => '商品数量不能为空',
        ];
    }

    /**
     * 字段规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'goods_id' => 'required',
            'total' => 'required',
        ];
    }

    /**
     * @return MemberCartCollection
     * @throws AppException
     */
    protected function getAllMemberCarts(){
        return (new MemberCartCollection(Member::current()->memberCarts));
    }


    public function goods()
    {
        return $this->belongsTo(app('GoodsManager')->make('Goods'));
    }
}
