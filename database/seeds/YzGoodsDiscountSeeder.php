<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;
use app\backend\modules\member\models\MemberLevel;

class YzGoodsDiscountSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_goods';
    protected $newTable = 'yz_goods_discount';

    public function run()
    {
        return;
        if (!Schema::hasTable($this->oldTable)) {
            echo $this->oldTable." 不存在 跳过\n";
            return;
        }
        $newList = \Illuminate\Support\Facades\DB::table($this->newTable)->get();
        if ($newList->isNotEmpty()) {
            echo "yz_goods_share 已经有数据了跳过\n";
            return;
        }
        $list = \Illuminate\Support\Facades\DB::table($this->oldTable)->get();

        $memberLevels = MemberLevel::getMemberLevelList();
        if ($list) {
            foreach ($list as $v) {
                $discounts = json_decode($v['discounts'], true);
                \Illuminate\Support\Facades\DB::table($this->newTable)->insert([
                    'goods_id' => $v['id'],
                    'level_discount_type' => $v['discounttype'],
                    'discount_method' => $v['discountway'],
                    'level_id' => 0,
                    'discount_value' => $discounts['default'],
                ]);
                foreach ($memberLevels as $m) {
                    if ($discounts['level' . $m['id']]) {
                        \Illuminate\Support\Facades\DB::table($this->newTable)->insert([
                            'goods_id' => $v['id'],
                            'level_discount_type' => $v['discounttype'],
                            'discount_method' => $v['discountway'],
                            'level_id' => $m['id'],
                            'discount_value' => $discounts['level' . $m['id']],
                        ]);
                    }
                }
            }
        }
    }
}