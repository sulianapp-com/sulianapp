<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 11:07
 */

namespace app\frontend\modules\coupon\controllers;

use app\common\components\ApiController;
use app\common\events\order\AfterOrderPaidImmediatelyEvent;
use app\common\models\coupon\ShoppingShareCouponLog;
use app\common\models\Order;
use app\common\exceptions\AppException;
use app\common\models\Coupon;
use app\frontend\models\Member;
use app\frontend\modules\coupon\models\ShoppingShareCoupon;
use app\frontend\modules\coupon\services\ShareCouponService;
use Carbon\Carbon;
use Yunshop\Hotel\common\models\CouponHotel;

class ShareCouponController extends ApiController
{
    protected $set;

    protected $share_model;

    protected $member;


    public function preAction()
    {
        parent::preAction();

        $this->getData();
    }


    //分享页面
    public function share()
    {

        //event(new AfterOrderPaidImmediatelyEvent(Order::find(801)));
        //dd(1);
        //拥有推广资格的会员才可以分享
        if ($this->set['share_limit'] == 1) {
            if (!$this->member->yzMember->is_agent) {
                throw new AppException('拥有推广资格的会员才可以分享');
            }
            $share_limit = $this->member->yzMember->is_agent ? 1 : 0;
        } else {
            $share_limit = 0;
        }


        $this->share_model->map(function ($model) {
            $model->coupon_num = count($model->share_coupon);
        });


        $data = [
            'set' => $this->set,
            'share_limit' => $share_limit,
            'coupon_num' => $this->share_model->sum('coupon_num'),
        ];


        return $this->successJson('share', $data);

    }

    //领取页面
    public function receive()
    {

        foreach ($this->share_model as $model) {

            $result = ShareCouponService::fen($model);

            if ($result['state'] == 'YES' || $result['state'] == 'ER') {
                break;
            }

        }

        if ($result['state'] == 'ER') {
            throw new AppException($result['msg']);
        }

        $data = [
            'set' =>  $this->set,
            'member_name' => $this->member->nickname?:$this->member->realname,
            'code' => $result['state'],
            'msg'  => $result['msg'],
            'coupon' =>  $this->handleCoupon($result['data']),
        ];

        return $this->successJson('share', $data);
    }


    public function logList()
    {
        $order_ids = explode('_', rtrim(\YunShop::request()->order_ids, '_'));

        $log_model = ShoppingShareCouponLog::yiLog($order_ids)->paginate(15)->toArray();


        $this->share_model->map(function ($model) {
            $model->coupon_num = count($model->share_coupon);
        });

        $coupon_num = $this->share_model->sum('coupon_num');

        $returnData = [
            'remainder' => $coupon_num,
            'total' => $log_model['total'],
            'current_page' => $log_model['current_page'],
            'last_page'   => $log_model['last_page'],
            'per_page' => $log_model['per_page'],
            'data' => $log_model['data'],
        ];

        return $this->successJson('成功', $returnData);
    }




    protected function handleCoupon($data)
    {
        if (empty($data)) return [];

        $data['api_limit'] = $this->handleCouponUseType($data);
        $now = strtotime('now');
        if ($data['time_limit'] == Coupon::COUPON_SINCE_RECEIVE) { //时间限制类型是"领取后几天有效"
            $end = $now + ($data['time_days'] * 24 * 3600);
            if ($data['time_days'] == 0) {
                $data['time_start'] = '不限时间使用'; //前端需要起止时间
                $data['time_end'] = ''; //前端需要起止时间
            } else {
                $data['time_start'] = date('Y-m-d', $now); //前端需要起止时间
                $data['time_end'] = date('Y-m-d', $end); //前端需要起止时间
            }
        } elseif ($data['time_limit'] == Coupon::COUPON_DATE_TIME_RANGE) { //时间限制类型是"时间范围"
            $data['time_start'] = substr($data['time_start'], 0, 10); //为了和前面保持一致
            $data['time_end'] = substr($data['time_end'], 0, 10); //为了和前面保持一致
        } else {
            $data['time_start'] = '优惠卷已经失效了'; //为了和前面保持一致
            $data['time_end'] = '';
        }

        return $data;
    }

    protected function handleCouponUseType($couponInArrayFormat)
    {
        switch ($couponInArrayFormat['use_type']) {
            case Coupon::COUPON_SHOP_USE:
                return ('商城通用');
                break;
            case Coupon::COUPON_CATEGORY_USE:
                $res = '适用于下列分类: ';
                $res .= implode(',', $couponInArrayFormat['categorynames']);
                return $res;
                break;
            case Coupon::COUPON_GOODS_USE:
                $res = '适用于下列商品: ';
                $res .= implode(',', $couponInArrayFormat['goods_names']);
                return $res;
                break;
            case Coupon::COUPON_SUPPLIER_USE:
                $res = '适用于下列供应商: ';
                $res .= implode(',', $couponInArrayFormat['suppliernames']);
                return $res;
                break;
            case Coupon::COUPON_STORE_USE:
            case 5:
                $res = '适用于下列门店: ';
                $res .= implode(',', $couponInArrayFormat['storenames']);
                return $res;
                break;
            case Coupon::COUPON_ONE_HOTEL_USE:
                $res = '适用于酒店:';
                if(app('plugins')->isEnabled('hotel')){
                    $coupon_hotel = CouponHotel::where('coupon_id',$couponInArrayFormat['id'])->with('hotel',function ($query){
                        $query->select('hotel_name');
                    })->first();
                    $res .= $coupon_hotel->hotel->hotel_name;
                }
                return $res;
                break;
            case Coupon::COUPON_MORE_HOTEL_USE:
                $res = '适用于下列酒店: ';
                if(app('plugins')->isEnabled('hotel')){
                    $hotel_arr = [];
                    $coupon_hotels = CouponHotel::where('coupon_id',$couponInArrayFormat['id'])->with('hotel',function ($query){
                        $query->select('hotel_name');
                    })->get();
                    foreach ($coupon_hotels as $v){
                        $hotel_arr[] = $v->hotel->hotel_name;
                    }
                    $res .= implode(',', $hotel_arr);
                }
                return $res;
                break;
            default:
                return ('Enjoy shopping');
        }
    }


    protected function getData()
    {

        $order_ids = explode('_', rtrim(\YunShop::request()->order_ids, '_'));

        $share_model = ShoppingShareCoupon::whereIn('order_id', $order_ids)->get();


        if ($share_model->isEmpty()) {
            throw new AppException('无分享优惠卷');
        }

        $set = \Setting::get('coupon.shopping_share');
        array_set($set, 'banner', yz_tomedia($set['banner']));


        $this->member = Member::with(['yzMember'])->find(\YunShop::app()->getMemberId());

        $this->set = $set;

        $this->share_model = $share_model;
    }
}