<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 15:30
 */

namespace app\backend\modules\goods\observers;


use Illuminate\Database\Eloquent\Model;

class DiscountObserver extends \app\common\observers\BaseObserver
{
    public function saving(Model $model)
    {

        if ($model->isDirty()) {
            (new \app\common\services\operation\DiscountLog($model, 'update'));
        }
    }
}