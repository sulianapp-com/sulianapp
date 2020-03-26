<?php


namespace app\frontend\modules\coupon\controllers;


use app\common\components\ApiController;
use app\frontend\modules\coupon\models\Coupon;
use app\frontend\modules\coupon\models\MemberCoupon;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\memberCart\MemberCartCollection;
use Carbon\Carbon;

class ExchangeCenterController extends ApiController
{
    /**
     * 兑换中心接口
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $pluginId = request()->input('platform_id', 0);
        $uid = \YunShop::app()->getMemberId();
        $coupons = MemberCoupon::getExchange($uid, $pluginId)->get()->toArray();
        $result = array();
        $goodsIds = array();

        foreach ($coupons as $key => $v) {
            $goodsIds[] = $v['belongs_to_coupon']['goods_ids'][0];
            if (strtotime($v['time_end']) < strtotime(date('Y-m-d')) && $v['time_end'] != '不限时间') {
                unset($coupons[$key]);
                continue;
            }
            //统计
            $result[$v['coupon_id']]['total'] += 1;
            $result[$v['coupon_id']]['coupon'] = $v;
        }
        $goodsIds = array_unique($goodsIds);
        if($pluginId == 32 && app('plugins')->isEnabled('store-cashier')){
            $storeInfo = \Yunshop\StoreCashier\common\models\StoreGoods::select('goods_id','store_id')
                ->whereIn('goods_id',$goodsIds)
                ->with(['store' => function($query){
                    $query->select('id','store_name');
                }])
                ->get()
                ->toArray();
            $storeInfo = array_column($storeInfo,null,'goods_id');
        }
        if($pluginId == 33 && app('plugins')->isEnabled('hotel')){
            $hotelInfo = \Yunshop\Hotel\common\models\HotelGoods::select('goods_id','hotel_id')
                ->whereIn('goods_id',$goodsIds)
                ->with(['hotel' => function($query){
                    $query->select('id','hotel_name');
                }])
                ->get()
                ->toArray();
            $hotelInfo = array_column($hotelInfo,null,'goods_id');
        }
        // dd($hotelInfo);
        $array = array();
        $i = 0;
        foreach ($result as $key => $value){
            $array['list'][$i] = array_merge($value,$value['coupon']);
            if($pluginId == 32 && app('plugins')->isEnabled('store-cashier')){
                $array['list'][$i]['store_id'] = $storeInfo[$value['coupon']['belongs_to_coupon']['goods_ids'][0]]['store_id'];
                $array['list'][$i]['store_name'] = $storeInfo[$value['coupon']['belongs_to_coupon']['goods_ids'][0]]['store']['store_name'];
            }
            if($pluginId == 33 && app('plugins')->isEnabled('hotel')){
                $array['list'][$i]['hotel_id'] = $hotelInfo[$value['coupon']['belongs_to_coupon']['goods_ids'][0]]['hotel_id'];
                $array['list'][$i]['hotel_name'] = $hotelInfo[$value['coupon']['belongs_to_coupon']['goods_ids'][0]]['hotel']['hotel_name'];
            }
            unset($array['list'][$i]['coupon']);
            $i++;
        }

        if(empty($array)){
            $array['list'] = [];
        }
        $array['navigation'][0] = [
            'id' => 0,
            'name' => '商城'
        ];

        if(app('plugins')->isEnabled('hotel')){
            $array['navigation'][2] = [
                'id' => 33,
                'name' => HOTEL_NAME,
            ];
        }
        if(app('plugins')->isEnabled('store-cashier')){
            $array['navigation'][1] = [
                'id' => 32,
                'name'=>'门店'
            ];
        }

        return $this->successJson('ok', $array);
    }


    /**
     * @return MemberCartCollection
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    protected function getMemberCarts()
    {
        $data = request()->input('data');
        $couponCount = array_column($data,'coupon_id');
        //获取可以兑换的优惠券Id
        $memberCoupon = MemberCoupon::getExchange(\YunShop::app()->getMemberId(),0)
            ->whereIn('coupon_id',$couponCount)
            ->get()
        ->toArray();
        foreach ($memberCoupon as $key => $v) {
            $goodsIds[] = $v['belongs_to_coupon']['goods_ids'][0];
            if (strtotime($v['time_end']) < strtotime(date('Y-m-d')) && $v['time_end'] != '不限时间') {
                unset($memberCoupon[$key]);
                continue;
            }
        }
        //终止条件
        $count = array_sum(array_column($data,'total'));
        //dd($count);
        $data = array_column($data,null,'coupon_id');
        $member_coupon_ids = [];
        foreach ($memberCoupon as $key => $value){
            //dd($data[$value['coupon_id']]);
            if($data[$value['coupon_id']]){
                   if(count($member_coupon_ids[$value['coupon_id']]) == $data[$value['coupon_id']]['total']) {
                       continue;
                   }
                $member_coupon_ids [$value['coupon_id']][] = $value['id'];
            }
        }

        $member_coupon_id = array();
        foreach ($member_coupon_ids as $value){
            foreach ($value as $v){
                $member_coupon_id[] = $v;
            }
        }
        $member_coupon_ids = implode(',',$member_coupon_id);
        if(request()->input('is_exchange') == 1){
            request()->offsetSet('member_coupon_ids', $member_coupon_ids);
        }

        $result = new MemberCartCollection();
        foreach ($data as $key => $value){
            unset($value['coupon_id']);
            $value['option_id'] = 0;
            $result->push(MemberCartService::newMemberCart($value));
        }
        return $result;
    }

    /**
     * 验证
     */
    private function validateParam()
    {
        $this->validate([
            'data' => 'required',
            'data.0.goods_id' => 'required | min:1 |integer',
            'data.0.total' => 'required | min:1 | integer',
            'data.0.coupon_id' => 'required | min:1 |integer',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    public function exchangeBuy()
    {
        $this->validateParam();
        $trade = $this->getMemberCarts()->getTrade();
        return $this->successJson('成功', $trade);
    }

}

