<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/22 上午11:26
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\Jobs;


use app\common\services\finance\PointToLoveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PointToLoveJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $uniacid;

    public function __construct($uniacid)
    {
        $this->uniacid = $uniacid;
    }

    public function handle()
    {
        (new PointToLoveService())->handleTransferQueue($this->uniacid);
    }

}
