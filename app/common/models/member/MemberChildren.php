<?php
/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/11/20
 * Time: 22:57
 */

namespace app\common\models\member;


use app\common\models\BaseModel;
use app\common\models\Order;
use Illuminate\Database\Eloquent\Builder;
use app\common\models\Member;

class MemberChildren extends BaseModel
{
    public $table = 'yz_member_children';

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }

    /**
     *会员  1:1 关系
     *
     * @return mixed
     */
    public function hasOneMember()
    {
        return $this->hasOne(Member::class, 'uid', 'child_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class,'uid','child_id');
    }
}