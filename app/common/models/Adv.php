<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/23
 * Time: 下午8:26
 */

namespace app\common\models;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Adv extends BaseModel
{
    public $table = 'yz_adv';
    public $timestamps = true;
    static protected $needLog = true;
    protected $guarded = [''];
    protected $casts = [
        'advs' => 'json'
    ];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}