<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 11:16
 */

namespace app\backend\modules\charts\modules\member\models;


use app\common\models\BaseModel;

class MemberLowerCount extends BaseModel
{
    public $table = 'yz_member_lower_count';
    public $timestamps = true;

    protected $fillable = [];
    protected  $guarded = [''];

    public function belongsToMember()
    {
        return $this->belongsTo(\app\common\models\Member::class, 'uid', 'uid');
    }

    public static function getMember($search)
    {
        $model = self::uniacid()->with('belongsToMember');

        if (!empty($search['member_id'])) {
            $model->whereHas('belongsToMember', function ($q) use($search) {
                $q->where('uid', $search['member_id']);
            });
        }

        if (!empty($search['member_info'])) {
            $model->whereHas('belongsToMember', function ($q) use($search) {
                $q->where('nickname', 'like' , '%' . $search['member_info'] . '%')
                    ->orWhere('realname', 'like' , '%' . $search['member_info'] . '%')
                    ->orWhere('mobile', 'like' , '%' . $search['member_info'] . '%');
            });
        }
        return $model;
    }
}