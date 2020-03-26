<?php

namespace app\common\modules\address\services;

use app\backend\modules\refund\models\RefundApply;
use app\backend\modules\refund\services\RefundOperationService;
use app\common\events\order\AfterOrderRefundedEvent;
use app\common\exceptions\AdminException;
use app\common\models\Address;
use app\common\models\finance\Balance;
use app\common\models\PayType;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use app\common\services\PayFactory;
use app\frontend\modules\finance\services\BalanceService;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/10
 * Time: 下午4:29
 */
class AddressService
{

    public static function makeTree()
    {
        ini_set("max_execution_time", "120");
        $provinces = Address::where('level', 1)->get();
        $cities = Address::where('level', 2)->get();
        $districts = Address::where('level', 3)->get();


        $cities->each(function ($c) use ($districts) {
            // 获取对应的县 转成数组
            $ds = $districts->where('parentid', $c->id)->map(function ($d) {
                return [
                    'n' => $d->areaname,
                    'v' => $d->id,
                ];
            })->values()->toArray();
            //$dArr =
            // 城市
            $c['c'] = $ds;
        });
        $provinces->each(function ($p) use ($cities) {
            $p->c = $cities->where('parentid', $p->id)->map(function ($c) {
                return [
                    'n' => $c->areaname,
                    'v' => $c->id,
                    'c' => $c->c,
                ];
            })->values()->toArray();
        });
        $result = $provinces->map(function ($p) {
            return [
                'n' => $p->areaname,
                'v' => $p->id,
                'c' => $p->c,
            ];
        });
        echo '/* ydui-district v1.1.0 by YDCSS (c) 2017 Licensed ISC */
!function(){var district='.$result->toJson().';if(typeof define==="function"){define(district)}else{window.YDUI_DISTRICT=district}}();';

    }

}