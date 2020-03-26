<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/5 下午3:33
 * Email: livsyitian@163.com
 */

namespace app\common\events;


use app\common\models\Withdraw;

class WithdrawEvent extends Event
{
    private $withdraw;


    public function __construct(Withdraw $withdraw)
    {
        $this->withdraw = $withdraw;
    }


    public function getWithdrawModel()
    {
        return $this->withdraw;
    }


}
