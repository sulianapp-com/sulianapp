<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/4
 * Time: 15:28
 */

namespace app\backend\modules\charts\models;

use app\common\models\MemberShopInfo;
use app\common\models\order\OrderPluginBonus;
use Illuminate\Support\Facades\DB;
use Yunshop\StoreCashier\common\models\CashierOrder;
use Yunshop\StoreCashier\common\models\StoreOrder;
use Yunshop\Supplier\common\models\SupplierOrder;

class Order extends \app\common\models\Order

{
    //订单导出订单数据
    public static function getExportOrders($search)
    {
        $builder = Order::exportOrders($search);
        $orders = $builder->get()->toArray();
        return $orders;
    }

    public function scopeExportOrders($query, $search)
    {
        $order_builder = $query->search($search);

        $orders = $order_builder->with([
            'belongsToMember' => self::memberBuilder(),
            'hasManyOrderGoods' => self::orderGoodsBuilder(),
            'hasOneDispatchType',

            'address',
            'hasOneOrderRemark',
            'express',
            'hasOnePayType',
            'hasOneOrderPay'
        ]);
        return $orders;
    }

    public function belongsToRecommender()
    {
        return $this->belongsTo(MemberShopInfo::class, 'uid', 'member_id');
    }

    public function scopeOrders($order_builder, $search)
    {

        $order_builder->search($search);
        $order_builder->where('status','>',0);

        $orders = $order_builder->with([
            'belongsToMember' => function ($query) {
                $query->select(['uid', 'mobile', 'nickname', 'realname','avatar']);
            },
            'hasOneOrderGoods' => function ($query) {
                $query->select(DB::raw('sum(goods_cost_price) as total_cost_price'),'order_id')->groupBy('order_id');
//                $query->whereRaw('sum(goods_cost_price) as total_cost_price')->groupBy('order_id');
            },
            'hasOneDispatchType',
            'belongsToRecommender' => function ($query) {
            },
            'hasOnePayType',
            'address',
            'express',
            'hasOneRefundApply' => self::refundBuilder(),
            'hasOneOrderRemark',
//            'hasOneOrderPay'=> function (Builder $query) {
//                $query->orderPay();
//            },

        ]);
        return $orders;
    }



    private static function refundBuilder()
    {
        return function ($query) {
            return $query->with('returnExpress')->with('resendExpress');
        };
    }

    private static function memberBuilder()
    {
        return function ($query) {
            return $query->select(['uid', 'mobile', 'nickname', 'realname','avatar']);
        };
    }

    private static function orderGoodsBuilder()
    {
        return function ($query) {
            $query->orderGoods();
        };
    }

    public function scopeSearch($order_builder, $params)
    {
        if (array_get($params, 'ambiguous.field', '') && array_get($params, 'ambiguous.string', '')) {
            //订单.支付单号
            if ($params['ambiguous']['field'] == 'order') {
                call_user_func(function () use (&$order_builder, $params) {
                    list($field, $value) = explode(':', $params['ambiguous']['string']);
                    if (isset($value)) {
                        return $order_builder->where($field, $value);
                    } else {
                        return $order_builder->where(function ($query)use ($params){
                            $query->searchLike($params['ambiguous']['string']);

                            $query->orWhereHas('hasOneOrderPay', function ($query) use ($params) {
                                $query->where('pay_sn','like',"%{$params['ambiguous']['string']}%");
                            });
                        });

                    }
                });


            }


            //用户
            if ($params['ambiguous']['field'] == 'member') {
                call_user_func(function () use (&$order_builder, $params) {
                    list($field, $value) = explode(':', $params['ambiguous']['string']);
                    if (isset($value)) {
                        return $order_builder->where($field, $value);
                    } else {
                        return $order_builder->whereHas('belongsToMember', function ($query) use ($params) {
                            return $query->searchLike($params['ambiguous']['string']);
                        });
                    }
                });

            }
            //订单商品
            if ($params['ambiguous']['field'] == 'order_goods') {
                $order_builder->whereHas('hasManyOrderGoods', function ($query) use ($params) {
                    $query->searchLike($params['ambiguous']['string']);
                });
            }

            //快递单号
            if ($params['ambiguous']['field'] == 'dispatch') {
                $order_builder->whereHas('express', function ($query) use ($params) {
                    $query->searchLike($params['ambiguous']['string']);
                });
            }

        }
        //支付方式
        if (array_get($params, 'pay_type', '')) {
            $order_builder->where('pay_type_id', $params['pay_type']);
        }
        //操作时间范围

        if (array_get($params, 'time_range.field', '') && array_get($params, 'time_range.start', 0) && array_get($params, 'time_range.end', 0)) {
            $range = [strtotime($params['time_range']['start']), strtotime($params['time_range']['end'])];
            $order_builder->whereBetween($params['time_range']['field'], $range);
        }
        return $order_builder;
    }

    public static function getOrderDetailById($order_id)
    {
        return self::orders()->with(['deductions','coupons','discounts','orderPays'=> function ($query) {
            $query->with('payType');
        },'hasOnePayType'])->find($order_id);
    }

    public static function boot()
    {
        parent::boot();

//        static::addGlobalScope(function (Builder $builder) {
//            $builder->isPlugin();
//        });
    }



    public function hasManyOrderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'id');
    }

    public function hasManyCashierOrder()
    {
        return $this->hasMany(CashierOrder::class,'order_id','id');
    }

    public function hasManyStoreOrder()
    {
        return $this->hasMany(StoreOrder::class,'order_id','id');
    }

    public function hasManySupplierOrder()
    {
        return $this->hasMany(SupplierOrder::class,'order_id','id');
    }

    public function hasManyOrderPluginBonus()
    {
        return $this->hasMany(OrderPluginBonus::class, 'order_id', 'id');
    }
}