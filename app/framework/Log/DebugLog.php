<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/20
 * Time: 2:57 PM
 */

namespace app\framework\Log;

class DebugLog extends BaseLog
{
    protected $logDir = 'logs/debug/debug.log';
    public function add($message, array $content = [])
    {
        $this->log->debug($message, $content);
    }
}