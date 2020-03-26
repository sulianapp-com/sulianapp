<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/23
 * Time: 上午10:25
 */

namespace app\common\models;


class OrderRefund extends BaseModel
{
    protected $casts = [
        'images' => 'json',
        'refund_proof_imgs' => 'json',
    ];
    protected $attributes = [
        'images' => [],
        'reason' => '',
        'content' => '',
        'reply' => '',
        'refund_proof_imgs' => [],
        'remark' => ''
    ];
}