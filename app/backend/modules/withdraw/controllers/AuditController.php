<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/12 下午4:23
 * Email: livsyitian@163.com
 */

namespace app\backend\modules\withdraw\controllers;


use app\backend\models\Withdraw;
use app\common\exceptions\ShopException;
use app\common\services\withdraw\AuditService;

class AuditController extends PreController
{
    /**
     * 提现记录审核接口
     */
    public function index()
    {
        list($audit_ids, $invalid_ids, $rebut_ids) = $this->auditResult();

        $this->withdrawModel->audit_ids = $audit_ids;
        $this->withdrawModel->rebut_ids = $rebut_ids;
        $this->withdrawModel->invalid_ids = $invalid_ids;

        $result = (new AuditService($this->withdrawModel))->withdrawAudit();

        if ($result == true) {
            return $this->message('审核成功', yzWebUrl("withdraw.detail.index", ['id' => $this->withdrawModel->id]));
        }
        return $this->message('审核失败，请刷新重试', yzWebUrl("withdraw.detail.index", ['id' => $this->withdrawModel->id]), 'error');
    }


    /**
     * @return array
     * @throws ShopException
     */
    private function auditResult()
    {
        $audit_data = $this->getPostAuditData();

        $audit_ids = [];
        $rebut_ids = [];
        $invalid_ids = [];
        foreach ($audit_data as $income_id => $status) {

            switch ($status) {
                case Withdraw::STATUS_AUDIT:
                    $audit_ids[] = $income_id;
                    break;
                case Withdraw::STATUS_INVALID:
                    $invalid_ids[] = $income_id;
                    break;
                case Withdraw::STATUS_REBUT:
                    $rebut_ids[] = $income_id;
                    break;
            }
        }

        return [$audit_ids, $invalid_ids, $rebut_ids];
    }


    /**
     * @return array
     * @throws ShopException
     */
    private function getPostAuditData()
    {
        $audit_data = \YunShop::request()->audit;
        if (!$audit_data) {
            throw new ShopException('数据参数错误');
        }
        return $audit_data;
    }


    public function validatorWithdrawModel($withdrawModel)
    {
        if ($withdrawModel->status != Withdraw::STATUS_INITIAL && $withdrawModel->status != Withdraw::STATUS_INVALID) {
            throw new ShopException('状态错误，不符合审核规则！');
        }
    }
}
