<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/3/31
 * Time: 10:36 PM
 */

namespace app\common\services\finance;


use app\common\models\UniAccount;
use app\Jobs\PointQueueJob;

class PointQueueService
{
    /**
     * 当前时间：商品赠送积分每月赠送验证参数
     *
     * @var string
     */
    private $nowTime;


    public function __construct()
    {
        $this->nowTime = $this->nowTime();
    }


    public function handle()
    {
        $uniAccount = UniAccount::getEnable() ?: [];
        foreach ($uniAccount as $u) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $u->uniacid;

            $this->pointQueue();
        }
    }

    private function pointQueue()
    {
        if ($this->isRun()) {
            dispatch(new PointQueueJob(\YunShop::app()->uniacid));
        }
    }

    /**
     * 是否运行
     *
     * @return bool
     */
    private function isRun()
    {
        if (date('d') != 1) {
            return false;
        }
        if ($this->isFinish()) {
            return false;
        }

        $this->setTime();

        return true;
    }

    private function setTime()
    {
        $setLog['return_at'] = $this->nowTime;
        \Setting::set('point_queue.return_log', $setLog);
    }

    /**
     * 是否已经执行完成
     *
     * @return bool
     */
    private function isFinish()
    {
        $lastRunTime = $this->lastRunTime();

        return $this->nowTime == $lastRunTime;
    }

    /**
     * 最会一次执行时间
     *
     * @return string|null
     */
    private function lastRunTime()
    {
        $setLog = \Setting::get('point_queue.return_log');

        return $setLog['return_at'];
    }

    /**
     * 当前时间（验证格式）
     *
     * @return string
     */
    private function nowTime()
    {
        return date('y') . '-' . date('m') . '-' . date('d');
    }
}
