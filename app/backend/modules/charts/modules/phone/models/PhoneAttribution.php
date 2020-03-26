<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 15:59
 */

namespace app\backend\modules\charts\modules\phone\models;


use app\common\models\BaseModel;

class PhoneAttribution extends BaseModel
{
    protected $table = 'yz_phone_attribution';
    protected $guarded = [''];
    protected $fillable = [];

    public $timestamps = true;

    public static function getMemberByID($uid)
    {
        return self::uniacid()->where('uid', $uid)->first();
    }
}