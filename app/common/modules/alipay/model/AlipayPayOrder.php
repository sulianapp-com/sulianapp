<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/11/28
 * Time: 16:44
 */

namespace app\common\modules\alipay\models;


use app\common\models\BaseModel;

/**
 * @property int account_id
 * @property boolean profit_sharing
 * @property string transaction_id
 * Class WechatPayOrder
 * @package app\common\modules\alipay\models
 */
class AlipayPayOrder extends BaseModel
{
    public $table = 'yz_alipay_pay_order';
    public $guarded = [''];

}