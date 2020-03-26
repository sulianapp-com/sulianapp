<?php

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 上午9:54
 */
use app\common\models\Member;
use Illuminate\Database\Seeder;

class YzNoticeSeeder extends Seeder
{
    protected $oldTable = 'sz_yi_goods';
    protected $newTable = 'yz_goods_notices';
    
    public function run()
    {
        return;
        if (!Schema::hasTable($this->oldTable)) {
            echo $this->oldTable." 不存在 跳过\n";
            return;
        }
        $newList = \Illuminate\Support\Facades\DB::table($this->newTable)->get();
        if($newList->isNotEmpty()){
            echo "yz_goods_notices 已经有数据了跳过\n";
            return ;
        }
        $list =  DB::table($this->oldTable)->get();
        if($list) {
            foreach ($list as $v) {
                $uid = \app\frontend\modules\member\models\McMappingFansModel::getUId($v['noticeopenid']);
                $noticetype = explode(",", $v['noticetype']);
                foreach ($noticetype as $item) {
                    DB::table($this->newTable)->insert([
                        'goods_id'=> $v['id'],
                        'uid'=> $uid,
                        'type'=> $item,
                    ]);
                }

            }
        }
    }

}