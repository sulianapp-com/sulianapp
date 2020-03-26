<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/12 上午10:57
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\controllers;


use app\common\components\BaseController;

class CacheController extends BaseController
{
    protected $isPublic = true;
    public function update()
    {
        \Artisan::call('config:cache');
        \Cache::flush();


        return $this->message('缓存更新成功');
    }

}
