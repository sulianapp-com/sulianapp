<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/31
 * Time: 11:47 PM
 */

namespace app\common\models;

/**
 * Class OrderRequest
 * @package app\common\models
 * @property int order_id
 * @property array request
 */
class OrderRequest extends BaseModel
{
    protected $table = 'yz_order_request';
    protected $guarded = ['id'];
    protected $casts = [
        'request' => 'json',
    ];
}