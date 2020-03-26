<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/10/8
 * Time: 16:41
 */

namespace app\frontend\modules\wechat\controllers;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\services\wechat\lib\WxPayApi;
use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayFaceAuthInfo;
use app\frontend\modules\order\services\OrderService;
use Yunshop\FacePayment\common\services\PlutusPayService;

class GetFaceAuthInfoController extends ApiController
{

    /**
     * @throws \app\common\services\wechat\lib\WxPayException
     */
    public function index()
    {
        $rawdata = request()->rawdata;
        $device_id = request()->device_id;
        $store_id = request()->store_id;//门店ID
        $store_name = request()->store_name;//门店名称

        $config = new WxPayConfig();
        $request = new WxPayFaceAuthInfo($config);
        $request->SetRawdata($rawdata);
        $request->SetStoreName($store_name);
        $request->SetStoreId($store_id);
        $request->SetDeviceId($device_id);
        $request->SetNow();

        $pay_sn = OrderService::createPaySN();

        $data = WxPayApi::authInfo($config, $request);
        if ($data['return_code'] != 'SUCCESS') {
            throw new AppException('人脸识别启动失败：',$data['return_msg']);
        }
        $data['pay_sn'] = $pay_sn;

        if (!$data['sub_appid']) {
            $data['sub_appid'] = '';
        }

        if (!$data['sub_mch_id']) {
            $data['sub_mch_id'] = '';
        }
        return $this->successJson('获取成功',$data);
    }

//    public function index()
//    {
//        $data = (new PlutusPayService())->getAuthInfo();
//        $data['pay_sn'] = OrderService::createPaySN();
//        $data['return_code'] = 'SUCCESS';
//        $data['return_msg'] = '请求成功';
//        return $this->successJson('获取成功',$data);
//    }

}