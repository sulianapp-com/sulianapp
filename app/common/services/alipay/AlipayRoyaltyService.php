<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/12/11
 * Time: 10:57
 */

namespace app\common\services\alipay\request;


use app\common\exceptions\ShopException;
use app\common\modules\alipay\models\AlipayOrderSettleLog;
use app\common\services\alipay\f2fpay\model\AlipayConfig;
use app\common\services\alipay\f2fpay\model\builder\AlipayOrderSettleContentBuilder;
use app\common\services\alipay\f2fpay\model\builder\AlipayRoyaltyContentBuilder;
use app\common\services\alipay\f2fpay\model\builder\AlipayTradeQueryContentBuilder;
use app\common\services\alipay\f2fpay\service\AlipayTradeService;
use Yunshop\FacePayment\common\models\RoyaltyModel;

class AlipayRoyaltyService
{

    /**
     * @param string $app_auth_token
     * @return array
     * @throws \Exception
     * @throws \app\common\exceptions\AppException
     */
    public static function bind($app_auth_token = '')
    {
        $config = (new AlipayConfig())->getConfig();
        $out_request_no = createNo('AR', true);
        $receiver_list = [
            [
                'type' => 'userId',
                'account' => $config['pid'],
                'name' => $config['name'],
                'memo' => '门店分账',
            ]
        ];
        $barRoyaltyRequestBuilder = new AlipayRoyaltyContentBuilder();
        $barRoyaltyRequestBuilder->setAppAuthToken($app_auth_token);
        $barRoyaltyRequestBuilder->setOutRequestNo($out_request_no);
        $barRoyaltyRequestBuilder->setReceiverList($receiver_list);
        $req = new AlipayTradeService($config);

        $barPayResult = (array)$req->royaltyBind($barRoyaltyRequestBuilder);
        if ($barPayResult['code'] != '10000') {
            throw new ShopException($barPayResult['sub_msg']);
        }
        $royalty_data = [
            'uniacid' => \YunShop::app()->uniacid,
            'app_id' => $config['app_id'],
            'account' => $receiver_list[0]['account'],
            'name' => $config['name'],
            'type' => $receiver_list[0]['type'],
        ];
        RoyaltyModel::create($royalty_data);
        return $barPayResult;
    }

    /**
     * @param $amount
     * @param $order_id
     * @param $trade_no
     * @return mixed
     * @throws ShopException
     * @throws \Exception
     * @throws \app\common\exceptions\AppException
     */
    public static function orderSettle($amount, $trade_no, $order_id)
    {
        $out_request_no = createNo('AOS', true);
        $alipay_config = new AlipayConfig();
        $config = $alipay_config->getConfig();
        $app_auth_token = $alipay_config->getAuthToken();
        $receiver_list = [
            [
                'royalty_type' => 'transfer',
//                'trans_out' => '2088821697943454',
//                'trans_out_type' => 'userId',
                'trans_in_type' => 'userId',
                'trans_in' => $config['pid'],
                'amount' => $amount,
//                'desc' => '分账给服务商',
            ]
        ];
        $orderSettleRequestBuilder = new AlipayOrderSettleContentBuilder();
        $orderSettleRequestBuilder->setAppAuthToken($app_auth_token);
        $orderSettleRequestBuilder->setOutRequestNo($out_request_no);
        $orderSettleRequestBuilder->setTradeNo($trade_no);
        $orderSettleRequestBuilder->setRoyaltyParameters($receiver_list);
        $req = new AlipayTradeService($config);
        $barPayResult = (array)$req->tradeOrderSettle($orderSettleRequestBuilder);


//        $queryContentBuilder = new AlipayTradeQueryContentBuilder();
//        $queryContentBuilder->setTradeNo($trade_no);
//        $queryContentBuilder->setQueryOptions(['TRADE_SETTLE_INFO']);
//        $queryContentBuilder->setAppAuthToken($app_auth_token);
//        $req = new AlipayTradeService($config);
//        $queryResponse = (array)$req->query($queryContentBuilder);
//        dd($queryResponse);
        $data = [
            'app_id' => $config['app_id'],
            'order_id' => $order_id,
            'app_auth_token' => $app_auth_token,
            'out_request_no' => $out_request_no,
            'trade_no' => $trade_no,
        ];

        if ($barPayResult['code'] == '10000') {
            $data['status'] = 1;
            $data['message'] = $barPayResult['msg'];
        } else {
            $data['status'] = -1;
            $data['message'] = $barPayResult['sub_msg'];
        }


        $create_data = array_merge($data,$receiver_list[0]);
        AlipayOrderSettleLog::create($create_data);
        return $barPayResult;
    }
}