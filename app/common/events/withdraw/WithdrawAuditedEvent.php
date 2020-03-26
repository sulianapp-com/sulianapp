<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/15 上午10:55
 * Email: livsyitian@163.com
 */

namespace app\common\events\withdraw;


use app\common\events\WithdrawEvent;

class WithdrawAuditedEvent extends WithdrawEvent
{
    /**
     * 审核事件使用需要注意
     *
     * 1，审核事件包含（审核事件、重新审核事件）
     *
     * 2，该状态时，提现记录属于修改完成状态
     */
}
