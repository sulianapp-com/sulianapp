<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/12/17
 * Time: 15:31
 */

namespace app\common\events\finance;


use app\common\events\Event;

class LoveOzyRecordEvent extends Event
{
    public $record;

    public function __construct($record)
    {
        $this->record = $record;
    }

    public function getRecord()
    {
        return $this->record;
    }
}