<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/12
 * Time: 下午2:30
 */

namespace app\backend\modules\setting\controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;

class CouponController extends BaseController
{
    public function index()
    {
        $coupon = Setting::get('shop.coupon');
        $requestModel = \YunShop::request()->coupon;
        if ($requestModel) {
            if (Setting::set('shop.coupon', $requestModel)) {
                return $this->message('优惠券设置成功', Url::absoluteWeb('setting.shop.index'));
            } else {
                $this->error('优惠券设置失败');
            }
        }
        for ($i = 0; $i <= 23; $i++) {
            $hourData[$i] = [
                'key' => $i,
                'name' => $i . ":00",
            ];
        }
        $temp_list = MessageTemp::getList();
        return view('setting.shop.coupon', [
            'set' => $coupon,
            'hourData' => $hourData,
            'temp_list' => $temp_list,
        ])->render();
    }
}