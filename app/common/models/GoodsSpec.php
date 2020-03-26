<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/1
 * Time: 09:41
 */

namespace app\common\models;

/**
 * Class GoodsSpec
 * @package app\common\models
 * @property int uniacid
 * @property int goods_id
 */
class GoodsSpec extends \app\common\models\BaseModel
{
    public $table = 'yz_goods_spec';
    
    public $guarded = [];

    //public $timestamps = false;

    public function hasManySpecsItem()
    {
        return $this->hasMany('app\common\models\GoodsSpecItem','specid','id');
    }
}