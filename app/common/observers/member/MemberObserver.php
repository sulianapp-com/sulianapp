<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/2
 * Time: 17:19
 */

namespace app\common\observers\member;


use Illuminate\Database\Eloquent\Model;

class MemberObserver extends \app\common\observers\BaseObserver
{
    public function saving(Model $model) {

    }

    public function saved(Model $model) {}

    public function updating(Model $model)
    {
        (new \app\common\services\operation\ShopMemberLog($model, 'update'));
    }

    public function updated(Model $model) {}

    public function creating(Model $model) {}

    public function created(Model $model) {}

    public function deleting(Model $model) {}

    public function deleted(Model $model) {}

    public function restoring(Model $model) {}

    public function restored(Model $model) {}
}