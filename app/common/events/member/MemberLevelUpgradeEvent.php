<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/27
 * Time: 0:42
 */

namespace app\common\events\member;


use app\common\events\Event;
use app\common\models\MemberShopInfo;

class MemberLevelUpgradeEvent extends Event
{
    protected $memberModel;
    protected $isManual;

    public function __construct(MemberShopInfo $memberModel, $isManual)
    {
        $this->memberModel = $memberModel;
        $this->isManual = $isManual;
    }

    public function getMemberModel()
    {
        return $this->memberModel;
    }

    public function isManual()
    {
        return $this->isManual;
    }

}