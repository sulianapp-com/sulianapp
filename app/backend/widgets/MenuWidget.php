<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets;


use app\common\components\Widget;

class MenuWidget extends Widget
{
    public $test = '';

    public function run()
    {
        $menu = \app\backend\modules\menu\Menu::current()->getItems();
        return view('widgets.menu',['menu'=>$menu]);
    }
}