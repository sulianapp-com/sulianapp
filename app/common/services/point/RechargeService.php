<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/22
 * Time: 下午4:19
 */

namespace app\common\services\point;


use app\common\exceptions\ShopException;
use app\common\models\point\RechargeModel;
use app\common\services\finance\PointService;
use Illuminate\Support\Facades\DB;

class RechargeService
{
    /**
     * @var RechargeModel
     */
    private $rechargeModel;

    public function __construct(RechargeModel $rechargeModel)
    {
        $this->rechargeModel = $rechargeModel;
    }


    public function tryRecharge()
    {
        DB::transaction(function () {
            $this->_tryRecharge();
        });
        return true;
    }

    /**
     * @throws ShopException
     */
    private function _tryRecharge()
    {
        $result = $this->updateMemberPoint();
        if (!$result) {
            throw new ShopException('充值失败：更新数据失败');
        }
        $result = $this->updateRechargeStatus();
        if (!$result) {
            throw new ShopException('充值失败：修改充值状态失败');
        }
    }

    /**
     * @return PointService|bool
     * @throws ShopException
     */
    private function updateMemberPoint()
    {
        return (new PointService($this->getChangeData()))->changePoint();
    }

    /**
     * @return bool
     */
    private function updateRechargeStatus()
    {
        $this->rechargeModel->status = RechargeModel::STATUS_SUCCESS;
        return $this->rechargeModel->save();
    }

    /**
     * @return array
     */
    private function getChangeData()
    {
        return [
            'point_mode'        => PointService::POINT_MODE_ADMIN,
            'member_id'         => $this->rechargeModel->member_id,
            'point'             => $this->rechargeModel->money,
            'remark'            => $this->rechargeRemark(),
            'point_income_type' => $this->pointIncomeType()
        ];
    }

    /**
     * @return string
     */
    private function rechargeRemark()
    {
        return "充值变动['{$this->rechargeModel->money}']积分，充值记录ID【{$this->rechargeModel->id}】";
    }

    /**
     * @return int
     */
    private function pointIncomeType()
    {
        return $this->rechargeModel->money < 0 ? PointService::POINT_INCOME_LOSE : PointService::POINT_INCOME_GET;
    }
}
