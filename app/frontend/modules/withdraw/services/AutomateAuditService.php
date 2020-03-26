<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/15 下午2:03
 * Email: livsyitian@163.com
 */

namespace app\frontend\modules\withdraw\services;


use app\common\events\withdraw\WithdrawAuditedEvent;
use app\common\events\withdraw\WithdrawAuditEvent;
use app\common\events\withdraw\WithdrawAuditingEvent;
use app\common\exceptions\ShopException;
use app\common\services\withdraw\AuditService;
use app\common\services\withdraw\PayedService;
use app\frontend\modules\withdraw\models\Income;
use app\frontend\modules\withdraw\models\Withdraw;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomateAuditService
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
     */
    public function freeAudit()
    {
        $this->withdrawAudit();
        $this->withdrawPay();
        Log::debug("收入提现免审核ID:{$this->withdrawModel->id}自动审核打款完成");
    }


    /**
     * 提现审核
     */
    private function withdrawAudit()
    {
        $audit_ids = explode(',', $this->withdrawModel->type_id);

        $this->withdrawModel->audit_ids = $audit_ids;

        (new AuditService($this->withdrawModel))->withdrawAudit();
    }


    /**
     * 提现打款
     */
    private function withdrawPay()
    {
        (new PayedService($this->withdrawModel))->withdrawPay();
    }




}
