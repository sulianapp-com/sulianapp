<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/7
 * Time: 下午2:41
 */

namespace app\payment\controllers;

use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\common\services\Pay;
use app\payment\PaymentController;
use Yunshop\YunPay\services\YunPayNotifyService;

class YunpayController extends PaymentController
{
    private $attach = [];

    public function preAction()
    {
        parent::preAction();

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

        if(!empty($parameter)){
            if($this->getSignResult()) {
                if ($_POST['respCode'] == '0006') {
                    \Log::debug('------验证成功-----');
                    $data = [
                        'total_fee'    => floatval($parameter['transAmt']),
                        'out_trade_no' => $this->attach[0],
                        'trade_no'     => $parameter['transactionId'],
                        'unit'         => 'fen',
                        'pay_type'     => intval($_POST['productId']) == 112 ? '微信-YZ' : '支付宝-YZ',
                        'pay_type_id'     => intval($_POST['productId']) == 112 ? 12 : 15

                    ];
                  
                    $this->payResutl($data);
                    \Log::debug('----结束----');
                    echo 'SUCCESS';
                } else {
                    //其他错误
                }
            } else {
                //签名验证失败
            }
        }else {
            echo 'FAIL';
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

    public function refundUrl()
    {
        $parameter = $_POST;

        if (!empty($parameter)) {
            if ($this->getSignResult()) {
                if ($_POST['respCode'] == '0000') {
                    //验证成功，业务逻辑
                } else {
                    //其他错误
                }
            } else {
                //签名验证失败
            }
        } else {
            echo 'FAIL';
        }
    }

    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult()
    {
        $pay = \Setting::get('plugin.yun_pay_set');

        $notify = new YunPayNotifyService();
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
        Pay::payResponseDataLog($orderNo[0], '芸微信支付', json_encode($data));
    }
}