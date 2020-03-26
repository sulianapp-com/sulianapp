<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;
class YzGoodsPrivilegeSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_goods';
    protected $newTable = 'yz_goods_privilege';
    
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
                    'show_levels'=> $v['showlevels'],
                    'show_groups'=> $v['showgroups'],
                    'buy_levels'=> $v['buylevels'],
                    'buy_groups'=> $v['buygroups'],
                    'once_buy_limit'=> $v['maxbuy'],
                    'total_buy_limit'=> $v['usermaxbuy'],
                    'time_begin_limit'=> $v['timestart'],
                    'time_end_limit'=> $v['timeend'],
                    'enable_time_limit'=> 0,
                ]);

            }
        }
    }

}