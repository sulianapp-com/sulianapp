<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/23
 * Time: 下午5:52
 */

namespace app\frontend\modules\shop\services;


use app\common\exceptions\AppException;
use app\common\models\UniAccount;
use app\frontend\modules\shop\services\models\ShopModel;

class ShopService
{
    private static $_current_shop;
    //todo 待实现
    public static function getCurrentShopModel(){

        $result = new UniAccount(['uniacid'=>\YunShop::app()->uniacid]);
        if(!isset($result)){
            throw new AppException('读取商城信息出错');
        }
        return $result;
    }
}