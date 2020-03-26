<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/7/13 下午2:52
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\services;


use app\common\events\finance\BalanceRechargedEvent;
use app\common\exceptions\ShopException;
use app\common\models\finance\BalanceRechargeActivity;
use app\common\services\credit\ConstService;
use app\common\services\finance\BalanceChange;
use \app\common\models\finance\BalanceRecharge;
use EasyWeChat\Support\Log;
use Illuminate\Support\Facades\DB;

class BalanceRechargeResultService
{
    /**
     * @var BalanceService
     */
    private $balanceSet;

    /**
     * @var BalanceRecharge
     */
    private $rechargeModel;


    private $array;

    private $enough;

    private $give;


    public function __construct(BalanceRecharge $balanceRecharge)
    {
        $this->balanceSet = new BalanceService();
        $this->rechargeModel = $balanceRecharge;
    }

    public function confirm()
    {
        DB::transaction(function () {
            $this->_confirm();
        });
    }

    private function _confirm()
    {
        $this->updateRechargeStatus();
        $this->updateMemberBalance();
        $is_award = $this->rechargeActivity();
        if ($is_award) {
            $this->rechargeEnoughGive();
        }
        event(new BalanceRechargedEvent($this->rechargeModel));
    }

    private function rechargeActivity()
    {
        //是否增加充值活动限制
        if ($this->balanceSet->rechargeActivityStatus()) {

            //是否在活动时间
            $start_time = $this->balanceSet->rechargeActivityStartTime();
            $end_time = $this->balanceSet->rechargeActivityEndTime();
            $recharge_time = $this->rechargeModel->created_at->timestamp;

            if (!($start_time <= $recharge_time) || !($end_time >= $recharge_time)) {
                \Log::debug("余额充值：订单号{$this->rechargeModel->ordersn}充值时间未在充值活动时间之内，取消赠送");
                return false;
            }

            //参与次数检测
            $rechargeActivity = BalanceRechargeActivity::where('member_id', $this->rechargeModel->member_id)
                ->where('activity_id', $this->balanceSet->rechargeActivityCount())
                ->first();

            $fetter = $this->balanceSet->rechargeActivityFetter();
            if ($fetter && $fetter >= 1 && $rechargeActivity && $rechargeActivity->partake_count >= $fetter) {
                \Log::debug("余额充值：订单号{$this->rechargeModel->ordersn}会员参与次数已达到上限");
                return false;
            }

            //更新会员参与活动次数
            if ($rechargeActivity) {
                $rechargeActivity->partake_count += 1;
            } else {
                $rechargeActivity = new BalanceRechargeActivity();

                $rechargeActivity->uniacid = $this->rechargeModel->uniacid;
                $rechargeActivity->member_id = $this->rechargeModel->member_id;
                $rechargeActivity->partake_count += 1;
                $rechargeActivity->activity_id = $this->balanceSet->rechargeActivityCount();
            }
            $rechargeActivity->save();
            return true;
        }
        return false;
    }

    /**
     * 余额充值支付回调
     *
     * @throws ShopException
     */
    public function rechargeEnoughGive()
    {
        $result = $this->_rechargeEnoughGive();
        if ($result !== true) {
            throw new ShopException("余额充值：订单号{$this->rechargeModel->ordersn}充值满奖失败");
        }
    }

    /**
     * 修改充值状态
     *
     * @throws ShopException
     */
    private function updateRechargeStatus()
    {
        $this->rechargeModel->status = ConstService::STATUS_SUCCESS;

        $result = $this->rechargeModel->save();
        if (!$result) {
            throw new ShopException("余额充值：订单号{$this->rechargeModel->ordersn}修改充值状态失败");
        }
    }

    /**
     * 修改会员余额
     *
     * @throws ShopException
     */
    private function updateMemberBalance()
    {
        $result = (new BalanceChange())->recharge($this->getBalanceChangeData());
        if (!$result) {
            throw new ShopException("余额充值：订单号{$this->rechargeModel->ordersn}修改会员余额失败");
        }
    }

    /**
     * 获取余额变动明细记录 data 数组
     * @return array
     */
    private function getBalanceChangeData()
    {
        return [
            'member_id'    => $this->rechargeModel->member_id,
            'remark'       => '会员充值' . $this->rechargeModel->money . '元，支付单号：' . $this->array['pay_sn'],
            'source'       => ConstService::SOURCE_RECHARGE,
            'relation'     => $this->rechargeModel->ordersn,
            'operator'     => ConstService::OPERATOR_MEMBER,
            'operator_id'  => $this->rechargeModel->member_id,
            'change_value' => $this->rechargeModel->money,
        ];
    }

    /**
     * 余额充值奖励
     * @return bool|string
     */
    private function _rechargeEnoughGive()
    {
        if ($this->getGiveMoney()) {
            return (new BalanceChange())->award($this->getBalanceAwardData());
        }
        return true;
    }

    /**
     * 获取充值奖励金额
     * @return string
     */
    private function getGiveMoney()
    {
        $sale = $this->getRechargeSale();
        $money = $this->rechargeModel->money;

        rsort($sale);
        $result = '';
        foreach ($sale as $key) {
            if (empty($key['enough']) || empty($key['give'])) {
                continue;
            }
            if (bccomp($money, $key['enough'], 2) != -1) {
                if ($this->getProportionStatus()) {
                    $result = bcdiv(bcmul($money, $key['give'], 2), 100, 2);
                } else {
                    $result = bcmul($key['give'], 1, 2);
                }
                $this->enough = floatval($key['enough']);
                $this->give = $key['give'];
                break;
            }
        }
        return $result;
    }

    /**
     * 获取充值奖励营销设置数组
     * @return array
     */
    private function getRechargeSale()
    {
        $sale = $this->balanceSet->rechargeSale();

        $sale = array_values(array_sort($sale, function ($value) {
            return $value['enough'];
        }));
        return $sale;
    }

    /**
     * 获取余额充值奖励变动 data 数组
     * @return array
     */
    private function getBalanceAwardData()
    {
        return [
            'member_id'    => $this->rechargeModel->member_id,
            'remark'       => $this->getBalanceAwardRemark(),
            'source'       => ConstService::SOURCE_AWARD,
            'relation'     => $this->array['order_sn'],
            'operator'     => ConstService::OPERATOR_MEMBER,
            'operator_id'  => $this->rechargeModel->member_id,
            'change_value' => $this->getGiveMoney(),
        ];
    }

    /**
     * 获取余额奖励日志
     * @return string
     */
    private function getBalanceAwardRemark()
    {
        if ($this->getProportionStatus()) {
            return '充值满' . $this->enough . '元赠送' . $this->give . '%,(充值金额:' . $this->rechargeModel->money . '元)';
        }
        return '充值满' . $this->enough . '元赠送' . $this->give . '元,(充值金额:' . $this->rechargeModel->money . '元)';
    }

    /**
     * 获取余额奖励设置，比例 或 固定金额
     * @return string
     */
    private function getProportionStatus()
    {
        return $this->balanceSet->proportionStatus();
    }


}
