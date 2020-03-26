<?php
/**
 * Created by PhpStorm.
 * User: CSY
 * Date: 2019/10/16
 * Time: 17:22
 */

namespace app\framework\Log;


class FrontendErrorLog extends BaseLog
{
    protected $logDir = 'logs/error/frontend.log';
    public function add($message, array $content = [])
    {
        $this->log->error($message, $content);
    }
}