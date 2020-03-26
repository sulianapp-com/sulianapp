<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/3/10
 * Time: 下午2:25
 */

namespace app\backend\modules\system\controllers;

use app\common\components\BaseController;
use app\backend\models\Menu;

class MenusController extends BaseController
{

    public function index()
    {
        $result = Menu::getMenuAllInfo(0)->get()->toArray();

        if (!empty($result)) {
            //echo '<pre>';print_r($result);
        } else {
            echo 'empty';
        }

        return $this->render('setting.menu', [
        ]);
    }

    public function add()
    {}

    public function edit()
    {}

    public function del()
    {}
}