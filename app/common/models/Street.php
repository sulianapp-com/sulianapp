<?php

namespace app\common\models;


/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 上午9:11
 */
class Street extends BaseModel
{

    public $table = 'yz_street';

    protected $guarded = [''];

    protected $fillable = [''];

    public $timestamps = false;


    public static function getStreetByParentId($parentId)
    {
        return self::where('parentid', $parentId)
            ->where('level', '4')
            ->get();
    }

    public function isLeaf()
    {
        return true;
    }

}
