<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/12/5
 * Time: 15:25
 */

namespace app\frontend\modules\alipay\controllers;


use app\common\components\ApiController;
use app\common\services\alipay\f2fpay\model\AlipayConfig;
use Yunshop\StoreCashier\store\models\StoreAlipaySetting;

class GetAlipayConfigController extends ApiController
{
    public function index()
    {

        $set = \Setting::get('shop.alipay_set');
        if (!$set['app_type']) {
            //第三方应用授权令牌,商户授权系统商开发模式下使用
            $set['pid'] = StoreAlipaySetting::uniacid()->where('store_id', request()->store_id)->first()->user_id;
        }

        $data = [
            'partnerId' => '',
            'merchantId' => $set['pid'],
            'appId' => $set['app_id']
        ];

        return $this->successJson('成功',$data);
    }

}