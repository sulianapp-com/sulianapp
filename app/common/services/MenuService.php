<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/7 上午10:36
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services;


use app\common\models\user\User;
use app\common\services\aliyun\Core\Config;

class MenuService
{
    //可以进入的路由
    public static function canAccess($item)
    {
        $current_menu = \app\backend\modules\menu\Menu::current()->getItems()[$item];
        $url = yzWebFullUrl($current_menu['url']) . ($current_menu['url_params'] ? '&' . $current_menu['url_params'] : '');


        if (PermissionService::isFounder()) {
            return $url;
        }
        if (PermissionService::isOwner()) {
            return $url;
        }
        if (PermissionService::isManager()) {
            return $url;
        }
        if (PermissionService::checkNoPermission($item) === true) {
            return $url;
        }

        if (!isset($current_menu['child'])) {
            return $url;
        }

        $userPermission = User::userPermissionCache();
        //检测当前 key 下路由是否有权限访问
        foreach ($current_menu['child'] as $key => $value) {

            if ($value['url'] == $current_menu['url'] && in_array($key, $userPermission)) {
                return $url;
                break;
            }
            continue;
        }
        //上面条件都不满足时，找第一个有权限访问的路由
        foreach ($current_menu['child'] as $key => $value) {

            if (in_array($key, $userPermission)) {
                return yzWebFullUrl($value['url']) . ($value['url_params'] ? '&' . $value['url_params'] : '');
                break;
            }
            continue;
        }
        return yzWebFullUrl('index.index');
    }

}
