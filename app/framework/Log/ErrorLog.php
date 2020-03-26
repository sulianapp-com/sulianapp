<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/20
 * Time: 2:57 PM
 */

namespace app\framework\Log;

class ErrorLog extends BaseLog
{
    protected $logDir = 'logs/error/error.log';
    public function add($message, array $content = [])
    {
        $this->log->error($message, $content);
    }
}