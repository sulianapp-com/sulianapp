<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/27
 * Time: 下午4:26
 */

namespace app\backend\controllers;


use app\common\components\BaseController;
use app\common\models\Address;
use app\common\models\Street;

class AddressController extends BaseController
{
    public function getAddress()
    {
        $addressData = [];
        switch (\YunShop::request()->type) {
            case 'province':

                $addressData = Address::getProvince();
                break;
            case 'city':
                $addressData = Address::getCityByParentId(\YunShop::request()->parentid);
                break;
            case 'district':
                $addressData = Address::getAreaByParentId(\YunShop::request()->parentid);
                break;
            case 'street':
                $addressData = Street::getStreetByParentId(\YunShop::request()->parentid);
                break;
        }

        echo json_encode($addressData);
    }

}