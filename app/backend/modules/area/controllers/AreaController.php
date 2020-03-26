<?php
namespace app\backend\modules\area\controllers;

use app\backend\modules\area\models\Area;
use app\common\components\BaseController;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 上午9:17
 */
class AreaController extends BaseController
{
    /**
     * 
     */
    public function selectCity()
    {
        $citys = Area::getAreasByCity(\YunShop::request()->parent_id);
        return view('area.selectcitys', [
            'citys' => $citys->toArray()
        ])->render();
    }

}