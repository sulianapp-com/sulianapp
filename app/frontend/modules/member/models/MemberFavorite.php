<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 下午6:35
 */

namespace app\frontend\modules\member\models;



class MemberFavorite extends \app\common\models\MemberFavorite
{
    public function goods()
    {
        return $this->hasOne(\app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification'),'id','goods_id');
    }
    /*
     * 通过主键ID查找
     *
     * @params int $favoriteId
     *
     * @return object*/
    public static function getFavoriteById($favoriteId)
    {
        return static::uniacid()->where('id', $favoriteId)->first();
    }

    /*
     * 通过商品ID、会员ID查找
     *
     * @params int $goodsId
     * @params int $memberId
     *
     * @return object */
    public static function getFavoriteByGoodsId($goodsId, $memberId)
    {
        return static::uniacid()->where('goods_id', $goodsId)->where('member_id', $memberId)->first();
    }

    /*
     * 获取会员收藏列表
     *
     * @params int $goodsId
     * @params int $memberId
     *
     * @return object */
    public static function getFavoriteList($memberId)
    {
//        return static::select('id', 'goods_id', 'created_at')->uniacid()->where('member_id', $memberId)
//            ->with(['goods' => function($query) {
//                return $query->select('id', 'thumb', 'price', 'market_price', 'title');
//            }])
//            ->orderBy('created_at', 'desc')->get()->toArray();
        $data = static::select('id', 'goods_id', 'created_at')->uniacid()->where('member_id', $memberId)
            ->with(['goods' => function($query) {
                return $query->select('id', 'thumb', 'price', 'market_price', 'title');
            }])
            ->orderBy('created_at', 'desc')->get();
        foreach ($data as &$itme){
            $itme['vip_level_status'] = $itme->goods->vip_level_status;
        }
        return $data->toArray();
    }
    /**
     * remove collection
     *
     * @param array $data
     *
     * @return 1 or 0
     * */
    public static function destroyFavorite($favoriteId)
    {
        return static::uniacid()->where('id', $favoriteId)->delete();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'goods_id'  => '商品ID不能为空',
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'goods_id'  => 'required|integer',
        ];
    }
}
