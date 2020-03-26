<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/7
 * Time: 下午3:08
 */

namespace app\common\services\finance;

use app\common\events\withdraw\WithdrawPayedEvent;
use app\common\models\Income;
use app\common\models\Withdraw as WithdrawModel;

class Withdraw
{

    public static function paySuccess($withdrawSN)
    {
        $withdrawModel = WithdrawModel::getWithdrawByWithdrawSN($withdrawSN);
        if ($withdrawModel && $withdrawModel->type == 'balance') {
            if ($withdrawModel->status != 2) {
                $withdrawModel->status = 2;
                $withdrawModel->arrival_at = time();
                $result = $withdrawModel->save();
                if ($result) {
                    BalanceNoticeService::withdrawSuccessNotice($withdrawModel);
                }
            }
            return true;
        }
        return static::otherWithdrawSuccess($withdrawSN);
    }

    public static function otherWithdrawSuccess($withdrawId)
    {
        $withdraw = WithdrawModel::getWithdrawById($withdrawId)->first();
        if ($withdraw->status != '1') {
            return false;
        }
        $withdraw->pay_status = 1;
        //提现打款到账事件
        event(new WithdrawPayedEvent($withdraw));
        //修改收入状态
        foreach ($withdraw['type_data']['incomes'] as $item) {
            if ($item['pay_status'] == '1') {
                Income::updatedIncomePayStatus($item['id'], ['pay_status' => '2']);
            }
        }
        //修改提现记录状态
        $updatedData = [
            'status'     => 2,
            'arrival_at' => time(),
        ];
        \Log::info('修改提现记录状态', print_r($updatedData, true));
        return WithdrawModel::updatedWithdrawStatus($withdrawId, $updatedData);
    }

    public static function payFail($withdrawSN)
    {
        $withdrawModel = WithdrawModel::getWithdrawByWithdrawSN($withdrawSN);
        if ($withdrawModel && $withdrawModel->type == 'balance') {
            if ($withdrawModel->status != 1 && $withdrawModel->status == 4) {
                $withdrawModel->status = 1;
                $withdrawModel->arrival_at = time();
                $withdrawModel->save();
            }
        }

        return static::otherWithdrawFail($withdrawSN);
    }

    public static function otherWithdrawFail($withdrawId)
    {
        $withdraw = WithdrawModel::getWithdrawById($withdrawId)->first();
        if ($withdraw->status != '1' && $withdraw->status == '4') {
            $updatedData = [
                'status'     => 1,
                'arrival_at' => time(),
            ];
            \Log::info('修改提现记录状态', print_r($updatedData, true));
            return WithdrawModel::updatedWithdrawStatus($withdrawId, $updatedData);
        }
    }
}