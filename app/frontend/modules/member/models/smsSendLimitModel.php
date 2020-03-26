<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/3/3
 * Time: 上午7:20
 */

namespace app\frontend\modules\member\models;

use app\backend\models\BackendModel;

class smsSendLimitModel extends BackendModel
{
    public $table = 'yz_sms_send_limit';
    public $timestamps = false;

    public static function getMobileInfo($uniacid, $mobile)
    {
        return self::where('uniacid', $uniacid)
                   ->where('mobile', $mobile)
                   ->first();
    }

    /**
     * 添加数据
     *
     * @param $data
     */
    public static function insertData($data)
    {
        self::insert($data);
    }

    /**
     * 更新更新短信条数，时间
     *
     * @param $where
     * @param $data
     */
    public static function updateData($where, $data)
    {
        self::where('uniacid', $where['uniacid'])
            ->where('mobile', $where['mobile'])
            ->update($data);
    }
}