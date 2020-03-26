<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/9/27
 * Time: 16:53
 */

namespace app\common\events\finance;


use app\common\events\Event;

class PetEvent extends Event
{
    public $data = [];

    public function setPet($data)
    {
        $this->data[] = $data;
    }

    public function getPet()
    {
        return $this->data;
    }
}