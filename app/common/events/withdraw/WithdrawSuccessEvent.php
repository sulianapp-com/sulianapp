<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/5 下午5:29
 * Email: livsyitian@163.com
 */

namespace app\common\events\withdraw;


use app\common\events\Event;
use app\common\exceptions\ShopException;
use app\common\models\Withdraw;

class WithdrawSuccessEvent extends Event
{
    private $withdraw_sn;


    private $withdrawModel;


    public function __construct($withdraw_sn)
    {
        $this->withdraw_sn = $withdraw_sn;
        $this->setWithdrawModel();
    }


    public function getWithdrawModel()
    {
        return $this->withdrawModel;
    }


    private function setWithdrawModel()
    {
        $withdrawModel = Withdraw::where('withdraw_sn', $this->withdraw_sn)->first();

        if (!$withdrawModel) {
            throw new ShopException("提现记录不存在，单号：{$this->withdraw_sn}");
        }
        $this->withdrawModel = $withdrawModel;
    }

}