<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/7/12
 * Time: 上午 09:59
 */

namespace app\frontend\modules\withdraw\services;

use app\common\models\Withdraw;
use Illuminate\Support\Facades\Log;
use app\common\exceptions\ShopException;
use app\backend\modules\finance\controllers\BalanceWithdrawController;

class BalanceAutomateAuditService
{
    /**
     * @var Withdraw
     */
    private $withdrawModel;


    public function __construct(Withdraw $withdrawModel)
    {
        $this->withdrawModel = $withdrawModel;
        \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $withdrawModel->uniacid;
    }


    /**
     * 提现免审核
     *
     * @throws ShopException
     */
    public function freeAudit()
    {
        $this->withdrawAudit();
        $this->withdrawPay();
        Log::debug("余额提现免审核ID:{$this->withdrawModel->id}自动审核打款完成");
    }


    /**
     * 提现审核
     *
     * @throws ShopException
     */
    private function withdrawAudit()
    {
        $this->withdrawModel->status = Withdraw::STATUS_AUDIT;
        $this->withdrawModel->audit_at = time();

        $this->withdrawUpdate();
    }


    /**
     * 提现打款
     *
     * @throws \app\common\exceptions\AppException
     */
    private function withdrawPay()
    {
        $BalanceWithdraw = new BalanceWithdrawController;

        $BalanceWithdraw->withdrawModel = $this->withdrawModel;

        $BalanceWithdraw->submitPay();
    }

    /**
     * 提现 model 数据保存
     * @return bool
     * @throws ShopException
     */
    private function withdrawUpdate()
    {
        if (!$this->withdrawModel->save()) {
            Log::debug("提现审核失败:{$this->withdrawModel->id}数据修改失败");
            throw new ShopException("提现审核失败:{$this->withdrawModel->id}数据修改失败");
        }

        return true;
    }
}