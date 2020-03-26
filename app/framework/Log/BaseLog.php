<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/20
 * Time: 3:32 PM
 */

namespace app\framework\Log;

use Monolog\Logger;
use Illuminate\Log\Writer;

abstract class BaseLog
{
    protected $logDir = '';
    protected $days = 7;
    /**
     * @var Writer;
     */
    protected $log;

    public function __construct()
    {
        $this->log = new Writer(new Logger(config('app.env')));
        $this->log->useDailyFiles(storage_path() . '/'.$this->logDir, $this->days);
    }

    abstract public function add($message, array $content = []);
    public function getLogger(){
        return $this->log;
    }
}