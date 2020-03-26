<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/1
 * Time: 下午4:37
 */

namespace app\frontend\modules\member\services;


use app\common\exceptions\AppException;
use app\frontend\models\Member;
use \app\frontend\models\MemberCart;
use Illuminate\Support\Collection;

class MemberCartService
{
    public static function clearCartByIds($ids)
    {
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        if (!is_array($ids)) {
            throw new AppException('购物车ID格式不正确');
        }
        return app('OrderManager')->make('MemberCart')->uniacid()->whereIn('id', $ids)->delete();
    }

    /**
     * @param $params
     * @return mixed
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    public static function newMemberCart($params)
    {
        if (!isset($params['total']) || $params['total'] <= 0) {
            // 数量默认1
            $params['total'] = 1;
        }

        if (!isset($params['member_id'])) {
            $params['member_id'] = Member::current()->uid;
        }

        $cart = app('OrderManager')->make('MemberCart', $params);
        if($cart->member_id == Member::current()->uid){
            $cart->setRelation('member', Member::current());
        }
        return $cart;
    }

}