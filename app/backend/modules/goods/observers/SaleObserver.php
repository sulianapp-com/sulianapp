<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 9:04
 */

namespace app\backend\modules\goods\observers;


use Illuminate\Database\Eloquent\Model;

class SaleObserver extends \app\common\observers\BaseObserver
{
    public function saving( $model)
    {

//        if (!empty($model->id) &&$model->isDirty()) {
//            (new \app\common\services\operation\SaleLog($model, 'update'));
//        }
    }

    public function updating( $model)
    {
        (new \app\common\services\operation\SaleLog($model, 'update'));
    }
}