<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/3/31
 * Time: 9:04 PM
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use app\common\models\Member;

class PointQueueLog extends BaseModel
{
    public $table = 'yz_point_queue_log';
    public $timestamps = true;
    protected $guarded = [''];

    public static function getList($search)
    {
        return self::select()
            ->with([
                'member' => function ($member) {
                    $member->select(['uid', 'nickname', 'realname', 'avatar', 'mobile']);
                }
            ])
            ->search($search);
    }

    public function scopeSearch($query, $search)
    {
        if ($search['uid']) {
            $query->where('uid', $search['uid']);
        }
        if ($search['member']) {
            $query->whereHas('member', function ($member) use ($search) {
                $member->select('uid', 'nickname', 'realname', 'mobile', 'avatar')
                    ->where('realname', 'like', '%' . $search['member'] . '%')
                    ->orWhere('mobile', 'like', '%' . $search['member'] . '%')
                    ->orWhere('nickname', 'like', '%' . $search['member'] . '%');
            });
        }
        return $query;
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public static function store($data)
    {
        $model = new self();
        $model->fill($data);
        $model->save();
        // 消息通知 暂无
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}