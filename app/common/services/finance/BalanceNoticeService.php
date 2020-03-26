<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/25 下午10:32
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\finance;


use app\common\models\Member;
use app\common\models\notice\MessageTemp;
use app\common\services\MessageService;
use Illuminate\Database\Eloquent\Model;

class BalanceNoticeService
{
    public static function withdrawSubmitNotice(Model $withdrawModel)
    {
        $template_id = \Setting::get('shop.notice.withdraw_submit');
        if (!$template_id) {
            return null;
        }

        $params = [
            ['name' => '时间', 'value' => $withdrawModel->created_at->toDateTimeString()],
            ['name' => '金额', 'value' => $withdrawModel->amounts],
            ['name' => '手续费', 'value' => $withdrawModel->actual_poundage],
        ];
        static::notice($template_id,$params,$withdrawModel->member_id);
    }

    public static function withdrawSuccessNotice(Model $withdrawModel)
    {
        $template_id = \Setting::get('shop.notice.withdraw_success');
        if (!$template_id) {
            return null;
        }
        $pay_at = $withdrawModel->pay_at;
        if(empty($pay_at)){
            $pay_at = time();
        }
        $params = [
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', $pay_at)],
            ['name' => '金额', 'value' => $withdrawModel->amounts],
            ['name' => '手续费', 'value' => $withdrawModel->actual_poundage],
        ];
        static::notice($template_id,$params,$withdrawModel->member_id);
    }

    public static function withdrawFailureNotice(Model $withdrawModel)
    {
        $template_id = \Setting::get('shop.notice.withdraw_fail');
        if (!$template_id) {
            return null;
        }

        $params = [
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', $withdrawModel->audit_at)],
            ['name' => '金额', 'value' => $withdrawModel->amounts],
            ['name' => '手续费', 'value' => $withdrawModel->actual_poundage],
            ['name' => '提现单号', 'value' => $withdrawModel->withdraw_sn],
        ];
        static::notice($template_id,$params,$withdrawModel->member_id);
    }

    public static function withdrawRejectNotice(Model $withdrawModel)
    {
        $template_id = \Setting::get('shop.notice.withdraw_reject');
        if (!$template_id) {
            return null;
        }

        $params = [
            ['name' => '时间', 'value' => date('Y-m-d H:i:s', $withdrawModel->audit_at)],
            ['name' => '金额', 'value' => $withdrawModel->amounts],
            ['name' => '手续费', 'value' => $withdrawModel->actual_poundage],
        ];
        static::notice($template_id,$params,$withdrawModel->member_id);
    }

    public static function notice($templateId,$params,$memberId)
    {
        if (!$templateId) {
            return;
        }
        $msg = MessageTemp::getSendMsg($templateId, $params);
        if (!$msg) {
            return;
        }
        $news_link = MessageTemp::find($templateId)->news_link;
        $news_link = $news_link ?:'';
        MessageService::notice(MessageTemp::$template_id, $msg, $memberId,'',$news_link);
    }



}
