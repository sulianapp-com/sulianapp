<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/11/1
 * Time: 9:52 AM
 */

namespace app\backend\modules\role\controllers;


use app\backend\modules\role\models\RoleModel;
use app\common\components\BaseController;
use app\common\models\user\YzPermission;

class PermissionController extends BaseController
{
    /**
     * @var RoleModel
     */
    private $roleModel;


    public function preAction()
    {
        parent::preAction();
        $this->roleModel = $this->roleModel();
    }

    public function index()
    {
        $permission = $this->rolePermission();

        return $this->successJson('ok', $permission);
    }

    /**
     * @return array
     */
    private function rolePermission()
    {
        $permission = [];
        $permissionModels = $this->rolePermissionModels();
        if ($permissionModels->isEmpty()) {
            return $permission;
        }
        foreach ($permissionModels as $key => $permissionModel) {
            $permission[] = $permissionModel->permission;
        }
        return $permission;
    }

    /**
     * @return YzPermission
     */
    private function rolePermissionModels()
    {
        return $this->roleModel->permission()->select('permission')->where('type', YzPermission::TYPE_ROLE)->get();
    }

    /**
     * @return RoleModel|\Illuminate\Http\JsonResponse
     */
    private function roleModel()
    {
        $role_id = $this->roleId();

        $roleModel = RoleModel::find($role_id);
        if (!$roleModel) {
            return $this->errorJson('角色不存在或已删除');
        }
        return $roleModel;
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    private function roleId()
    {
        $role_id = \YunShop::request()->role_id;
        if (!$role_id) {
            return $this->errorJson('参数错误');
        }
        return (int)$role_id;
    }

}
