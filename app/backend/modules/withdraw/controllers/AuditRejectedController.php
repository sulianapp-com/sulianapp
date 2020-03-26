<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/27
 * Time: 4:33 PM
 */

namespace app\backend\modules\withdraw\controllers;


use app\backend\models\Withdraw;
use app\common\exceptions\ShopException;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use Illuminate\Support\Facades\DB;

class AuditRejectedController extends PreController
{

    /**
     * 提现记录 审核后驳回接口
     */
    public function index()
    {
        $result = $this->auditedRebut();
        if ($result == true) {
//            BalanceNoticeService::withdrawRejectNotice($this->withdrawModel);
            return $this->message('驳回成功', yzWebUrl("withdraw.detail.index", ['id' => $this->withdrawModel->id]));
        }
        return $this->message('驳回失败，请刷新重试', yzWebUrl("withdraw.detail.index", ['id' => $this->withdrawModel->id]), 'error');
    }

    public function validatorWithdrawModel($withdrawModel)
    {
        if (!in_array($withdrawModel->status, [Withdraw::STATUS_INITIAL, Withdraw::STATUS_INVALID])) {
            throw new ShopException('状态错误，不符合驳回规则！');
        }
    }

    /**
     * @return bool
     */
    private function auditedRebut()
    {
        DB::transaction(function () {
            $this->_auditedRebut();
        });
        return true;
    }

    /**
     * @throws ShopException
     */
    private function _auditedRebut()
    {
        $result = $this->updateWithdrawStatus();
        if (!$result) {
            throw new ShopException('驳回失败：更新状态失败');
        }

        $result = $this->updateBalance();
        if (!$result) {
            throw new ShopException('驳回失败：更新余额明细失败');
        }
    }

    /**
     * @return bool
     */
    private function updateWithdrawStatus()
    {
        $this->withdrawModel->status = Withdraw::STATUS_REBUT;
        $this->withdrawModel->arrival_at = time();

        return $this->withdrawModel->save();
    }

    private function updateBalance()
    {
        $data = array(
            'member_id'     => $this->withdrawModel->member_id,
            'change_value'  => $this->withdrawModel->amounts,
            'operator'      => ConstService::OPERATOR_SHOP,
            'operator_id'   => \YunShop::app()->uid,
            'remark'        => '余额提现驳回' . $amounts = $this->withdrawModel->amounts . "元",
            'relation'      => $this->withdrawModel->withdraw_sn,
        );
        $result = (new BalanceChange())->rejected($data);
        if ($result === true) {
            return true;
        }
        return false;
    }
}
