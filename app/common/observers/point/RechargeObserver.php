<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/22
 * Time: 下午3:30
 */

namespace app\common\observers\point;


use app\common\observers\BaseObserver;
use app\common\services\point\RechargeService;
use Illuminate\Database\Eloquent\Model;

class RechargeObserver extends BaseObserver
{
    public function created(Model $model)
    {
        (new RechargeService($model))->tryRecharge();
    }
}
