<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/17
 * Time: 22:43
 */

namespace app\common\observers\balance;


use app\common\events\finance\BalanceChangeEvent;
use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;

class BalanceChangeObserver extends BaseObserver
{
    public function created(Model $model)
    {
        event(new BalanceChangeEvent($model));
    }
}