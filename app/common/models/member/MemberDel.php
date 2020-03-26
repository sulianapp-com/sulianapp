<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/27
 * Time: 14:26
 */

namespace app\common\models\member;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberDel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_member_del_log';

    protected $guarded = [];

    protected $casts = [
        'value' => 'json',
    ];

    public static function insertData($member)
    {
        if (!self::uniacid()->where('member_id', $member->uid)->first()) {
            $data = [
                'uniacid'   => $member->uniacid,
                'member_id' => $member->uid,
                'value'     => $member->toArray(),
            ];
            MemberDel::create($data);
        }

    }

    /**
     * 删除的会员还有缓存的 清空缓存
     * @param $member_id
     * @return bool
     */
    public static function delUpdate($member_id)
    {
        return self::where('member_id',$member_id)->update(['type' => 1]);
    }


    public function scopeByMemberId($query, $member_id)
    {
        return $query->where('member_id', $member_id);
    }
}