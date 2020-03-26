<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/10/8
 * Time: 16:41
 */

namespace app\frontend\modules\alipay\controllers;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\services\alipay\AlipayScanPayService;
use app\common\services\alipay\f2fpay\model\AlipayConfig;
use app\common\services\alipay\f2fpay\model\builder\AlipayInitializeContentBuilder;
use app\common\services\alipay\f2fpay\service\AlipayTradeService;
use app\common\services\wechat\lib\WxPayApi;
use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayFaceAuthInfo;
use app\frontend\modules\order\services\OrderService;
use Yunshop\FacePayment\common\services\PlutusPayService;

class GetFaceInitializeController extends ApiController
{

    /**
     * @return mixed
     * @throws \Exception
     */
    public function index()
    {
        $store_id = request()->store_id;
        $app_id = request()->app_id;
        $zimMetaInfo = request()->zimmetainfo;

        $set = \Setting::get('shop.alipay_set');
        $initializeRequestRequestBuilder = new AlipayInitializeContentBuilder();
        $initializeRequestRequestBuilder->setZimMetaInfo($zimMetaInfo);
        if (!$set['app_type']) {
            //第三方应用授权令牌,商户授权系统商开发模式下使用
            $appAuthToken = (new AlipayScanPayService())->getAuthToken();//根据真实值填写
            $initializeRequestRequestBuilder->setAppAuthToken($appAuthToken);
        }

        $config = (new AlipayConfig())->getConfig();
        $barPay = new AlipayTradeService($config);
        $result = (array)$barPay->initialize($initializeRequestRequestBuilder);
        if ($result['code'] != 10000) {
            return $this->errorJson('失败:'.$result['sub_msg']);
        }
        return $this->successJson('成功',$result['result']);
    }

}