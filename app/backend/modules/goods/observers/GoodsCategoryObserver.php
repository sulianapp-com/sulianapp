<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 16:02
 */

namespace app\backend\modules\goods\observers;


use Illuminate\Database\Eloquent\Model;

class GoodsCategoryObserver extends \app\common\observers\BaseObserver
{
    public function saving(Model$model)
    {
        (new \app\common\services\operation\GoodsCategoryLog($model, 'create'));
    }
}