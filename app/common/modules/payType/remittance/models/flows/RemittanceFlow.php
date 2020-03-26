<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/14
 * Time: 下午5:31
 */

namespace app\common\modules\payType\remittance\models\flows;

use app\common\models\Flow;

class RemittanceFlow extends Flow
{
    const STATE_WAIT_REMITTANCE = 'waitRemittance';
    const STATE_WAIT_RECEIPT = 'waitReceipt';
}