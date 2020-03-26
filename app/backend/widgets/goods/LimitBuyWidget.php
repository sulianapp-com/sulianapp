<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/20 0020
 * Time: 下午 3:18
 */

namespace app\backend\widgets\goods;

use app\common\components\Widget;
use app\common\models\goods\GoodsLimitBuy;

class LimitBuyWidget extends Widget
{
    public function run(){
        $goods_id = \YunShop::request()->id;
        $data= GoodsLimitBuy::getDataByGoodsId($goods_id);

        $starttime = strtotime('-1 month');
        $endtime = time();

        if (!empty($data)) {
            $starttime = $data->start_time;
            $endtime = $data->end_time;
        }
//        dd($starttime);
        return view('goods.widgets.limitbuy',[
                'data'  =>  $data,
                'starttime' => $starttime,
                'endtime' => $endtime
            ]
        )->render();

    }

}
