<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/3
 * Time: 14:34
 */

namespace app\common\events\member;

use app\common\events\Event;

class MemberDelEvent extends Event
{
    protected $uid;


    public function __construct($uid)
    {

        $this->uid = $uid;
    }


    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }
}