<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2018/1/9 下午1:44
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\controllers;


use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\Income;


/**
 * 收入接口重构 2018-01-09 YITIAN
 * Class EarningController
 * @package app\frontend\modules\finance\controllers
 */
class EarningController extends ApiController
{

    private $incomeModel;


    public function preAction()
    {
        parent::preAction();
        $this->incomeModel = Income::uniacid()->where('member_id', $this->getMemberId());

    }

    /**
     * 收入页面数据显示接口
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function earningCount()
    {
        $result = [
            'total' => [
                'grand_total' => $this->grandTotal(),
                'can_withdraw' => $this->canWithdrawTotal()
            ],
            'data' => $this->earningDetail(),
        ];

        return $this->successJson('ok', $result);
    }


    //累计收入
    private function grandTotal()
    {
        return $this->incomeModel->sum('amount');
    }

    //可提现收入
    private function canWithdrawTotal()
    {
        return $this->incomeModel->where('status', 0)->sum('amount');
    }

    //收入明细
    private function earningDetail()
    {
        $config = \app\common\modules\shop\ShopConfig::current()->get('plugin');

        $array = [];
        foreach ($config as $key => $item) {

            //$typeModel = $this->incomeModel->where('incometable_type', $item['class']);
            $typeModel = Income::uniacid()->where('member_id', $this->getMemberId())->whereStatus(0)->where('incometable_type', $item['class']);
            $array[] = [
                'title' => $item['title'],
                'ico' => $item['ico'],
                'type' => $item['type'],
                'type_name' => $item['title'],
                'income' => $typeModel->sum('amount'),
                'can' => $this->itemIsShow($item)
            ];
        }
        return $array;
    }


    private function itemIsShow($item)
    {
        $result = true;
        if ($item['agent_class']) {
            $agentModel = $item['agent_class']::{$item['agent_name']}($this->getMemberId());

            if ($item['agent_status']) {
                $agentModel = $agentModel->where('status', 1);
            }

            //推广中心显示
            if (!$agentModel) {
                $result = false;
            } else {
                $agent = $agentModel->first();
                if ($agent) {
                    $result = true;
                } else {
                    $result = false;
                }
            }
        }
        return $result;
    }

    //
    private function getMemberId()
    {
        $member_id = \YunShop::app()->getMemberId();

        if (!$member_id) {
            throw new AppException('未获取到会员数据');
        }
        return $member_id;
    }


}
