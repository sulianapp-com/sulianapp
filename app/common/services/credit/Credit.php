<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/15
 * Time: 上午8:56
 */

namespace app\common\services\credit;


use app\common\events\finance\LoveChangeEvent;
use app\common\exceptions\ShopException;
use app\framework\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

abstract class Credit
{
    protected $data =[];

    protected $type = ConstService::TYPE_INCOME;

    protected $source;

    protected $change_value;



    protected $memberModel;

    //abstract function changeValue();

    abstract function getMemberModel();

    abstract function recordSave();

    abstract function updateMemberCredit();

    abstract function validatorData();

    //abstract function validatorOzy();


    /**
     * 充值接口
     * @param array $data
     * @return string
     */
    public function recharge(array $data)
    {
        $this->source = ConstService::SOURCE_RECHARGE;
        return $this->addition($data);
    }

    /**
     * 后台扣除
     * @param array $data
     * @return string
     */
    public function rechargeMinus(array $data)
    {
        $this->source = ConstService::SOURCE_RECHARGE_MINUS;
        return $this->subtraction($data);
    }

    /**
     * 消费接口
     * @param array $data
     * @return string
     */
    public function consume(array $data)
    {
        $this->source = ConstService::SOURCE_CONSUME;
        return $this->subtraction($data);
    }

    /**
     * 转让接口
     * @param array $data
     * @return string
     */
    public function transfer(array $data)
    {
        $this->source = ConstService::SOURCE_TRANSFER;
        return $this->subtraction($data);
    }

    /**
     * 余额转化爱心值
     * @param array $data
     * @return bool|string
     */
    public function convert(array $data)
    {
        $this->source = ConstService::SOURCE_CONVERT;
        return $this->subtraction($data);
    }

    /**
     * 余额转化爱心值回滚
     * @param array $data
     * @return bool|string
     */
    public function convertCancel(array $data)
    {
        $this->source = ConstService::SOURCE_CONVERT_CANCEL;
        return $this->addition($data);
    }

    /**
     * 转让收入接口
     * @param array $data
     * @return string
     */
    public function recipient(array $data)
    {
        $this->source = ConstService::SOURCE_TRANSFER;
        return $this->addition($data);
    }

    /**
     * 抵扣接口
     * @param array $data
     * @return string
     */
    public function deduction(array $data)
    {
        $this->source = ConstService::SOURCE_DEDUCTION;
        return $this->subtraction($data);
    }

    /**
     * 奖励接口
     * @param array $data
     * @return string
     */
    public function award(array $data)
    {
        $this->source = ConstService::SOURCE_AWARD;
        return $this->addition($data);
    }

    /**
     * 充值消费积分赠送爱心值
     * @param array $data
     * @return string
     */
    public function integralAward(array $data)
    {
        $this->source = ConstService::SOURCE_AWARD;
        return $this->addition($data);
    }

    /**
     * 提现接口
     * @param array $data
     * @return string
     */
    public function withdrawal(array $data)
    {
        $this->source = ConstService::SOURCE_WITHDRAWAL;
        return $this->subtraction($data);
    }

    /**
     * 提现至………（余额）………接口
     * @param array $data
     * @return string
     */
    public function income(array $data)
    {
        $this->source = ConstService::SOURCE_INCOME;
        return $this->addition($data);
    }

    /**
     * 抵扣取消回滚接口
     * @param array $data
     * @return string
     */
    public function cancelDeduction(array $data)
    {
        $this->source = ConstService::SOURCE_CANCEL_DEDUCTION;
        return $this->addition($data);
    }

    /**
     * 奖励取消回滚接口
     * @param array $data
     * @return string
     */
    public function cancelAward(array $data)
    {
        $this->source = ConstService::SOURCE_CANCEL_AWARD;
        return $this->subtraction($data);
    }

    /**
     * 消费取消回滚接口
     * @param array $data
     * @return string
     */
    public function cancelConsume(array $data)
    {
        \Log::debug("消费取消回滚接口",$data);
        $this->source = ConstService::SOURCE_CANCEL_CONSUME;
        return $this->addition($data);
    }

    /**
     * 抽奖获得余额
     * @param array $data
     * @return string
     */
    public function DrawGet(array $data)
    {
        $this->source = ConstService::SOURCE_DRAW_CHARGE;
        return $this->addition($data);
    }

    /**
     * 抽奖奖励余额
     * @param array $data
     * @return string
     */
    public function DrawReward(array $data)
    {
        $this->source = ConstService::SOURCE_DRAW_REWARD;
        return $this->addition($data);
    }

    //第三方同步
    public function ThirdSynchronization(array $data)
    {
        $this->source = ConstService::SOURCE_THIRD_SYN;
        return $this->addition($data);
    }

    //加法
    protected function addition($data)
    {
        if (!isset($data['change_value']) || $data['change_value'] < 0) {
            return '变动值必须是正数';
        }

        $this->data = $data;
        $this->type = ConstService::TYPE_INCOME;
        $this->change_value = $this->data['change_value'];
        \Log::debug("监听加法",$this->change_value);

        event(new LoveChangeEvent($data['member_id']));

        return $this->result();
    }

    //加法
    protected function ozyAddition($data)
    {
        if (!isset($data['change_value']) || $data['change_value'] < 0) {
            return '变动值必须是正数';
        }

        $this->data = $data;
        $this->type = ConstService::TYPE_INCOME;
        $this->change_value = $this->data['change_value'];
        \Log::debug("监听加法",$this->change_value);

        event(new LoveChangeEvent($data['member_id']));

        return $this->result();
    }

    //减法
    protected function subtraction($data)
    {
        if (!isset($data['change_value']) || $data['change_value'] < 0) {
            return '变动值必须是正数';
        }
        $this->data = $data;
        $this->type = ConstService::TYPE_EXPENDITURE;
        $this->change_value = -$this->data['change_value'];

        return $this->result();
    }

    protected function ozySubtraction($data)
    {
        if (!isset($data['change_value']) || $data['change_value'] < 0) {
            return '变动值必须是正数';
        }
        $this->data = $data;
        $this->type = ConstService::TYPE_EXPENDITURE;
        $this->change_value = -$this->data['change_value'];

        return $this->result();
    }


    //余额提现驳回
    public function rejected($data)
    {
        $this->source = ConstService::SOURCE_REJECTED;
        return $this->addition($data);
    }

    public function excelRecharge(array $data)
    {
        $this->source = ConstService::SOURCE_EXCEL_RECHARGE;
        return $this->addition($data);
    }


    protected function result()
    {
//        if ($status == 1) {
            DB::transaction(function () {
                $this->_result();
            });
//        } else {
//            DB::transaction(function () {
//                $this->_ozy_result();
//            });
//        }


        return true;
    }


    //todo 应该改为私有
    protected function _result()
    {
        $this->memberModel = $this->getMemberModel();
        if (!$this->memberModel) {
            throw new ShopException("未获取到会员数据");
        }
        $validator = $this->validatorData();
        if (!($validator === true)) {
            throw new ShopException("$validator");
        }

        $result = $this->recordSave();
        if (!$result) {
            throw new ShopException("数据写入错误：CREDIT_RECORD");
        }

        $result = $this->updateMemberCredit();
        if (!$result) {
            throw new ShopException("数据写入错误：CREDIT_UPDATE");
        }
        return true;
    }

//    //todo 应该改为私有
//    protected function _ozy_result()
//    {
//        $this->memberModel = $this->getMemberModel();
//        if (!$this->memberModel) {
//            throw new ShopException("未获取到会员数据");
//        }
//
//        $validator = $this->validatorOzy();
//        if (!($validator === true)) {
//            throw new ShopException("$validator");
//        }
//
//        $result = $this->recordSave();
//        if (!$result) {
//            throw new ShopException("数据写入错误：CREDIT_RECORD");
//        }
//
//        $result = $this->updateMemberCredit();
//        if (!$result) {
//            throw new ShopException("数据写入错误：CREDIT_UPDATE");
//        }
//        return true;
//    }


}
