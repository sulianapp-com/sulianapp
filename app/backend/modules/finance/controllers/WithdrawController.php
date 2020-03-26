<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/31
 * Time: 上午11:28
 */

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\models\Withdraw;
use app\backend\modules\finance\services\WithdrawService;
use app\backend\modules\withdraw\controllers\AgainPayController;
use app\backend\modules\withdraw\controllers\AuditController;
use app\backend\modules\withdraw\controllers\AuditedRebutController;
use app\backend\modules\withdraw\controllers\ConfirmPayController;
use app\backend\modules\withdraw\controllers\PayController;
use app\common\components\BaseController;
use app\common\events\withdraw\WithdrawAuditedEvent;
use app\common\events\withdraw\WithdrawPayedEvent;
use app\common\exceptions\AppException;
use app\common\models\Income;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawController extends BaseController
{
    private $withdrawModel;


    public function dealt()
    {
        $resultData = \YunShop::request();

        \Log::debug('开始执行提现程序+++++++++++++++++', $resultData['submit_check']);

        if (isset($resultData['submit_check'])) {
            //审核
           return (new AuditController())->index();
        } elseif (isset($resultData['submit_pay'])) {
            //打款
            return (new PayController())->index();
        } elseif (isset($resultData['submit_cancel'])) {
            //重新审核
            return (new AuditController())->index();
        } elseif (isset($resultData['confirm_pay'])) {
            return (new ConfirmPayController())->index();
            //确认打款
        } elseif (isset($resultData['again_pay'])) {
            //重新打款
            return (new AgainPayController())->index();
        } elseif (isset($resultData['audited_rebut'])) {
            //审核后驳回
            return (new AuditedRebutController())->index();
        }

        return $this->message('提交数据有误，请刷新重试', yzWebUrl("finance.withdraw.index", ['id' => $resultData['id']]));
    }

    public function submitCheck($withdrawId, $incomeData)
    {
        \Log::debug('审核检测接口+++++++++++++++++');

        $this->withdrawModel = $this->getWithdrawModel($withdrawId);

        if ($this->withdrawModel->status != Withdraw::STATUS_INITIAL) {
            return ['msg' => '审核失败,数据不符合提现规则!'];
        }
        return $this->examine();
        /*$withdraw = Withdraw::getWithdrawById($withdrawId)->first();
        if ($withdraw->status != '0') {
            return json_encode(['msg' => '审核失败,数据不符合提现规则!']);
        }
        $withdrawStatus = "-1";
        $actual_amounts = 0;

        // 修改 yz_member_income 表
        foreach ($incomeData as $key => $income) {
            if ($income == 1) {
                $withdrawStatus = "1";
                $actual_amounts += Income::getIncomeById($key)->get()->sum('amount');
                Income::updatedIncomePayStatus($key, ['pay_status' => '1']);

            } elseif ($income == -1) {
                $withdrawStatus = "1";
                Income::updatedIncomePayStatus($key, ['pay_status' => '3','status'=> '0']);
            } else {
                Income::updatedIncomePayStatus($key, ['pay_status' => '-1']);
            }
        }
        $actual_poundage = sprintf("%.2f", $actual_amounts / 100 * $withdraw['poundage_rate']);
        $actual_servicetax = sprintf("%.2f", ($actual_amounts - $actual_poundage) / 100 * $withdraw['servicetax_rate']);
        $updatedData = [
            'status' => $withdrawStatus,
            'actual_amounts' => $actual_amounts - $actual_poundage - $actual_servicetax,
            'actual_poundage' => $actual_poundage,
            'actual_servicetax' => $actual_servicetax,
            'audit_at' => time(),
        ];
        $result = Withdraw::updatedWithdrawStatus($withdrawId, $updatedData);

        if ($result) {
            $noticeData = $withdraw;
            $noticeData->status = $withdrawStatus;
            $noticeData->actual_amounts = $updatedData['actual_amounts'];
            $noticeData->actual_poundage = $updatedData['actual_poundage'];
            $noticeData->audit_at = $updatedData['audit_at'];
            //审核通知事件
            event(new AfterIncomeWithdrawCheckEvent($noticeData));
            return json_encode(['msg' => '审核成功!']);
        }
        return json_encode(['msg' => '审核失败!'];)*/
    }

    public function submitCancel($withdrawId, $incomeData)
    {
        $this->withdrawModel = $this->getWithdrawModel($withdrawId);

        if ($this->withdrawModel->status != Withdraw::STATUS_INVALID) {
            return ['msg' => '重审核失败,数据不符合提现规则!'];
        }
        return $this->examine();
        /*$withdraw = Withdraw::getWithdrawById($withdrawId)->first();
        if ($withdraw->status != '-1') {
            return json_encode(['msg' => '审核失败,数据不符合提现规则!']);
        }
        $withdrawStatus = "-1";
        $actual_amounts = 0;
        foreach ($incomeData as $key => $income) {
            if ($income == 1) {
                $actual_amounts += Income::getIncomeById($key)->get()->sum('amount');
                $withdrawStatus = "1";
                Income::updatedIncomePayStatus($key, ['pay_status' => '1']);

            } elseif ($income == -1) {
                $withdrawStatus = "1";
                Income::updatedIncomePayStatus($key, ['pay_status' => '3','status'=> '0']);

            } else {
                Income::updatedIncomePayStatus($key, ['pay_status' => '-1']);
            }
        }
        $actual_poundage = sprintf("%.2f", $actual_amounts / 100 * $withdraw['poundage_rate']);
        $actual_servicetax = sprintf("%.2f", ($actual_amounts - $actual_poundage) / 100 * $withdraw['servicetax_rate']);
        $updatedData = [
            'status' => $withdrawStatus,
            'actual_amounts' => $actual_amounts - $actual_poundage - $actual_servicetax,
            'actual_poundage' => $actual_poundage,
            'actual_servicetax' => $actual_servicetax,
            'audit_at' => time(),
        ];


        $result = Withdraw::updatedWithdrawStatus($withdrawId, $updatedData);

        if ($result) {
            $noticeData = $withdraw;
            $noticeData->status = $withdrawStatus;
            $noticeData->actual_amounts = $updatedData['actual_amounts'];
            $noticeData->actual_poundage = $updatedData['actual_poundage'];
            $noticeData->audit_at = $updatedData['audit_at'];
            //重新审核通知事件
            event(new AfterIncomeWithdrawCheckEvent($noticeData));
            return json_encode(['msg' => '审核成功!']);
        }
        return json_encode(['msg' => '审核失败!']);*/
    }



    public function submitPay($withdrawId, $payWay)
    {
        if (!is_array($withdrawId)) {
            $withdraw = Withdraw::getWithdrawById($withdrawId)->first();

            if ($withdraw->status != '1') {
                return ['msg' => '打款失败,数据不存在或不符合打款规则!'];
            }

            $remark = '提现打款-' . $withdraw->type_name . '-金额:' . $withdraw->actual_amounts . '元,' .
                '手续费:' . $withdraw->actual_poundage;

        } else {
            //支付宝批量打款
            $withdraw = [];
            if ($payWay == '2' && !empty($withdrawId)) {
                foreach ($withdrawId as $id) {
                    $withdraw_modle = Withdraw::getWithdrawById($id)->first();

                    if (!is_null($withdraw_modle)) {
                        if ($withdraw_modle->status != '1') {
                            return ['msg' => '打款失败,数据不存在或不符合打款规则!'];
                        }

                        $withdraw[] = $withdraw_modle;

                        $remark[] = '提现打款-' . $withdraw_modle->type_name . '-金额:' . $withdraw_modle->actual_amounts . '元,' .
                            '手续费:' . $withdraw_modle->actual_poundage;
                    }
                }

                $remark = json_encode($remark);
            }
        }


        if ($payWay == '3') {
            //余额打款

            $resultPay = WithdrawService::balanceWithdrawPay($withdraw, $remark);
            if (is_bool($resultPay)) {
                $resultPay = ['errno' => 0, 'message' => '余额打款成功'];
            }
            Log::info('MemberId:' . $withdraw->member_id . ', ' . $remark . "打款到余额中!");

        } elseif ($payWay == '2') {
            //支付宝打款

            $resultPay = WithdrawService::alipayWithdrawPay($withdraw, $remark);
            Log::info('MemberId:' . $withdraw->member_id . ', ' . $remark . "支付宝打款中!");

        } elseif ($payWay == '1') {
            //微信打款

            $resultPay = WithdrawService::wechatWithdrawPay($withdraw, $remark);
            Log::info('resultPay:' . $resultPay);
            Log::info('MemberId:' . $withdraw->member_id . ', ' . $remark . "微信打款中!");

        } elseif ($payWay == '4') {
            //手动打款
            $resultPay = ['errno' => 0, 'message' => '手动打款成功'];
            Log::info('MemberId:' . $withdraw->member_id . ', ' . $remark . "手动打款!");

        }

        if (!empty($resultPay) && 0 == $resultPay['errno']) {

            $withdraw->pay_status = 1;
            //审核通知事件
            event(new WithdrawPayedEvent($withdraw));

            $updatedData = ['pay_at' => time()];
            Withdraw::updatedWithdrawStatus($withdrawId, $updatedData);
            $result = WithdrawService::otherWithdrawSuccess($withdrawId);
            return ['msg' => '提现打款成功!'];
        } elseif ($payWay == '2') {
            //修改提现记录状态
            $updatedData = [
                'status' => 4,
                'arrival_at' => time(),
            ];
            \Log::info('修改提现记录状态',print_r($updatedData,true));
            return Withdraw::updatedWithdrawStatus($withdrawId, $updatedData);
        }
    }





    public function batchAlipay()
    {
        $ids = \YunShop::request()->ids;

        $ids = explode(',', $ids);

        $result = $this->submitPay($ids, 2);
    }

    public function getAllWithdraw()
    {
        $type = request('type');

        $res = Withdraw::getAllWithdraw($type);

        return json_encode($res);
    }

    public function updateWidthdrawOrderStatus()
    {
        $ids = \YunShop::request()->ids;
        $status = 0;

        if (empty($ids)) {
            return ['status' => $status];
        }

        $withdrawId = explode(',', $ids);

        if (Withdraw::updateWidthdrawOrderStatus($withdrawId)) {
            $status = 1;
        }

        return ['status' => $status];
    }



    private function examine()
    {
        $audit_data = \YunShop::request()->audit;

        $audit_count = count($audit_data);

        $actual_amounts = 0;

        $adopt_count    = 0;
        $invalid_count  = 0;
        $reject_count   = 0;

        DB::beginTransaction();
        foreach ($audit_data as $income_id => $status) {

            //通过
            if ($status == Withdraw::STATUS_AUDIT) {
                $adopt_count += 1;
                $actual_amounts += Income::uniacid()->where('id', $income_id)->sum('amount');
                Income::where('id',$income_id)->update(['pay_status' => Income::PAY_STATUS_WAIT]);
            }

            //无效
            if ($status == Withdraw::STATUS_INVALID) {
                $invalid_count += 1;
                Income::where('id',$income_id)->update(['pay_status' => Income::PAY_STATUS_INVALID]);
            }

            //驳回
            if ($status == Withdraw::STATUS_REBUT) {
                $reject_count += 1;
                Income::where('id',$income_id)->update(['status' => Income::STATUS_INITIAL, 'pay_status' => Income::PAY_STATUS_REJECT]);
            }
        }

        $this->withdrawModel->status = Withdraw::STATUS_AUDIT;

        //如果全无效
        if ($invalid_count > 0 && $invalid_count == $audit_count) {
            $this->withdrawModel->status = Withdraw::STATUS_INVALID;
        }

        //如果全驳回
        if ($reject_count > 0 && $reject_count == $audit_count) {
            $this->withdrawModel->status = Withdraw::STATUS_PAY;
            $this->withdrawModel->pay_at = $this->withdrawModel->arrival_at = time();
        }

        //如果是无效 + 驳回 [同全驳回，直接完成]
        if ($invalid_count > 0 && $reject_count > 0 && ($invalid_count + $reject_count) == $audit_count) {
            $this->withdrawModel->status = Withdraw::STATUS_PAY;
            $this->withdrawModel->pay_at = $this->withdrawModel->arrival_at = time();
        }


        $this->withdrawModel->audit_at = time();

        $this->withdrawModel->actual_poundage = $this->getActualPoundage($actual_amounts);
        $this->withdrawModel->actual_servicetax = $this->getActualServiceTax($actual_amounts);
        $this->withdrawModel->actual_amounts = $actual_amounts - $this->getActualPoundage($actual_amounts) - $this->getActualServiceTax($actual_amounts);


        $result = $this->withdrawModel->save();
        if ($result !== true) {
            DB::rollBack();
            return ['msg' => '审核失败：记录修改失败!'];
        }

        event(new WithdrawAuditedEvent($this->withdrawModel));

        DB::commit();
        return ['msg' => '审核成功!'];
    }


    /**
     * 手续费
     * @return string
     */
    private function getActualPoundage($amount)
    {
        return bcdiv(bcmul($amount,$this->withdrawModel->poundage_rate,4),100,2);
    }



    /**
     * 劳务税
     * @return string
     */
    private function getActualServiceTax($amount)
    {
        $amount =$amount - $this->getActualPoundage($amount);

        return bcdiv(bcmul($amount,$this->withdrawModel->servicetax_rate,4),100,2);
    }




    private function getWithdrawModel($withdraw_id)
    {
        $_model = Withdraw::find($withdraw_id);
        if (!$_model) {
            throw new AppException('数据不存在或已被删除!');
        }
        return $_model;
    }


}
