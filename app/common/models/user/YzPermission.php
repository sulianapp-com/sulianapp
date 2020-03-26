<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 02/03/2017
 * Time: 18:28
 */

namespace app\common\models\user;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class YzPermission extends BaseModel
{
    const TYPE_USER = 1;
    const TYPE_ROLE = 2;
    const TYPE_ACCOUNT = 3;

    public $table = 'yz_permission';

    public $timestamps = false;

    protected $guarded = [''];


    /*
     * 通过操作员ID获得操作员个人所有操作权限
     *
     * @param int $userId
     *
     * @return object */
    public static function gerUserPermissonByUserId($userId)
    {
        return static::where('type', '=', static::TYPE_USER)->where('item_id', $userId)->first();
    }

    /*
     * 添加操作员权限 或 添加角色权限 多条数据同时写入
     *
     * @param array $data
     *
     * @return result */
    public static function insertYzPermission(array $data = [])
    {
        return static::insert($data);
    }

    /*
     * 通过操作员ID删除操作员所有权限
     *
     * @param int $userId
     *
     * @return result */
    public static function deleteUserPermissionByUserId($userId)
    {
        return static::where('type', '=', static::TYPE_USER)->where('item_id', $userId)->delete();
    }

    /**
     * Delete role permissions by roleId
     *
     * @param int $roleId
     * @return \mysqli_result
     */
    public static function deleteRolePermission($roleId)
    {
        return static::where('type', '=', static::TYPE_ROLE)->where('item_id', $roleId)->delete();
    }


}
