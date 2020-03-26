<?php
namespace app\backend\modules\goods\listeners;
use app\common\models\Goods;
use app\common\models\UniAccount;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/4/10 0010
 * Time: 下午 4:12
 */
class LimitBuy
{
    use DispatchesJobs;

    public function handle()
    {
        $uniAccount = UniAccount::getEnable() ?: [];

        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;

            $goods_model = Goods::uniacid()
                ->whereHas('hasOneGoodsLimitbuy', function ($query) {
                    return $query->uniacid();
                })
                ->with(['hasOneGoodsLimitbuy'=> function ($query) {
                    return $query->select('goods_id', 'end_time');
                }])
                ->get();
            $current_time = time();

            foreach ($goods_model as $key => $item) {
                $end_time = $item->hasOneGoodsLimitbuy->end_time;
                if ($end_time < $current_time && $item->hasOneGoodsLimitbuy->status == 1) {
                    $item->status = 0;
                    $item->save();
                }
            }
        }
    }


    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Limit-buy', '*/10 * * * * *', function() {
                $this->handle();
                return;
            });
        });
    }
}