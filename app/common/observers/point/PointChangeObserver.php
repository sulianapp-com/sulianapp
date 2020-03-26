<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/17
 * Time: 23:07
 */

namespace app\common\observers\point;


use app\common\events\finance\PointChangeEvent;
use app\common\observers\BaseObserver;
use Illuminate\Database\Eloquent\Model;

class PointChangeObserver extends BaseObserver
{
    public function created(Model $model)
    {
        event(new PointChangeEvent($model));
    }
}