<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;

class YzDispatchSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_dispatch';
    protected $newTable = 'yz_dispatch';

    public function run()
    {
        return;
        $newList = \Illuminate\Support\Facades\DB::table($this->newTable)->get();
        if ($newList->isNotEmpty()) {
            echo "yz_goods_share 已经有数据了跳过\n";
            return;
        }
        $list = \Illuminate\Support\Facades\DB::table($this->oldTable)->get();
        if ($list) {
            foreach ($list as $v) {
                \Illuminate\Support\Facades\DB::table($this->newTable)->insert([
                    'uniacid' => $v['uniacid'],
                    'dispatch_name' => $v['dispatchname'],
                    'display_order' => $v['displayorder'],
                    'first_weight_price' => $v['firstprice'],
                    'another_weight_price' => $v['secondprice'],
                    'first_weight' => $v['firstweight'],
                    'another_weight' => $v['secondweight'],
                    'areas' => $v['areas'],
                    'carriers' => $v['carriers'],
                    'enabled' => $v['enabled'],
                    'is_default' => $v['isdefault'],
                    'calculate_type' => $v['calculatetype'],
                    'first_piece' => $v['firstnum'],
                    'another_piece' => $v['secondnum'],
                    'first_piece_price' => $v['firstnumprice'],
                    'another_piece_price' => $v['secondnumprice'],

                ]);

            }
        }
    }

}