<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 18:46
 */

namespace app\backend\modules\charts\models;


use app\common\models\BaseModel;

class OrderStatistics extends BaseModel
{
    protected $table = 'yz_order_statistics';
    protected $guarded = [''];
    protected $fillable = [];

    public $timestamps = true;

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