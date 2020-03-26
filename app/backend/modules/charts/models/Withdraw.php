<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/11/14
 * Time: 9:56
 */

namespace app\backend\modules\charts\models;


use app\common\scopes\UniacidScope;

class Withdraw extends \app\common\models\Withdraw
{
    public static function boot()
    {
        parent::boot();
        self::addGlobalScope(new UniacidScope);
    }

    public function scopeSearch($query, $search)
    {
        if ($search['is_time']) {
            $query->whereBetween('created_at',[strtotime($search['time']['start']), strtotime($search['time']['end'])]);
        }
        if (!empty($search['type'])) {
            $query->whereType($search['type']);
        }
        return $query;
    }

    public static function getTypes()
    {
        $configs = \app\backend\modules\income\Income::current()->getItems();

        return $configs;
    }


}