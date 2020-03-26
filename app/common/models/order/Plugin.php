<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/8/1
 * Time: 上午10:55
 */

namespace app\common\models\order;


use app\common\models\BaseModel;

class Plugin extends BaseModel
{
    public $table = 'yz_plugin_order';
    protected $guarded = [''];
}