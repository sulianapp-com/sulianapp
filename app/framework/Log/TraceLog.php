<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/10/26
 * Time: 上午11:01
 */

namespace app\framework\Log;

class TraceLog
{
    private $enable = false;

    public function __construct()
    {
        $this->enable = isset($_GET['debug_log']);
    }

    private function enable($key)
    {
        if(!$this->enable){
            return false;
        }
        if($_GET['debug_log'] == '*'){
            return true;
        }

        if(is_array(json_decode($_GET['debug_log'])) && in_array($key,json_decode($_GET['debug_log'],true))){
            return true;
        }
        return false;
    }

    public function coupon($key, $value)
    {
        if (!$this->enable('coupon')) {
            return;
        }
        file_put_contents($this->getFileName('coupon'), "{$key}:{$value}" . PHP_EOL, FILE_APPEND);

    }

    public function deduction($key, $value)
    {
        if (!$this->enable('deduction')) {
            return;
        }
        file_put_contents($this->getFileName('deduction'), "{$key}:{$value}" . PHP_EOL, FILE_APPEND);
    }
    public function freight($key, $value)
    {
        if (!$this->enable('freight')) {
            return;
        }
        file_put_contents($this->getFileName('freight'), "{$key}:{$value}" . PHP_EOL, FILE_APPEND);
    }
    private function getFileName($name)
    {

        return storage_path("logs/trace/{$name}.log");
    }
}