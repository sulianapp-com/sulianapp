<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/4/25
 * Time: 10:29
 */

namespace app\backend\modules\goods\models;


class ReturnAddress extends \app\common\models\goods\ReturnAddress
{
    static protected $needLog = true;

    public static function getOneByDefault()
    {
        return self::uniacid()->where('is_default', 1)
            ->first();
    }

    /**
     * 获取配送模板单条数据
     * @param int $goodsId
     * @return array
     */
    public static function getOne($id)
    {
        return self::uniacid()->where('id', $id)
            ->first();
    }

    public static function getOneByPluginsId($id = 0, $store_id = 0, $supplier_id = 0)
    {
        return self::uniacid()
            ->where('plugins_id', $id)
            ->where('store_id', $store_id)
            ->where('supplier_id', $supplier_id)
            ->where('is_default', 1)
            ->first();
    }
}