<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 15:31
 */

namespace app\backend\modules\goods\observers;


use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;

class GoodsDispatchObserver extends BaseObserver
{
    public function saving(Model $model)
    {

//        if ($model->isDirty()) {
//            (new \app\common\services\operation\GoodsDispatchLog($model, 'update'));
//        }
    }

    public function updating(Model $model)
    {
    }

    public function updated(Model $model)
    {
        (new \app\common\services\operation\GoodsDispatchLog($model, 'update'));

    }
}