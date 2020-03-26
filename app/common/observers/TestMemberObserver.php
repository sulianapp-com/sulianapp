<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 24/02/2017
 * Time: 00:41
 */

namespace app\common\observers;


use app\common\events\Event;
use app\common\events\OrderCreatedEvent;
use Eloquent;

class TestMemberObserver extends BaseObserver
{

    public function creating(Eloquent $model)
    {
        $model->register_at = time();
    }

    public function created(Eloquent $model)
    {
        \Event::fire(new OrderCreatedEvent($model));

    }

    public function updating(Eloquent $model)
    {
        $model->ip = request()->ip();
    }

    public function updated(Eloquent $model)
    {
         //
    }


}