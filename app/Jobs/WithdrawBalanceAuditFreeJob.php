<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/7/12
 * Time: 上午 10:00
 */

namespace app\Jobs;


use app\common\models\Withdraw;
use app\frontend\modules\withdraw\services\BalanceAutomateAuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WithdrawBalanceAuditFreeJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var Withdraw
     */
    private $withdrawModel;


    public function __construct(Withdraw $withdrawModel)
    {
        $this->withdrawModel = $withdrawModel;
    }

    /**
     * @throws \app\common\exceptions\ShopException
     */
    public function handle()
    {
        $automateAuditService = new BalanceAutomateAuditService($this->withdrawModel);

        $automateAuditService->freeAudit();
    }

}