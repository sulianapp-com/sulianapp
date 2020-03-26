<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/10
 * Time: 09:50
 */
class GoodsSeeder extends \Illuminate\Database\Seeder
{
    protected $oldGoodsTable = 'sz_yi_goods';
    protected $oldGoodsParamTable = 'sz_yi_goods_param';
    protected $oldGoodsSpecTable = 'sz_yi_goods_spec';
    protected $oldGoodsSpecItemTable = 'sz_yi_goods_spec_item';
    protected $oldGoodsOptionTable = 'sz_yi_goods_option';

    protected $goodsTable = 'yz_goods';
    protected $goodsParamTable = 'yz_goods_param';
    protected $goodsSpecTable = 'yz_goods_spec';
    protected $goodsSpecItemTable = 'yz_goods_spec_item';
    protected $goodsOptionTable = 'yz_goods_option';
    protected $goodsCategoryTable = 'yz_goods_category';


    public function run()
    {
        return;
        if (!Schema::hasTable($this->oldGoodsTable)) {
            echo $this->oldGoodsTable." 不存在 跳过\n";
            return;
        }
        $dataList = \Illuminate\Support\Facades\DB::table($this->goodsTable)->get();
        if($dataList->isNotEmpty()){
            echo "{$this->goodsTable}已经有数据了跳过\n";
            return ;
        }

        $list =  \Illuminate\Support\Facades\DB::table($this->oldGoodsTable)->get();
        if($list){

        }
    }
}