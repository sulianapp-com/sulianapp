<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/9/18
 * Time: 16:35
 */

namespace app\common\events\finance;


use app\common\events\Event;

class PointChangeEvent extends Event
{
    public $pointModel;

    public function __construct($pointModel)
    {
        $this->pointModel = $pointModel;
    }

    public function getPointModel()
    {
        return $this->pointModel;
    }
}