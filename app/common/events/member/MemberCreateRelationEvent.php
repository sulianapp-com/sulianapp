<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/30
 * Time: 下午4:17
 */

namespace app\common\events\member;



use app\common\events\Event;

class MemberCreateRelationEvent extends Event
{
    protected $uid;
    protected $parent_id;
    protected $member;

    public function __construct($model, $parent_id)
    {
        if (is_int($model)) {
            $this->uid       = $model;
        } else {
            $this->member = $model;
            $this->uid    = $model->member_id;
        }

        $this->parent_id = $parent_id;
    }

    public function getUid()
    {
        return $this->uid;
    }

    public function getParentId()
    {
        return $this->parent_id;
    }

    public function getMemberModel()
    {
        return $this->member;
    }
}
