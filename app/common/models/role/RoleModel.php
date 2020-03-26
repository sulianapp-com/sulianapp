<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2018/11/1
 * Time: 10:00 AM
 */

namespace app\common\models\role;


use app\common\models\BaseModel;
use app\framework\Database\Eloquent\Builder;

class RoleModel extends BaseModel
{
    protected $table = 'yz_role';

    public function permission()
    {
        return $this->hasMany('app\common\models\user\YzPermission', 'item_id', 'id');
    }
}
