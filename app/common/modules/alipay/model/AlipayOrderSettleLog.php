<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/11/28
 * Time: 16:44
 */

namespace app\common\modules\alipay\models;


use app\common\models\BaseModel;

class AlipayOrderSettleLog extends BaseModel
{
    public $table = 'yz_alipay_order_settle_log';
    public $guarded = [''];

}