<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2018/12/30
 * Time: 5:02 PM
 */

namespace app\common\observers\income;


use app\common\events\income\IncomeCreatedEvent;
use app\common\models\Income;
use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;

class IncomeObserver extends BaseObserver
{
    public function created(Model $model)
    {
        /**
         * @var Income $model
         */
        event(new IncomeCreatedEvent($model));
    }
}
