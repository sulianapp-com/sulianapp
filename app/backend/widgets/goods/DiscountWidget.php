<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets\goods;


use app\common\components\Widget;
use app\backend\modules\goods\models\Discount;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberGroup;

class DiscountWidget extends Widget
{

    public function run()
    {
        $discounts = new Discount();
        $discountValue = array();
        if ($this->goods_id && Discount::getList($this->goods_id)) {
            $discounts = Discount::getList($this->goods_id);
            foreach ($discounts as $key => $discount) {
                $discountValue[$discount['level_id']] =   $discount['discount_value'];
            }
        }
        $levels = MemberLevel::getMemberLevelList();
        $levels = array_merge($this->defaultLevel(),$levels);
        $groups = MemberGroup::getMemberGroupList();
        return view('goods.widgets.discount', [
            'discount' => $discounts->toArray(),
            'discountValue' => $discountValue,
            'levels' => $levels,
            'groups' => $groups
        ])->render();
    }

    private function defaultLevel()
    {
        return [
            '0'=> [
                'id' => "0",
                'level_name' => \Setting::get('shop.member.level_name') ?: '普通会员'
            ],
        ];
    }
}