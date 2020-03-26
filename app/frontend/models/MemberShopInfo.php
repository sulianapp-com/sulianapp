<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/6/30
 * Time: 上午10:14
 */

namespace app\frontend\models;


class MemberShopInfo extends \app\common\models\MemberShopInfo
{
    /**
     * 关联会员等级表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(self::getNearestModel('MemberLevel'), 'level_id', 'id');
    }
    public function group()
    {
        return $this->belongsTo('app\backend\modules\member\models\MemberGroup');
    }
    public function agent()
    {
        return $this->belongsTo('app\backend\modules\member\models\Member', 'parent_id', 'uid');
    }
}