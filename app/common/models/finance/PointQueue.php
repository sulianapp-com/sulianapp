<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/3/31
 * Time: 9:04 PM
 */

namespace app\common\models\finance;


use app\common\models\BaseModel;
use app\common\models\Order;
use app\common\services\finance\PointService;
use Illuminate\Database\Eloquent\Builder;
use app\common\models\Member;

class PointQueue extends BaseModel
{
    public $table = 'yz_point_queue';
    public $timestamps = true;
    protected $guarded = [''];
    protected $appends = [
        'status_name'
    ];

    const STATUS_FINISH = 1;
    const STATUS_RUNING = 0;

    public static function getList($search)
    {
        return self::select()
            ->with([
                'member' => function ($member) {
                    $member->select(['uid', 'nickname', 'realname', 'avatar', 'mobile']);
                },
                'order' => function ($order) {
                    $order->select(['id', 'order_sn']);
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
        if ($search['queue_id']) {
            $query->where('id', $search['queue_id']);
        }
        return $query;
    }

    public function getStatusNameAttribute()
    {
        $statusName = '奖励中';
        if ($this->status == self::STATUS_FINISH) {
            $statusName = '已完成';
        }
        return $statusName;
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'uid', 'uid');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public static function handle($orderModel, $goodsSale, $pointTotal)
    {
        if ($pointTotal <= 0) {
            return;
        }
        $data = [
            'uniacid' => $orderModel->uniacid,
            'uid' => $orderModel->uid,
            'order_id' => $orderModel->id,
            'goods_id' => $goodsSale->goods_id,
            'point_total' => $pointTotal,
            'finish_point' => 0,
            'surplus_point' => $pointTotal,
            'once_unit' => $goodsSale->max_once_point,
            'last_point' => 0,
            'status' => self::STATUS_RUNING
        ];
        self::store($data);
    }

    public static function store($data)
    {
        $model = new self();
        $model->fill($data);
        $model->save();
        // 消息通知 暂无
    }

    public static function returnRun($queueModel)
    {
        // 单期奖励积分数量
        $amount = $queueModel->once_unit;
        if ($queueModel->surplus_point - $amount < 0) {
            $amount = $queueModel->surplus_point;
            $queueModel->status = PointQueue::STATUS_FINISH;
        }
        $queueModel->last_point = $amount;
        $queueModel->surplus_point -= $amount;
        $queueModel->finish_point += $amount;
        // 修改队列
        $queueModel->save();

        // 奖励记录
        $log = [
            'uniacid' => $queueModel->uniacid,
            'uid' => $queueModel->uid,
            'queue_id' => $queueModel->id,
            'amount' => $amount,
            'point_total' => $queueModel->point_total,
            'finish_point' => $queueModel->finish_point,
            'surplus_point' => $queueModel->surplus_point
        ];
        PointQueueLog::store($log);
        // 奖励通知 暂无
        // 发放奖励到会员
        $remark = "queue_id[{$queueModel->id}]";
        (new PointService([
            'point_income_type' => PointService::POINT_INCOME_GET,
            'point_mode' => PointService::POINT_MODE_GOODS,
            'member_id' => $queueModel->uid,
            'point' => $amount,
            'remark' => $remark
        ]))->changePoint();
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(function (Builder $builder) {
            $builder->uniacid();
        });
    }
}