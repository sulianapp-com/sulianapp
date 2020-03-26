<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 07/03/2017
 * Time: 10:40
 */

namespace app\common\models\user;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class YzRole extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_role';

    const ROLE_ENABLE = 2;

    const ROLE_DISABLE = 1;

    /**
     *  定义字段名
     * 可使
     * @return array */
    public  function atributeNames() {
        return [
            'name'=> '角色名称',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public  function rules() {
        return [
            'name' => 'required',
        ];
    }

    //这个好像没有用，应该可以删除，待确认
    public function UserPermistions()
    {
        return $this->hasMany('app\common\models\user\YzPermission');
    }

    public function roleUser()
    {
        return $this->hasMany('app\common\models\user\YzUserRole', 'role_id');
    }

    public function rolePermission()
    {
        return $this->hasMany('app\common\models\user\YzPermission', 'item_id');
    }

    /**
     * @param int $pageSize
     * @return object
     */
    public static function getPageList($pageSize,$search)
    {
        $query = static::uniacid();
        if ($search['keyword']) {
            $query = $query->where('name', 'like', $search['keyword'] . '%');
        }
        if ($search['status']) {
            $query = $query->where('status', $search['status']);
        }
        return $query->with(['roleUser'])->paginate($pageSize);
    }

    public static function getRolelistToUser()
    {
        return static::select('id', 'name')->uniacid()->where('status', '=', "2")->get()->toArray();
    }

    /**
     * Get full role information and role permissions By roleId
     *
     * @param int $roleId
     * @return object
     */
    public static function getRoleById($roleId)
    {
        return static::where('id', $roleId)
            ->with(['rolePermission' => function($query) {
                return $query->select('id', 'item_id','permission')->where('type', '=', YzPermission::TYPE_ROLE);
            }])
            ->first();
    }

    /**
     * @param int $roleId
     * @return \mysqli_result
     */
    public static function deleteRole($roleId)
    {
        return static::where('id', $roleId)->delete();
    }


}