<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 08/03/2017
 * Time: 09:39
 */

namespace app\common\traits;


use app\common\models\user\User;

trait PermissionTrait
{

    public function isAuth()
    {
        return \YunShop::app()->uid;
    }

    /**
     * 检测是否有权限
     * @param $route
     * @return bool
     */
    public function can($route)
    {

        if($this->checkNoPermission($route) === true){
            return true;
        }
        if ($this->isFounder()) {
            return true;
        }
        return in_array($route, User::userPermissionCache());
    }

    /**
     * 检测是否存在白名单
     * @param $route
     * @return bool
     */
    public function checkNoPermission($route)
    {
        $noPermissions = \Cache::get('noPermissions');
        if($noPermissions === null){
            $noPermissions = $this->getNoPermissionList(\app\backend\modules\menu\Menu::current()->getItems());
            \Cache::put('noPermissions',$noPermissions);
        }
        if(in_array($route, $noPermissions)){
            return true;
        }
        return false;
    }

    /**
     * 获取权限白名单
     * @param $menus
     * @return array
     */
    public function getNoPermissionList($menus)
    {
        $noPermissions = [];
        if ($menus) {
            foreach ($menus as $key => $m) {
                if (!(isset($m['permit']) && $m['permit'] === true)) {
                    $noPermissions[] = $key;
                }
                if(isset($m['child']) && $m['child']){
                     $noPermissions = array_merge($noPermissions,$this->getNoPermissionList($m['child']));
                }
            }
        }
        return $noPermissions;
    }


    /**
     * 是否是创始人
     * @return mixed
     */
    public function isFounder()
    {
        return \YunShop::app()->isfounder === true;
    }
}
