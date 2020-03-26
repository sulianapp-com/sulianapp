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
use app\common\models\user\User;
use app\common\models\user\YzRole;
use app\common\services\Utils;

class UserController extends BaseController
{
    const PageSize = 10;
    /*
     *  操作员分页列表
     **/
    public function index()
    {
        $records = User::records();

        $search = \YunShop::request()->search;
        if ($search) {
            $records = $records->search($search);
        }

        if (config('app.framework') == 'platform') {
            $userList = $records->orderBy('created_at','desc')->paginate(static::PageSize);
        } else {
            $userList = $records->orderBy('starttime','desc')->paginate(static::PageSize);
        }
        $pager = PaginationHelper::show($userList->total(), $userList->currentPage(), $userList->perPage());

        $roleList = YzRole::getRoleListToUser();
        return view('user.user.user', [
            'pager' => $pager,
            'roleList' => $roleList,
            'userList' => $userList,
            'search' => $search,
            'hasStore' => app('plugins')->isEnabled('store-cashier')
        ])->render();
    }

    /*
     *  添加操作员
     **/
    public function store()
    {
        $userModel = new User();

        $requestUser = \YunShop::request()->user;
        if ($requestUser) {

            $userData = $this->addedUserData($requestUser);

            if (config('app.framework') == 'platform') {
                $userData['owner_uid'] = 0;
            }

            $userModel->fill($userData);
            $userModel->widgets = \YunShop::request()->widgets;
            $userModel->widgets['perms'] = \YunShop::request()->perms;

            $validator = $userModel->validator();
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if (config('app.framework') == 'platform') {
                    $userModel->password = bcrypt($userModel->password);
                } else {
                    $userModel->password = $this->password($userModel->password, $userModel->salt);
                }
                if ($userModel->save()) {
                    return $this->message('添加操作员成功.', Url::absoluteWeb('user.user.index'));
                }
            }

        }
        //$permissions = \app\backend\modules\menu\Menu::current()->getItems();
        $roleList = YzRole::getRoleListToUser();
        return view('user.user.form',[
            'user'=>$userModel,
            'roleList' => $roleList,
            'permissions'=>PermissionService::getPermission(),
            'userPermissions'=>[],
        ])->render();
    }

    /*
     *  修改操作员
     **/
    public function update()
    {
        $userModel = User::getUserByid(\YunShop::request()->id);
        //dd($userModel);

        if (!$userModel) {
            return $this->message("未找到数据或已删除！", '', 'error');
        }
        $permissionService = new PermissionService();

        $userPermissions = $permissionService->handlePermission($userModel->permissions->toArray());

        $permissions = PermissionService::getPermission();
        $roleList = YzRole::getRoleListToUser();

        $rolePermissions = [];
        if ($userModel->userRole && $userModel->userRole->role) {
            $rolePermissions = $permissionService->handlePermission($userModel->userRole->permissions->toArray());
            $userPermissions = array_merge($rolePermissions, $userPermissions);
            //dd($permissionService->handlePermission($userModel->userRole->permissions->toArray()));
        }
        //dd($userPermissions);
        //修改 start
        $requestUser =\YunShop::request()->user;
        if ($requestUser) {
            //dd(\YunShop::request());
            $userModel->status = $requestUser['status'];
            if ($requestUser['password']) {
                $userModel->password = user_hash($requestUser['password'], $userModel->salt);
            }
            $userModel->widgets = \YunShop::request()->widgets;
            $userModel->widgets['perms'] = \YunShop::request()->perms;
            if ($userModel->save()) {
                $key = 'user.permissions.'.$userModel->uid;
                \Cache::forget($key);
                \Cache::forget('menu_list'.$userModel->uid);
                return $this->message('修改操作员成功.', Url::absoluteWeb('user.user.update', array('id' => $userModel->uid)));
            }
        }

        return view('user.user.form',[
            'user'          => $userModel,
            'roleList'      => $roleList,
            'permissions'   => $permissions,
            'rolePermission' => $rolePermissions,
            'userPermissions' => $userPermissions
        ])->render();
    }

    /*
     *  删除操作员
     * */
    public function destroy()
    {
        //@todo 需要完善删除关联表数据

        $requeserUser = User::getUserByid(\YunShop::request()->id);
        if (!$requeserUser) {
            return $this->message("未找到数据或以删除！", '', 'error');
        }

        $result = User::destroyUser(\YunShop::request()->id);
        if ($result) {
            return $this->message("删除操作员成功。", Url::absoluteWeb('user.user.index'));
        } else {
            return $this->message('数据写入出错，请重试1', '', 'error');
        }
    }

    /**
     * 附加的用户数据
     * @param string $data 需要储存的数据
     * @return string
     */
    private function addedUserData(array $data = [])
    {
        if (config('app.framework') == 'platform') {
            $data['lastvisit'] = time();
            $data['lastip'] = Utils::getClientIp();
            $data['joinip'] = Utils::getClientIp();
            $data['salt'] = randNum(8);
        } else {
            $data['joindate'] = $data['lastvisit'] = $data['starttime'] = time();
            $data['lastip'] = CLIENT_IP;
            $data['joinip'] = CLIENT_IP;
            $data['salt'] = $this->randNum(8);
        }

        return $data;
    }

    /**
     * 计算用户密码
     * @param string $passwordinput 输入字符串
     * @param string $salt 附加字符串
     * @return string
     */
    private function password($passwordinput, $salt)
    {
        $authkey = \YunShop::app()->config['setting']['authkey'];
        $passwordinput = "{$passwordinput}-{$salt}-{$authkey}";
        return sha1($passwordinput);
    }

    /**
     * 获取随机字符串
     * @param number $length 字符串长度
     * @param boolean $numeric 是否为纯数字
     * @return string
     */
    private function randNum($length, $numeric = FALSE) {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

}
