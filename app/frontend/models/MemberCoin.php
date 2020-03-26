<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 下午5:40
 */

namespace app\frontend\models;

use app\common\models\VirtualCoin;

abstract class MemberCoin
{
    /**
     * @var Member
     */
    protected $member;

    function __construct(\app\common\models\Member $member)
    {
        $this->member = $member;
    }

    /**
     * @return VirtualCoin $coin
     */
    abstract public function getMaxUsableCoin();

    abstract public function lockCoin($coin);

    /**
     * @param VirtualCoin $coin
     * @return bool
     */
    abstract public function consume(VirtualCoin $coin, $data);
}