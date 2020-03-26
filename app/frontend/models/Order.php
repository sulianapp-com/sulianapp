<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/6
 * Time: 下午4:35
 */
namespace app\frontend\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Order
 * @package app\frontend\models
 * @property Member belongsToMember
 * @property Carbon finish_time
 * @property int refund_id
 */
class Order extends \app\common\models\Order
{
    protected $appends = ['status_name', 'pay_type_name', 'button_models'];
    protected $hidden = [
        'uniacid',
        'create_time',
        'is_deleted',
        'is_member_deleted',
        'finish_time',
        'pay_time',
        'send_time',
        'send_time',
        'uid',
        'cancel_time',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public function scopeDetail($query){
        return $query->with(['hasManyOrderGoods'=>function($query){
            return $query->detail();
        }])->select(['id','uid','order_sn','price','goods_price','create_time','finish_time','pay_time','send_time','cancel_time','dispatch_type_id','pay_type_id','status','refund_id','dispatch_price','deduction_price']);
    }
    /**
     * 订单列表
     * @return $this
     */
    public function scopeOrders($query)
    {
        return $query->with(['hasManyOrderGoods'=>function($query){
            return $query->select(['order_id','goods_id','goods_price','total','price','thumb','title','goods_option_id','goods_option_title','comment_status']);
        }],'hasOnePayType')->orderBy('yz_order.id','desc');
    }
    public function belongsToMember()
    {
        return $this->belongsTo(app('OrderManager')->make('Member'), 'uid', 'uid');
    }

    public function belongsToOrderGoods()
    {
        return $this->belongsTo(app('OrderManager')->make('OrderGoods'), 'id', 'order_id');
    }

    public function orderGoodsBuilder($status)
    {
        $operator = [];
        if ($status == 0) {
            $operator['operator'] = '=';
            $operator['status'] = 0;
        } else {
            $operator['operator'] = '>';
            $operator['status'] = 0;
        }
        return function ($query) use ($operator) {
            return $query->with('hasOneComment')->where('comment_status', $operator['operator'], $operator['status']);
        };
    }

    public static function getMyCommentList($status)
    {
        $operator = [];
        if ($status == 0) {
            $operator['operator'] = '=';
            $operator['status'] = 0;
        } else {
            $operator['operator'] = '>';
            $operator['status'] = 0;
        }
        return self::whereHas('hasManyOrderGoods', function($query) use ($operator){
                return $query->where('comment_status', $operator['operator'], $operator['status']);
            })
            ->with([
                'hasManyOrderGoods' => self::orderGoodsBuilder($status)
            ])->where('status', 3)->orderBy('id', 'desc')->get();
    }
    public static function getMyCommentListPaginate($status,$page,$pageSize)
    {
        $operator = [];
        if ($status == 0) {
            $operator['operator'] = '=';
            $operator['status'] = 0;
        } else {
            $operator['operator'] = '>';
            $operator['status'] = 0;
        }
        return self::whereHas('hasManyOrderGoods', function($query) use ($operator){
            return $query->where('comment_status', $operator['operator'], $operator['status']);
        })
            ->with([
                'hasManyOrderGoods' => self::orderGoodsBuilder($status)
            ])->where('status', 3)->orderBy('id', 'desc')->paginate($pageSize,['*'],'page',$page);
    }

    /**
     * 关系链 指定商品
     *
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getOrderListByUid($uid)
    {
        return self::select(['*'])
            ->where('status','>=',1)
            ->where('status','<=',3)
            ->with(['hasManyOrderGoods'=>function($query){
                return $query->select(['*']);
            }])
            ->get();
    }


    public static function boot()
    {
        parent::boot();

        self::addGlobalScope(function(Builder $query){
            return $query->uid();
        });
    }

    /**
     * @return array
     * @throws \app\common\exceptions\AppException
     */
    public function getOperationsSetting()
    {
        if (Member::current()->uid == $this->uid) {
            return app('OrderManager')->setting('member_order_operations')[$this->statusCode] ?: [];
        }
        return [];
    }
}