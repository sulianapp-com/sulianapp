<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use Illuminate\Database\Seeder;
//use Illuminate\Database\Schema\Blueprint;


class YzStreetSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_street';
    protected $newTable = 'yz_street';
    public function run()
    {
        return;
        if (!Schema::hasTable($this->oldTable)) {
            echo $this->oldTable." 不存在 跳过\n";
            return;
        }
        $newList = \Illuminate\Support\Facades\DB::table($this->newTable)->get();
        if($newList->isNotEmpty()){
            echo "yz_street 已经有数据了跳过\n";
            return ;
        }
        $list =  \Illuminate\Support\Facades\DB::table($this->oldTable)->get();
        if($list) {
            foreach ($list as $v) {
                \Illuminate\Support\Facades\DB::table($this->newTable)->insert([
                    'id'=>$v['id'],
                    'areaname'=>$v['areaname'],
                    'parentid'=>$v['parentid'],
                    'level'=>$v['level']
                ]);
            }
        }
    }

}