<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 07/03/2017
 * Time: 16:13
 */

namespace app\backend\modules\user\controllers;


use app\backend\modules\user\services\PermissionService;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\user\YzPermission;
use app\common\models\user\YzRole;

class RoleController extends BaseController
{

    //todo 页面功能逻辑优化，搜索功能完善
    /**
     * 角色列表
     */
    public function index()
    {
        $pageSize = '10';
        $search = \YunShop::request()->search;
        $roleList = YzRole::getPageList($pageSize,$search);

        $pager = PaginationHelper::show($roleList->total(), $roleList->currentPage(), $roleList->perPage());
        //dd($roleList->items());
        return view('user.role.index',[
            'pager'     => $pager,
            'roleList'  => $roleList,
            'search'    => $search
        ])->render();
    }



    /**
     * 创建角色
     */
    public function store()
    {
        $roleModel = new YzRole();

        $requestRole = \YunShop::request()->YzRole;
        //dd($requestRole);
        if ($requestRole) {
            //将数据赋值到model
            $roleModel->setRawAttributes($requestRole);
            //其他字段赋值
            $roleModel->uniacid = \YunShop::app()->uniacid;

            //字段检测
            $validator = $roleModel->validator($roleModel->getAttributes());
            //dd($validator->messages());
            if ($validator->fails()) {
                //dd("角色数据验证出错");
                $this->error($validator->messages()); 
            }else{
                if ($roleModel->save()) {
                    $requestPermission = \YunShop::request()->perms;
                    //数据处理
                    if ($requestPermission) {
                        //dd(1);
                        $data = [];
                        foreach ($requestPermission as $key => $value) {
                            $data[$key] = array(
                                'type'      => YzPermission::TYPE_ROLE,
                                'item_id'   => $roleModel->id,
                                'permission' => $value
                            );
                            $validator = (new YzPermission)->validator($data);
                            if ($validator->fails()) {
                                dd("权限数据验证失败");
                                $this->error($validator->message());
                            }
                        }
                        $result = YzPermission::insertYzPermission($data);
                        if (!$result) {
                            //删除刚刚添加的角色
                            YzRole::deleteRole($roleModel->id);
                            $this->error('权限数据写入出错，请重试！');
                        }
                    }
                    return $this->message('添加角色成功', Url::absoluteWeb('user.role.index'));
                } else {
                    $this->error('角色数据写入出错，请重试！');
                }
            }
        }
        $permissions = PermissionService::getPermission();
        return view('user.role.form',[
            'role'          => $roleModel,
            'permissions'   =>$permissions,
            'userPermissions'=>[],
        ])->render();
    }

    /**
     * 修改角色
     */
    public function update()
    {
        $permissions = PermissionService::getPermission();
        $roleModel = YzRole::getRoleById(\YunShop::request()->id);
        //dd($role);
        $rolePermission = $roleModel->toArray();
        foreach ($rolePermission['role_permission'] as $key) {
            $rolePermissions[] = $key['permission'];
        }
        if(empty($rolePermissions)) {
            $rolePermissions = [];
        }

        $requestRole = \YunShop::request()->YzRole;
        if ($requestRole) {
            $roleModel->setRawAttributes($requestRole);
            $validator = $roleModel->validator($roleModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($roleModel->save()) {
                    //return $this->message("更新角色成功");
                    \Cache::flush();
                    $requestPermission = \YunShop::request()->perms;
                    if ($requestPermission) {
                        //dd(1);
                        $data = [];
                        foreach ($requestPermission as $key => $value) {
                            $data[$key] = array(
                                'type'      => YzPermission::TYPE_ROLE,
                                'item_id'   => \YunShop::request()->id,
                                'permission' => $value
                            );
                            $validator = (new YzPermission)->validator($data);
                            if ($validator->fails()) {
                                dd("权限数据验证失败");
                                $this->error($validator->message());
                            }
                        }
                        //删除原权限数据，更新数据储存
                        YzPermission::deleteRolePermission(\YunShop::request()->id);
                        $result = YzPermission::insertYzPermission($data);
                        if (!$result) {
                            //删除刚刚添加的角色
                            YzRole::deleteRole($roleModel->id);
                            $this->error('角色更新成功，权限数据写入出错，请重新编辑权限！');
                        } else {
                            return $this->message('编辑角色成功', Url::absoluteWeb('user.role.index'));
                        }
                    } else {
                        YzPermission::deleteRolePermission(\YunShop::request()->id);
                    }
                    return $this->message('编辑角色成功', Url::absoluteWeb('user.role.index'));

                }
            }

        }
        //$this->save(\YunShop::request()->id, \YunShop::request()->YzRole);


        return view('user.role.form',[
            'role'=>$rolePermission,
            'permissions'=>$permissions,
            'userPermissions'=>$rolePermissions
        ])->render();
    }

    /**
     * 删除角色
     */
    public function destory()
    {
        $requestRole = YzRole::getRoleById(\YunShop::request()->id);
        if (!$requestRole) {
            return $this->message('未找到数据或已删除','','error');
        }
        $resultRole = YzRole::deleteRole(\YunShop::request()->id);
        if ($resultRole) {
            $resultPermission = YzPermission::deleteRolePermission(\YunShop::request()->id);
            if ($resultPermission) {
                return $this->message('删除角色成功。', Url::absoluteWeb('user.role.index'));
            }
            //是否需要怎么增加角色权限删除失败提示
        } else {
            return $this->message('数据写入出错，请重试！','','error');
        }
    }

}
