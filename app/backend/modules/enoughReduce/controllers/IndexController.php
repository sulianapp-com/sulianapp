<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/5/18
 * Time: 下午17:28
 */

namespace app\backend\modules\enoughReduce\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;

class IndexController extends BaseController
{
    public function index()
    {
        $setting = \Setting::getByGroup('enoughReduce');

        return view('goods.enoughReduce.index', [
            'setting' => json_encode($setting),
        ])->render();
    }
}