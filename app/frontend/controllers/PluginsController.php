<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 10/03/2017
 * Time: 16:42
 */

namespace app\frontend\controllers;


use app\common\components\BaseController;

class PluginsController extends BaseController
{

    public function getPluginData()
    {
        $enableds = app('plugins')->getEnabledPlugins()->toArray();

        foreach ($enableds as &$enabled) {
            unset($enabled['path']);
        }

        if($enableds){
            return $this->successJson('获取数据成功!', $enableds);
        }
        return $this->errorJson('未检测到数据!');
    }

}