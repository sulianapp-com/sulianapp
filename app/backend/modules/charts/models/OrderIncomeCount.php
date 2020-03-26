<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/31
 * Time: 14:38
 */

namespace app\backend\modules\charts\models;


use app\common\models\BaseModel;
use app\common\models\OrderAddress;

class OrderIncomeCount extends BaseModel
{
    public $table = 'yz_order_income_count';
    protected $guarded = [''];

    public function scopeSearch($query, $search)
    {
        $query->uniacid();
        if ($search['order_sn']) {
            $query->where('order_sn', $search['order_sn']);
        }

        if ($search['shop_name']) {
            $query->where('shop_name','like','%'.$search['shop_name'].'%');
        }

        if ($search['member']) {
            $query->whereHas('hasOneMember', function ($q) use ($search) {
                $q->searchLike($search['member']);
            });
        }
        if ($search['recommend']) {
            $query->whereHas('hasOneRecommend', function ($q) use ($search) {
                $q->searchLike($search['recommend']);
            });
        }

        if ($search['member_id']) {
            $query->where('uid', $search['member_id']);
        }

        if ($search['status'] != '') {
            $query->where('status', $search['status']);
        }

        if ($search['is_time']) {
            if ($search['time']) {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $query->whereBetween('created_at', $range);
            }
        }

        if ($search['order_sn']) {
            $query->where('order_sn', $search['order_sn']);
        }

        if ($search['province_id']) {
            $query->whereHas('hasOneOrderAddress',function ($q) use ($search) {
                if ($search['street_id']) {
                    $q->where('street_id', $search['street_id']);
                } elseif ($search['city_id']) {
                    $q->where('street_id', $search['street_id']);
                } elseif ($search['district_id']) {
                    $q->where('street_id', $search['street_id']);
                } else {
                    $q->where('province_id', $search['province_id']);
                }
            });
        }

        return $query;

    }

    public static function updateByOrderId($order_id, $data)
    {
        return self::uniacid()->where('order_id', $order_id)->update($data);
    }

    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'uid');
    }

    public function hasOneRecommend()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'parent_id');
    }

    public function hasOneOrderAddress()
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'order_id');
    }
}