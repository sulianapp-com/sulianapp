<?php
namespace app\common\models;


/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 上午9:11
 */
class Address extends BaseModel
{

    public $timestamps = false;

    public $table = 'yz_address';

    protected $guarded = [''];

    protected $fillable = [''];


    public static function getProvince()
    {
        return self::where('level', '1')->get();
    }

    public static function getCityByParentId($parentId)
    {
        return self::where('parentid', $parentId)
            ->where('level', '2')
            ->get();
    }

    public static function getAreaByParentId($parentId)
    {
        return self::where('parentid', $parentId)
            ->where('level', '3')
            ->get();
    }

    public static function getAddress($data)
    {
        return self::whereIn('id', $data)
            ->get();
    }
}
