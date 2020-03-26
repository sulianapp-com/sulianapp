<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/13 下午2:22
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\withdraw\services;


use app\common\exceptions\AppException;
use app\common\facades\Setting;
use app\frontend\modules\finance\services\WithdrawManualService;
use app\frontend\modules\withdraw\models\Withdraw;

class PayWayValidatorService
{
    public function validator($pay_way)
    {
        //todo 临时使用，应该提供一个系统的验证
        switch ($pay_way) {
            case 'balance':
                $this->balanceValidator();
                break;
            case 'wechat':
                $this->weChatValidator();
                break;
            case 'alipay':
                $this->alPayValidator();
                break;
            case 'huanxun':
                $this->huanXunValidator();
                break;
            case 'manual':
                $this->manualValidator();
                break;
            case 'eup_pay':
                $this->eupPayValidator();
                break;
            case 'yop_pay':
                $this->yopPayValidator();
                break;
            case 'converge_pay':
                $this->convergePayValidator();
                break;
            default:
                throw new AppException('未知提现方式');
                break;
        }
    }


    private function balanceValidator()
    {

    }

    private function weChatValidator()
    {

    }

    private function alPayValidator()
    {
        if (!WithdrawManualService::getAlipayStatus()) {
            throw new AppException('您未配置支付宝信息，请先修改个人信息中支付宝信息');
        }
    }


    private function huanXunValidator()
    {

    }


    private function eupPayValidator()
    {

    }

    private function yopPayValidator()
    {

    }

    private function convergePayValidator()
    {

    }


    private function manualValidator()
    {
        switch ($this->getManualType()) {
            case Withdraw::MANUAL_TO_WECHAT:
                $result = $this->weChatStatus();
                break;
            case Withdraw::MANUAL_TO_ALIPAY:
                $result = $this->alipayStatus();
                break;
            default:
                $result = $this->bankStatus();
        }
        if ($result !== true) {
            throw new AppException($result);
        }
    }


    /**
     * 是否配置银行卡信息
     * @return bool|string
     */
    private function bankStatus()
    {
        if (!WithdrawManualService::getBankStatus()) {
            return '请先完善您个人信息中银行卡信息';
        }
        return true;
    }



    /**
     * 是否配置微信信息
     * @return bool|string
     */
    private function weChatStatus()
    {
        if (!WithdrawManualService::getWeChatStatus()) {
            return '请先完善您个人信息中的微信信息';
        }
        return true;
    }



    /**
     * 是否配置支付宝信息
     * @return bool|string
     */
    private function alipayStatus()
    {
        if (!WithdrawManualService::getAlipayStatus()) {
            return '请先完善您个人信息中支付宝信息';
        }
        return true;
    }


    private function getManualType()
    {
        $set = Setting::get('withdraw.income');

        return empty($set['manual_type']) ? 1 : $set['manual_type'];
    }




}
