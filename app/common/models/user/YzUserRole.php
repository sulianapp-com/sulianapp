<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 04/03/2017
 * Time: 14:25
 */

namespace app\common\models\user;


use app\common\models\BaseModel;

class YzUserRole extends BaseModel
{
    public $table = 'yz_user_role';

    public $timestamps = false;

    protected $guarded = [''];

    public function role()
    {
        return $this->hasOne('app\common\models\user\YzRole','id','role_id');
    }

    /*
     * 每一个角色 role_id 有多个权限，type = 2 的为角色权限，item_id 此时为角色ID
     * */
    public function permissions()
    {
        return $this->hasMany('app\common\models\user\YzPermission','item_id','role_id')
            ->where('type','=', YzPermission::TYPE_ROLE);
    }

    /*
     * 通过 user_id 值移除此值相等数据
     * user_id 为 user 表唯一主键，每一个 user_id 可以有一个角色 role_id 为角色ID
     *
     * @params int $userId
     *
     * @return object */
    public static function removeDataByUserId($userId)
    {
        return static::where('user_id', $userId)->delete();
    }



}
