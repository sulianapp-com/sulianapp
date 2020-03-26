<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/10/21
 * Time: 11:04
 */

namespace app\common\services\wechat;


use app\common\modules\wechat\models\WechatProfitSharingLog;
use app\common\services\wechat\lib\WxPayApi;
use app\common\services\wechat\lib\WxPayConfig;
use app\common\services\wechat\lib\WxPayException;
use app\common\services\wechat\lib\WxPayProfitSharing;

class ProfitSharingService
{
    /**
     * @param $data
     * @return mixed
     * @throws lib\WxPayException
     */
    public static function addProfitSharing()
    {
//        $receiver = json_decode([
//            'type' => $data['type'],
//            'account' => $data['openid'],
//            'relation_type' => $data['relation_type'],
//        ]);
        $config = new WxPayConfig();
        $data = [
            'type' => 'MERCHANT_ID',
            'name' => $config->GetMchName(),
            'account' => $config->GetMerchantId(),
            'relation_type' => 'SERVICE_PROVIDER',
        ];
        $receiver = json_decode($data,256);

        $inputObj = new WxPayProfitSharing($config);
        $inputObj->SetReceiver($receiver);
        $result = WxPayApi::profitsharingaddreceiver($config, $inputObj);

        if ($result['return_code'] != 'SUCCESS') {
            throw new WxPayException($result['return_msg']);
        }

        return $result;
    }
    /**
     * @param $data
     * @return mixed
     * @throws lib\WxPayException
     */
    public static function deleteProfitSharing($data)
    {
        $receiver = json_decode([
            'type' => $data['type'],
            'account' => $data['openid'],
        ]);
        $config = new WxPayConfig();
        $inputObj = new WxPayProfitSharing($config);
        $inputObj->SetReceiver($receiver);
        $result = WxPayApi::profitsharingremovereceiver($config, $inputObj);

        if ($result['return_code'] != 'SUCCESS') {
            throw new WxPayException($result['return_msg']);
        }

        return $result;
    }

    /**
     * @param $amount
     * @param $transaction_id
     * @param $out_order_no
     * @return mixed
     * @throws WxPayException
     */
    public static function profitSharing($amount, $transaction_id)
    {
        $config = new WxPayConfig();
        $out_order_no = createNo('PSO', true);
        //记录分账信息
        $data = [
            'uniacid' => \YunShop::app()->uniacid,
            'mch_id' => $config->GetMerchantId(),
            'sub_mch_id' => $config->GetSubMerchantId(),
            'appid' => $config->GetAppId(),
            'sub_appid' => $config->GetSubAppId(),
            'transaction_id' => $transaction_id,
            'out_order_no' => $out_order_no,
            'status' => 0,
        ];
        $receiver = [
            [
                'type' => 'MERCHANT_ID',
                'account' => $config->GetMerchantId(),
                'amount' => (int)($amount * 100),
                'description' => '门店分账给服务商',
            ]
        ];
        $inputObj = new WxPayProfitSharing($config);
        $inputObj->SetTransaction_id($transaction_id);
        $inputObj->SetOut_order_no($out_order_no);
        $inputObj->SetReceivers(json_encode($receiver,256));
        $result = WxPayApi::profitsharing($config, $inputObj);

        if ($result['return_code'] =! 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            $data['status'] = -1;
            $data['message'] = $result['err_code_des'] ?: $result['return_msg'];
        } else {
            $data['status'] = 1;
            $data['message'] = $result['result_code'];
        }

        $create_data = array_merge($data,$receiver[0]);
        WechatProfitSharingLog::create($create_data);
        return $result;
    }
}