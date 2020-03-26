<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/4/24
 * Time: 下午3:10
 */

namespace app\payment\controllers;

use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\services\Pay;
use app\payment\PaymentController;
use Yunshop\Usdtpay\services\UsdtpayNotifyService;

class UsdtpayController extends PaymentController
{
    private $attach = [];

    public function __construct()
    {
        parent::__construct();

        if (empty(\YunShop::app()->uniacid)) {
            $this->attach = explode(':', $_POST['orderNo']);

            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach[1];

            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    public function notifyUrl()
    {
        $parameter = $_POST;

        $this->log($parameter);

        if($this->getSignResult()) {
            if ($_POST['status'] == 'success') {
                \Log::debug('------验证成功-----');
                $data = [
                    'total_fee'    => floatval($parameter['orderAmount']),
                    'out_trade_no' => $this->attach[0],
                    'trade_no'     => $parameter['transactionId'],
                    'unit'         => 'yuan',
                    'pay_type'     => 'USDT支付',
                    'pay_type_id'     => 27

                ];

                $this->payResutl($data);
                \Log::debug('----结束----');
                echo 'success';
            } else {
                //其他错误
                echo 'fail';
            }
        } else {
            //签名验证失败
            echo 'fail1';
        }
    }

    public function returnUrl()
    {
        $trade = \Setting::get('shop.trade');

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            return redirect($trade['redirect_url'])->send();
        }

        if (0 == $_GET['state'] && $_GET['errorDetail'] == '成功') {
            redirect(Url::absoluteApp('member/payYes', ['i' => $_GET['attach']]))->send();
        } else {
            redirect(Url::absoluteApp('member/payErr', ['i' => $_GET['attach']]))->send();
        }
    }

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        $pay = \Setting::get('plugin.usdtpay_set');

        $notify = new UsdtpayNotifyService();
        $notify->setKey($pay['key']);

        return $notify->verifySign();
    }

    /**
     * 支付日志
     *
     * @param $post
     */
    public function log($data)
    {
        $orderNo = explode(':', $data['orderNo']);
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($orderNo[0], 'Usdt支付', json_encode($data));
    }
}