<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 22/02/2017
 * Time: 14:10
 */

namespace app\backend\modules\member\models;


class TestMember extends \app\common\models\TestMember
{
    protected $casts = [
        'ext' => 'string',
    ];
    public function getExtAttribute()
    {
        return 'a';
    }
}
