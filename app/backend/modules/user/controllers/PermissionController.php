<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 06/03/2017
 * Time: 23:58
 */

namespace app\backend\modules\user\controllers;


use app\common\components\BaseController;

class PermissionController extends BaseController
{
    public function index()
    {
        $menu = \app\backend\modules\menu\Menu::current()->getItems();
        return view('permission.index',['permission'=>$menu])->render();
    }
}