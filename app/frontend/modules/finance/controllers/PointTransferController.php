<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/1 下午2:22
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\finance\controllers;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\finance\PointTransfer;
use app\common\services\credit\ConstService;
use app\common\services\finance\PointService;
use app\frontend\models\Member;
use Illuminate\Support\Facades\DB;

class PointTransferController extends ApiController
{
    private $transfer;

    private $transferModel;

    private $poundage;

    private $request_point;
    /**
     * 积分转让接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $result = Setting::get('point.set.point_transfer') ? $this->transferStart() : '未开启积分转让';
//        dd($result);
        return $result === true ? $this->successJson('转让成功') : $this->errorJson($result);
    }

    /**
     * 被转让者信息接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecipientInfo()
    {
        $recipient = $this->getMemberInfo($this->getPostRecipient());
        return $recipient ? $this->successJson('ok',$recipient) : $this->errorJson('未获取到被转让者或被转让者不存在');
    }

    /**
     * 转让开始
     * @return bool|string
     */
    private function transferStart()
    {

        if (!$this->getTransferInfo()) {
            return '未获取到会员信息';
        }
        if (!$this->getMemberInfo($this->getPostRecipient())) {
            return '被转让者不存在';
        }
        if ($this->transfer->uid == $this->getPostRecipient()) {
            return '转让者不能是自己';
        }
        if (bccomp($this->getPostTransferPoint(), 0,2) != 1){
            return '转让积分必须大于 0.01';
        }
        if (bccomp($this->transfer->credit1,$this->getPostTransferPoint(),2) == -1) {
            return '转让积分不能大于您的剩余积分';
        }


        $result = $this->transferRecordSave();
        if ($result !== true) {
            return '转让失败，记录出错';
        }

        DB::beginTransaction();
        (new PointService($this->getTransferRecordData()))->changePoint();
        (new PointService($this->getRecipientRecordData()))->changePoint();

        $result = $this->updateTransferRecordStatus();
        if ($result !== true) {
            DB::rollBack();
            return '转让失败，记录修改出错';
        }
        DB::commit();

        return true;
    }

    /**
     * 转让记录保存
     * @return bool|\Illuminate\Support\MessageBag
     */
    private function transferRecordSave()
    {
        $this->transferModel = new PointTransfer();

        $this->transferModel->fill($this->getTransferData());
        $validator = $this->transferModel->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }

        return $this->transferModel->save();
    }

    /**
     * 修改转让状态
     * @return mixed
     */
    private function updateTransferRecordStatus()
    {
        $this->transferModel->status = ConstService::STATUS_SUCCESS;
        return $this->transferModel->save();
    }

    /**
     * 转让记录数据
     * @return array
     */
    private function getTransferData()
    {
        return [
            'uniacid'       => \YunShop::app()->uniacid,
            'transferor'    => \YunShop::app()->getMemberId(),
            'recipient'     => $this->getPostRecipient(),
            'value'         => $this->getPostTransferPoint(),
            'status'        => ConstService::STATUS_FAILURE,
            'order_sn'      => PointTransfer::createOrderSn('PT'),
            'poundage'      => $this->poundage,
        ];
    }

    private function getTransferRecordData()
    {
        return [
            'point_income_type' => PointService::POINT_INCOME_LOSE,
            'point_mode'        => PointService::POINT_MODE_TRANSFER,
            'member_id'         => $this->transferModel->transferor,
            'point'             => -$this->request_point,
            'remark'            => '积分转让-转出：' . -$this->request_point,
        ];
    }
    private function getRecipientRecordData()
    {
        return [
            'point_income_type' => PointService::POINT_INCOME_GET,
            'point_mode'        => PointService::POINT_MODE_RECIPIENT,
            'member_id'         => $this->transferModel->recipient,
            'point'             => $this->transferModel->value,
            'remark'            => '积分转让-转入：' . $this->transferModel->value,
        ];
    }

    private function getTransferInfo()
    {
        return $this->transfer = $this->getMemberInfo(\YunShop::app()->getMemberId());
    }

    private function getMemberInfo($memberId)
    {
        return Member::select('uid', 'avatar', 'nickname', 'realname', 'credit1')->where('uid',$memberId)->first();
    }

    private function getPostTransferPoint()
    {
        $this->request_point = trim(\YunShop::request()->transfer_point);
        if ($this->getRateSet() > 0) {
            $point = round($this->request_point - $this->request_point * $this->getRateSet(), 2);
            $this->poundage = round($this->request_point * $this->getRateSet(), 2);
            return $point;
        }else{
            return $this->request_point;
        }
    }

    private function getPostRecipient()
    {
        return trim(\YunShop::request()->recipient);
    }

    public function getRateSet()
    {
        return round(Setting::get('point.set.point_transfer_poundage')/100, 4) ?: 0;
    }

}
