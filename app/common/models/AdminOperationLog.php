<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/14
 * Time: 上午10:18
 */

namespace app\common\models;


use app\Jobs\AdminOperationLogQueueJob;

class AdminOperationLog extends BaseModel
{
    protected $table = 'yz_admin_operation_log';
    public $timestamps = true;
    protected $casts = [
        'after' => 'json',
        'before' => 'json',
    ];

    public function save(array $options = [])
    {
        $this->ip = request()->ip();
        $this->admin_uid = \YunShop::app()->uid;
        $this->uid = \YunShop::app()->getMemberId();
        (new AdminOperationLogQueueJob($this->getAttributes()))->handle();
    }
}