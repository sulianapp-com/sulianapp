<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 上午10:42
 */

/**
 * 微信开放平台Unionid表
 */
namespace app\frontend\modules\member\models;

use app\backend\models\BackendModel;

class MemberUniqueModel extends BackendModel
{
    public $table = 'yz_member_unique';

    public $dateFormat  = 'U';

    protected $guarded = [''];

    protected $primaryKey = 'unique_id';

    /**
     * 检查是否存在unionid
     *
     * @param $uniacid
     * @param $unionid
     * @return mixed
     */
    public static function getUnionidInfo($uniacid, $unionid)
    {
        return self::uniacid()
            ->where('unionid', $unionid)
            ->orderby('unique_id', 'desc');
    }

    public static function getUnionidInfoByMemberId($uniacid, $member_id)
    {
        return self::where('uniacid', $uniacid)
            ->where('member_id', $member_id)
            ->orderby('unique_id', 'desc');
    }

    /**
     * 添加数据
     *
     * @param $data
     */
    public static function insertData($data)
    {
        $default = array(
            'uniacid' => 0,
            'unionid' => 0,
            'member_id' => 0,
            'type' => '',
            'created_at' => time()
        );

        $data = array_merge($default, $data);

        self::create($data);
    }

    /**
     * 更新登录类型
     *
     * @param $data
     */
    public static function updateData($data)
    {
        self::where('unique_id', $data['unique_id'])
            ->update(['type'=>$data['type']]);
    }
}