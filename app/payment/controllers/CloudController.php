<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/4
 * Time: 下午4:04
 */

namespace app\payment\controllers;


use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\services\Pay;
use app\payment\PaymentController;
use Yunshop\CloudPay\services\CloudPayNotifyService;

class CloudController extends PaymentController
{
    private $attach = [];

    public function preAction()
    {
        parent::preAction();

        if (empty(\YunShop::app()->uniacid)) {
            $this->attach = explode(':', $_GET['attach']);

            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach[0];

            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    public function notifyUrl()
    {
        $this->log($_GET);

        if ($this->getSignResult() && '00' == $_GET['respcd'] && $_GET['errorDetail'] == "SUCCESS") {
            \Log::debug('------验证成功-----');
            $data = [
                'total_fee'    => floatval($_GET['txamt']),
                'out_trade_no' => $_GET['orderNum'],
                'trade_no'     => $_GET['channelOrderNum'],
                'unit'         => 'fen',
                'pay_type'     => '云微信支付',
                'pay_type_id'     => 6

            ];

            $this->payResutl($data);
            \Log::debug('----结束----');
            echo "success";
        } else {
            echo "fail";
        }
    }

    public function notifyAliPayUrl()
    {
        $this->log($_GET);

        if ($this->getSignResult() && '00' == $_GET['respcd'] && $_GET['errorDetail'] == "SUCCESS") {
            \Log::debug('------验证成功-----');
            $data = [
                'total_fee'    => floatval($_GET['txamt']),
                'out_trade_no' => $_GET['orderNum'],
                'trade_no'     => $_GET['channelOrderNum'],
                'unit'         => 'fen',
                'pay_type'     => '云支付宝支付',
                'pay_type_id'     => 7

            ];

            $this->payResutl($data);
            \Log::debug('----结束----');
            echo "success";
        } else {
            echo "fail";
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

    public function frontUrl()
    {
        $trade = \Setting::get('shop.trade');

        if (!is_null($trade) && isset($trade['redirect_url']) && !empty($trade['redirect_url'])) {
            return redirect($trade['redirect_url'])->send();
        }

        if (0 == $_GET['state'] && $_GET['errorDetail'] == '成功') {
            redirect(Url::absoluteApp('member', ['i' => $_GET['attach']]))->send();
        } else {
            redirect(Url::absoluteApp('home', ['i' => $_GET['attach']]))->send();
        }
    }

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        $pay = \Setting::get('plugin.cloud_pay_set');

        $notify = new CloudPayNotifyService();
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
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($data['orderNum'], '云收银微信支付', json_encode($data));
    }
}