<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/23
 * Time: 下午6:01
 */

namespace app\common\models;



use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;


class MemberGroup extends BaseModel
{
    use SoftDeletes;

    protected $table = 'yz_member_group';


    /*public static function boot()
    {
        parent::boot();
        static::addGlobalScope('uniacid',function (Builder $builder) {
            return $builder->uniacid();
        });
    }*/

    public function scopeRecords($query)
    {
        return $query->uniacid()->select('id','group_name');
    }



    /**
     * Get member group information by groupId
     *
     * @param array $data
     *
     * @return 1 or 0
     * */
    protected static function getMemberGroupByGroupID($groupId)
    {
        return static::uniacid()->where('id', $groupId)->first(1)->toArray();
    }

    /**
     * 获取默认组
     *
     * @return mixed
     */
    public static function getDefaultGroupId()
    {
        return self::select('id')
            ->uniacid()
            ->where('is_default', 1);
    }

}
