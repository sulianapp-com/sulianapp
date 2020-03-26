<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/29
 * Time: 4:15 PM
 */

namespace app\common\modules\discount;

use app\common\models\BaseModel;

/**
 * Class DiscountLog
 * @package app\common\modules\discount
 * @property string code
 * @property float amount
 * @property string name
 * @property string note
 * @property array extra
 */
class GoodsMemberDiscountLog extends BaseModel
{
    protected $guarded = ['id'];
}