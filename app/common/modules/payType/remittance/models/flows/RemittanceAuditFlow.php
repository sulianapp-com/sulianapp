<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/14
 * Time: 下午5:31
 */

namespace app\common\modules\payType\remittance\models\flows;


use app\common\models\Flow;

class RemittanceAuditFlow extends Flow
{
    const CODE = 'remittanceAudit';
    const STATE_WAIT_AUDIT = 'waitAudit';

    protected static function boot()
    {
        parent::boot();
        self::addGlobalScope(function ($query) {
            $query->where('code',self::CODE);
        });
    }
}