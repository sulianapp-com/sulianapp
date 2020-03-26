<?php
namespace app\backend\modules\user\observers;

use app\backend\modules\user\services\PermissionService;
use app\common\models\user\UniAccountUser;
use app\common\models\user\User;
use app\common\models\user\UserPermission;
use app\common\models\user\UserProfile;
use app\common\models\user\YzPermission;
use app\common\models\user\YzUserRole;
use app\common\observers\BaseObserver;
use app\common\traits\MessageTrait;
use Illuminate\Database\Eloquent\Model;


/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/17
 * Time: 上午10:56
 */
class UserObserver extends BaseObserver
{

    //todo 目前所有完成状态的事件都不能返回错误，需要做事物处理

    use MessageTrait;

    public function saving(Model $model) {
        //验证操作员简介数据
        $profileModel = UserProfile::getProfileByUid($model->uid) ?: new UserProfile();

        $profileModel->setRawAttributes($model->widgets['profile']);
        $validator = $profileModel->validator();
        if ($validator->fails()) {
            $this->error($validator->messages());
            return false;
        }
    }

    public function saved(Model $model) {
        //操作员简介写入 或 修改操作员简介
        $profileModel = UserProfile::getProfileByUid($model->uid) ?: new UserProfile();

        $profileModel->fill($model->widgets['profile']);
        $profileModel->uid = $model->uid;
        if (config('app.framework') != 'platform') {
            $profileModel->createtime = time();
        }
        if (!$profileModel->save()) {
            dd("操作员简介写入失败,请重试！！");
            $this->error("操作员简介写入失败,请重试！！");
            return false;
        }

        //操作员角色写入 或 修改 没有主键的表，删除原数据重新添加
        YzUserRole::removeDataByUserId($model->uid);

        $yzUserRoleModel = new YzUserRole();

        $yzUserRoleModel->user_id = $model->uid;
        $yzUserRoleModel->role_id = $model->widgets['role_id'] ?: '0';
        if (!$yzUserRoleModel->save()) {
            dd("操作员角色关联写入失败,请重试！！");
            $this->error("操作员角色关联写入失败,请重试！！");
            return false;
        }

        //操作员权限写入 或 修改， 修改时需注意：挂件中的 permission 需要去除角色权限
        //同时，目前采用删除操作员原权限，写入新权限做法
        YzPermission::deleteUserPermissionByUserId($model->uid);

        if ($model->widgets['perms']) {
            $permissions = (new PermissionService())->addedToPermission($model->widgets['perms'], YzPermission::TYPE_USER, $model->uid);
            if (!YzPermission::insertYzPermission($permissions)) {
                dd("写入操作员权限失败，请重新编辑！！");
                $this->error("写入操作员权限失败，请重新编辑！！");
                return false;
            }
        }
    }

    public function updating(Model $model) {}

    public function updated(Model $model) {}

    public function creating(Model $model) {}

    public function created(Model $model)
    {
        //框架操作员属性写入，只有创建后才会写入，商城不支持修改
        $uniAccountUserModel = new UniAccountUser();

        $accountData = array(
            'uid'       => $model->uid,
            'role'      => 'operator',
            'rank'      => '0',
            'uniacid'   => \YunShop::app()->uniacid
        );
        $uniAccountUserModel->fill($accountData);
        if (!$uniAccountUserModel->save()) {
            dd("操作员user写入失败,请重试！！");
            $this->error("操作员user写入失败,请重试！！");
            return false;
        }

        //框架操作员权限写入，只有创建后才会写入，商城不支持修改
        //todo 因为微擎新版勾选模块操作权限导致访问模块提示权限不足，暂时注释掉 2017-09-18
        /*$userPermissionModel = new UserPermission();

        $permissionData = array(
            'uid'       => $model->uid,
            'type'      => 'yun_shop',
            'permission'=> 'all',
            'uniacid'   => \YunShop::app()->uniacid,
            'url'       => ''
        );
        $userPermissionModel->fill($permissionData);
        if (!$userPermissionModel->save()) {
            dd("操作员user写入失败,请重试！！");
            $this->error("操作员user写入失败,请重试！！");
            return false;
        }*/

    }

    public function deleting(Model $model) {}

    public function deleted(Model $model) {}

    public function restoring(Model $model) {}

    public function restored(Model $model) {}
}