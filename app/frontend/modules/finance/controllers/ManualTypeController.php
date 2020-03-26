<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/26 下午2:19
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\controllers;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\frontend\models\MemberShopInfo;
use app\frontend\modules\finance\services\WithdrawManualService;
use app\frontend\modules\member\models\MemberBankCard;

class ManualTypeController extends ApiController
{
    public function isCanSubmit()
    {
        $manual_type = Setting::get('withdraw.income')['manual_type'] ?: 1;
        //$manualService = new WithdrawManualService();

        switch ($manual_type) {
            case 2:
                $result['manual_type'] = 'wechat';
                $result['status'] = WithdrawManualService::getWeChatStatus();
                break;
            case 3:
                $result['manual_type'] = 'alipay';
                $result['status'] = WithdrawManualService::getAlipayStatus();
                break;
            default:
                $result['manual_type'] = 'bank';
                $result['status'] = WithdrawManualService::getBankStatus();

        }
        return $this->successJson('ok',$result);
    }




}
