<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;
class YzSaleSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_goods';
    protected $newTable = 'yz_goods_sale';
    
    public function run()
    {
        return;
        if (!Schema::hasTable($this->oldTable)) {
            echo $this->oldTable." 不存在 跳过\n";
            return;
        }
        $newList = \Illuminate\Support\Facades\DB::table($this->newTable)->get();
        if($newList->isNotEmpty()){
            echo "yz_goods_sale 已经有数据了跳过\n";
            return ;
        }
        $list =  \Illuminate\Support\Facades\DB::table($this->oldTable)->get();
        if($list) {
            foreach ($list as $v) {
                \Illuminate\Support\Facades\DB::table($this->newTable)->insert([
                    'goods_id'=> $v['id'],
                    'max_point_deduct'=> $v['deduct'] * 100,
                    'max_balance_deduct'=> $v['deduct2'] * 100,
                    'is_sendfree'=> $v['issendfree'],
                    'ed_num'=> $v['ednum'],
                    'ed_money'=> $v['edmoney'],
                    'ed_areas'=> $v['edareas'],
                    'point'=> $v['credit'] * 100,
                    'bonus'=> $v['redprice'] * 100
                ]);

            }
        }
    }

}