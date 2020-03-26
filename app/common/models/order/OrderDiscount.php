<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/7/25
 * Time: 下午7:10
 */

namespace app\common\models\order;

use app\common\models\BaseModel;

/**
 * Class OrderDiscount
 * @property int id
 * @property int uid
 * @property int code
 * @property int amount
 * @package app\common\models\order
 */
class OrderDiscount extends BaseModel
{
    public $table = 'yz_order_discount';
    protected $fillable = [];
    protected $guarded = ['id'];

    public function save(array $options = [])
    {
        $this->amount = (float)$this->amount;
        return parent::save($options);
    }
}