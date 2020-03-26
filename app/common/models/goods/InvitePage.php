<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 9:28
 */

namespace app\common\models\goods;


use app\common\models\BaseModel;

class InvitePage extends BaseModel
{
    protected $table = 'yz_invite_page';

    protected $guarded = [''];

    public static function getDataByGoodsId($goods_id)
    {
        return self::where('goods_id', $goods_id)->first();
    }
}