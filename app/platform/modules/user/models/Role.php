<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 下午5:03
 */

namespace app\platform\modules\user\models;

use app\common\models\BaseModel;

class Role extends BaseModel
{
    protected $table='yz_admin_roles';
    //
    public function permissions()
    {
        return $this->belongsToMany(Permission::class,'yz_admin_permission_role','role_id','permission_id');
    }
    public function users()
    {
        return $this->belongsToMany(AdminUser::class,'yz_admin_role_user','role_id','user_id');
    }
    //给角色添加权限
    public function givePermissionTo($permission)
    {
        return $this->permissions()->save($permission);
    }


}