<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/4/10
 * Time: 11:25
 */

namespace app\common\events\member;


use app\common\events\Event;

class PluginCreateRelationEvent extends Event
{
    protected $parent_id;

    protected $model;

    protected $mark_id;

    protected $mark;

    public function __construct($mid, $model, $mark, $mark_id)
    {
        $this->parent_id = $mid;
        $this->model = $model;
        $this->mark_id = $mark_id;
        $this->mark = $mark;
    }

    public function getParentId()
    {
        return $this->parent_id;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getMarkId()
    {
        return $this->mark_id;
    }

    public function getMark()
    {
        return $this->mark;
    }
}