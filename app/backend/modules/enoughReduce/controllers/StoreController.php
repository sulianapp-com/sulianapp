<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/5/18
 * Time: 下午17:28
 */

namespace app\backend\modules\EnoughReduce\controllers;

use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\common\facades\SiteSetting;
use app\common\helpers\Url;

class StoreController extends BaseController
{
    public function index()
    {
        $setting = request()->input('setting');

        foreach ($setting as $key => $value) {
//            SiteSetting::set($key, $value);
            \Setting::set('enoughReduce.'.$key,$value);
        }

        return $this->successJson("设置保存成功", Url::absoluteWeb('goods.enough-reduce.index'));
    }
}