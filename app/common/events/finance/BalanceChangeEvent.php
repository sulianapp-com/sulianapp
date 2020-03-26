<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/9/18
 * Time: 16:35
 */

namespace app\common\events\finance;


use app\common\events\Event;

class BalanceChangeEvent extends Event
{
    public $BalanceModel;

    public function __construct($balanceModel)
    {
        $this->BalanceModel = $balanceModel;
    }

    public function getBalanceModel()
    {
        return $this->BalanceModel;
    }
}