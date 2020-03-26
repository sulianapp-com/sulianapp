<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/11/28
 * Time: 16:44
 */

namespace app\common\modules\wechat\models;


use app\common\models\BaseModel;

/**
 * @property int account_id
 * @property boolean profit_sharing
 * @property string transaction_id
 * Class WechatPayOrder
 * @package app\common\modules\wechat\models
 */
class WechatPayOrder extends BaseModel
{
    public $table = 'yz_wechat_pay_order';
    public $guarded = [''];

}