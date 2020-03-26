<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20
 * Time: 16:51
 */

namespace app\backend\modules\discount\models;


use app\common\models\BaseModel;

class DispatchClassify extends BaseModel
{
    public $table = 'yz_dispatch_classify';
    public $guarded = [''];

    public $casts = [
        'discount_value' => 'json'
    ];

}