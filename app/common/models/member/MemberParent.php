<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/10/26
 * Time: 5:12 PM
 */

namespace app\common\models\member;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class MemberParent extends BaseModel
{
    public $table = 'yz_member_parent';
    public $timestamps = true;
    protected $guarded = [''];

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}