<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;
class YzGoodsDispatchSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_goods';
    protected $newTable = 'yz_goods_dispatch';
    
    public function run()
    {
        return;
        if (!Schema::hasTable($this->oldTable)) {
            echo $this->oldTable." 不存在 跳过\n";
            return;
        }
        $newList = \Illuminate\Support\Facades\DB::table($this->newTable)->get();
        if($newList->isNotEmpty()){
            echo "yz_goods_share 已经有数据了跳过\n";
            return ;
        }
        $list =  \Illuminate\Support\Facades\DB::table($this->oldTable)->get();
        if($list) {
            foreach ($list as $v) {
                \Illuminate\Support\Facades\DB::table($this->newTable)->insert([
                    'goods_id'=> $v['id'],
                    'dispatch_type'=> $v['dispatchtype'],
                    'dispatch_price'=> $v['dispatchprice'],
                    'dispatch_id'=> $v['dispatchid'],
                    'is_cod'=> $v['cash'],
                ]);

            }
        }
    }

}