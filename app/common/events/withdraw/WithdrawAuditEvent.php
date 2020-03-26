<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/6/15 上午10:54
 * Email: livsyitian@163.com
 */

namespace app\common\events\withdraw;


use app\common\events\WithdrawEvent;

class WithdrawAuditEvent extends WithdrawEvent
{
    /**
     * 审核事件使用需要注意
     *
     * 1，审核事件包含（审核事件、重新审核事件）
     *
     * 2，该状态时，提现记录数据属于未修改状态（即申请后的状态数据）
     */
}
