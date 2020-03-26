<?php

namespace app\payment\controllers;

use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\payment\PaymentController;
use app\frontend\modules\finance\models\BalanceRecharge;
use app\common\services\Pay;

class EupController extends PaymentController
{
	
	private $attach = [];

    public function preAction()
    {
        parent::preAction();

        if (empty(\YunShop::app()->uniacid)) {
            $this->attach = explode('a', $_GET['OrderID']);

            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $this->attach[0];

            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    //异步充值通知
	public function notifyUrl()
	{
		$parameter = $_GET;

		\Log::debug('------------EUP异步通知----------------'.$_SERVER['QUERY_STRING']);

		if(!empty($parameter)){
            if($this->getSignResult($parameter)) {
            	$recharge_log = BalanceRecharge::ofOrderSn($this->attach[1])->withoutGlobalScope('member_id')->first();
                if ($recharge_log && $recharge_log->status != 1) {
                    $this->log($parameter);
                    \Log::info('------EUP验证成功-----');
                    $data = [
                        'total_fee'    => floatval($parameter['Amount']),
                        'out_trade_no' => $this->attach[1],
                        'trade_no'     => 'eup',
                        'unit'         => 'yuan',
                        'pay_type'     => 'EUP支付',
                        'pay_type_id'  => 19,

                    ];
                    $this->payResutl($data);
                    \Log::info('----EUP结束----');
                    echo 'ok';
                } else {
                    if ($recharge_log && $recharge_log->status == 1) {
                        echo 'ok';
                    }
                }
            } else {
                //签名验证失败
                echo '签名验证失败';
            }
        }else {
            echo '无参数';
        }
	}

	//同步充值通知
	public function returnUrl()
    {
        $parameter = $_GET;

        $this->log($parameter);

        if(!empty($parameter)){
            if($this->getSignResult($parameter)) {
                $recharge_log = BalanceRecharge::ofOrderSn($this->attach[1])->withoutGlobalScope('member_id')->first();
                if ($recharge_log && $recharge_log->status != 1) {
                    \Log::info('------EUP验证成功-----');
                    $data = [
                        'total_fee'    => floatval($parameter['Amount']),
                        'out_trade_no' => $this->attach[1],
                        'trade_no'     => 'eup',
                        'unit'         => 'yuan',
                        'pay_type'     => 'EUP支付',
                        'pay_type_id'  => 19,

                    ];
                    $this->payResutl($data);
                    \Log::info('----EUP结束----');
                    redirect(Url::absoluteApp('member', ['i' => \YunShop::app()->uniacid]))->send();
                } else {
                    if ($recharge_log && $recharge_log->status == 1) {
                        \Log::debug('--------EUP充值已记录------------');
                        redirect(Url::absoluteApp('member', ['i' => \YunShop::app()->uniacid]))->send();
                    }
                    //其他错误
                    \Log::debug('----EUP充值记录不存在----');
                }
            } else {
                //签名验证失败
                \Log::debug('----EUP签名验证失败----');
            }
        }else {
            \Log::debug('----参数为空----');
        }
        redirect(Url::absoluteApp('home'))->send();
    }


    /**
     * 签名验证
     *
     * @return bool
     */
    public function getSignResult($parameter)
    {
    	$key = $parameter['OrderID'].$parameter['Amount'].'zhijie';

    	$md5_key = md5(md5($key));

    	return $parameter['Sign'] == $md5_key;
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
        Pay::payResponseDataLog($this->attach[1], 'EUP充值支付', json_encode($data));
    }
}