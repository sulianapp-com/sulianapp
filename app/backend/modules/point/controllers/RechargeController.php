<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/10/22
 * Time: 上午11:48
 */

namespace app\backend\modules\point\controllers;


use app\backend\modules\member\models\Member;
use app\backend\modules\point\models\RechargeModel;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;
class RechargeController extends BaseController
{
    /**
     * @var Member
     */
    private $memberModel;

    /**
     * @var RechargeModel
     */
    private $rechargeModel;


    public function preAction()
    {
        parent::preAction();
        $this->memberModel = $this->getMemberModel();
    }


    public function index()
    {
        $value = $this->getPostValue();
        if ($value) {
            return $this->recharge();
        }

        return view('point.recharge', $this->getResultData());
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function recharge()
    {
        $result = $this->tryRecharge();

        if ($result === true && $this->rechargeModel->status == RechargeModel::STATUS_SUCCESS) {
            $temp_id = \Setting::get('shop.notice.point_change');

            $params = [
                ['name' => '时间', 'value' => date('Y-m-d H:i:s')],
                ['name' => '积分变动金额', 'value' => $this->getRechargeData()['money'] ],
                ['name' => '积分变动类型', 'value' => $this->rechargeModel->getTypeNameComment($this->rechargeModel->type) ],
                ['name' => '变动后积分数值', 'value' => $this->memberModel->credit1+$this->getRechargeData()['money']]
            ];

            $msg = \app\common\models\notice\MessageTemp::getSendMsg($temp_id, $params);

            $news_link = MessageTemp::find($temp_id)->news_link;
            $news_link = $news_link ?:'';
            \app\common\services\MessageService::notice($temp_id, $msg, $this->memberModel->uid, \YunShop::app()->uniacid,$news_link);

            return $this->message('积分充值成功', $this->successUrl());
        }
        return view('point.recharge', $this->getResultData());
    }

    /**
     * @return bool|\Laracasts\Flash\FlashNotifier
     */
    private function tryRecharge()
    {
        $this->rechargeModel = new RechargeModel();

        $this->rechargeModel->fill($this->getRechargeData());
        $validator = $this->rechargeModel->validator();
        if ($validator->fails()) {
            return $this->error($validator->messages()->first());
        }
        return $this->rechargeModel->save();
    }

    /**
     * @return array
     */
    private function getRechargeData()
    {
        return [
            'type'          => 0,       //todo 后台充值、商城付款，应该在支付模型中设置常量
            'money'         => $this->getPostValue(),
            'status'        => RechargeModel::STATUS_ERROR,
            'remark'        => $this->getPostRemark(),
            'uniacid'       => \YunShop::app()->uniacid,
            'member_id'     => $this->memberModel->uid,
            'order_sn'       => RechargeModel::createOrderSn('RP','order_sn')
        ];
    }

    private function successUrl()
    {
        return Url::absoluteWeb('point.recharge.index',array('id' => $this->memberModel->uid));
    }

    /**
     * @return array
     */
    private function getResultData()
    {
        return [
            'memberInfo'    => $this->memberModel,
            'rechargeMenu'  => $this->getRechargeMenu()
        ];
    }

    /**
     * @return mixed
     * @throws ShopException
     */
    private function getMemberModel()
    {
        $member_id = $this->getMemberId();

        $memberModel = Member::getMemberInfoById($member_id);
        if (!$memberModel) {
            throw new ShopException('会员不存在');
        }
        return $memberModel;
    }

    /**
     * @return int
     * @throws ShopException
     */
    private function getMemberId()
    {
        $member_id = (int)\YunShop::request()->id;
        if (!$member_id) {
            throw new ShopException('参数错误');
        }
        return $member_id;
    }

    /**
     * @return double
     */
    private function getPostValue()
    {
        return \YunShop::request()->point;
    }

    /**
     * @return string
     */
    private function getPostRemark()
    {
        return \YunShop::request()->remark;
    }

    /**
     * @return array
     */
    private function getRechargeMenu()
    {
        return array(
            'title' => '积分充值',
            'name' => '粉丝',
            'type' => 'point',
            'profile' => '会员信息',
            'old_value' => '当前积分',
            'charge_value' => '充值积分'
        );
    }
}
