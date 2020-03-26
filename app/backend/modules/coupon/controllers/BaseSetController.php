<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/25 下午6:38
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\coupon\controllers;


use app\common\components\BaseController;
use app\common\models\notice\MessageTemp;
use app\common\facades\Setting;
use app\common\helpers\Url;

class BaseSetController extends BaseController
{

    public function see()
    {
        //$coupon_set = array_pluck(\Setting::getAllByGroup('coupon')->toArray(), 'value', 'key');
        $coupon_set = \Setting::getByGroup('coupon');

        $temp_list = MessageTemp::getList();
        return view('coupon.base_set', [
            'coupon' => $coupon_set,
            'temp_list' => $temp_list,
        ])->render();
    }

    /**
     * 保存设置
     * @return mixed|string
     */
    public function store()
    {
        $requestData = \YunShop::request()->coupon;
//        dump($requestData);exit();
        if ($requestData) {
            foreach ($requestData as $key => $item) {
                \Setting::set('coupon.' . $key, $item);
            }
            return $this->message("设置保存成功",Url::absoluteWeb('coupon.base-set.see'));
        }
        return $this->see();
    }

}
