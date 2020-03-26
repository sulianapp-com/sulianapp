<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/16
 * Time: 15:35
 */

namespace app\frontend\modules\withdraw\services;

use app\common\models\notice\MessageTemp;
use app\common\services\MessageService;

class WithdrawMessageService extends MessageService
{
    protected $order;
    protected $msg;
    protected $templateId;
    protected $notice;

    /**
     * @param $withdrawModel
     */
    public function withdraw($withdrawModel)
    {
        $temp_id = \Setting::get('withdraw.notice')['member_withdraw'];
        if (!$temp_id) {
            return;
        }
        $params = [
            ['name' => '粉丝昵称', 'value' => $withdrawModel->hasOneMember->nickname],
            ['name' => '申请时间', 'value' => $withdrawModel->created_at->toDateTimeString()],
            ['name' => '提现金额', 'value' => $withdrawModel->amounts],
            ['name' => '提现类型', 'value' => $withdrawModel->type_name],
            ['name' => '提现方式', 'value' => $withdrawModel->pay_way_name],
            ['name' => '提现单号', 'value' => $withdrawModel->withdraw_sn],
        ];
        $this->transfer($temp_id, $params);
    }

    private function transfer($temp_id, $params)
    {
        $this->msg = MessageTemp::getSendMsg($temp_id, $params);
        if (!$this->msg) {
            return;
        }
        $this->templateId = MessageTemp::$template_id;
        $this->sendToShops();
    }

    private function sendToShops()
    {
        if (empty(\Setting::get('withdraw.notice.withdraw_user'))) {
            return;
        }
        if (empty($this->templateId)) {
            return;
        }
        //客服发送消息通知
        foreach (\Setting::get('withdraw.notice.withdraw_user') as $withdraw_user) {
            $this->notice($this->templateId, $this->msg, $withdraw_user['uid']);
        }
    }

}