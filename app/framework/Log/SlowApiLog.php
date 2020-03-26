<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/20
 * Time: 2:57 PM
 */

namespace app\framework\Log;

class SlowApiLog extends BaseLog
{
    protected $logDir = 'logs/slow-api/slow-api.log';
    public function add($message, array $content = [])
    {
        $this->log->info($message, $content);
    }
}