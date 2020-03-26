<?php

namespace app\backend\modules\coupon\models;


use app\common\observers\coupon\CouponObserver;

class Coupon extends \app\common\models\Coupon
{
    static protected $needLog = true;

    public $table = 'yz_coupon';

    public $widgets = [];

    //类型转换
    protected $casts = [
        'goods_ids' => 'json',
        'category_ids' => 'json',
        'storeids' => 'json',
        'goods_names' => 'json',
        'categorynames' => 'json',
        'storenames' => 'json',
    ];

    //默认值
    protected $attributes = [
        'goods_ids' => '[]',
        'category_ids' => '[]',
        'storeids' => '[]',
        'display_order' => 0,
        'plugin_id' => 0,
    ];

    /**
     *  定义字段名
     * 可使
     * @return array */
    public function atributeNames() {
        return [
            'display_order'=> '排序',
            'name'=> '优惠券名称',
            'enough'=> '使用条件(消费金额前提)',
            'time_days'=> '使用时间限制',
            'deduct'=> '立减',
            'discount'=> '折扣',
            'get_max'=> '每个人的限领数量',
            'total' => '发放总数',
            'resp_title' => '推送标题',
            'resp_desc' => '推送说明',
            'resp_url' => '推送链接',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public function rules() {

        //因为 deduct 和 discount 的默认值都为 0, 为了让 0 通过验证, 所以需要如下判断
        if($this->coupon_method == 1){
            if($this->enough){
                $deduct = '|between:1,'.$this->enough; //不能超过"订单金额"(如果 enough 为 0, 表示不限制消费金额, 则不限制"立减"金额)
            } else{
                $deduct = '|min:1';
            }
        }elseif($this->coupon_method == 2){
            $discount = '|between:0,10';
        }else{
            $deduct = null;
            $discount = null;
        }
        return [
            'display_order' => 'required|integer',
            'name' => 'required',
            'enough' => 'required|integer',
            'time_days' => 'required|integer',
            'deduct' => 'required|integer'.$deduct,
            'discount' => 'required|integer'.$discount,
            'get_max' => 'required|integer',
            'total' => 'required|integer',
            'resp_title' => 'nullable|string',
            'resp_desc' => 'nullable|string',
            'resp_url' => 'nullable|url',
        ];
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public static function getCouponsByName($keyword)
    {
        return static::uniacid()->select('id', 'name')
            ->where('name', 'like', '%' . $keyword . '%')
            ->get();
    }

    /**
     * @param $keyword
     * @return mixed
     */
    public static function getCouponsDataByName($keyword)
    {
        return static::uniacid()
            ->select(['id','display_order','name', 'enough', 'coupon_method', 'deduct', 'discount', 'get_type', 'created_at','status','money' , 'total'])
            ->where('name', 'like', '%' . $keyword . '%');
    }


    /**
     * @param $title 优惠券名称
     * @param $type 优惠券是否在领取中心显示
     * @param $timeSwitch 是否开启"创建时间"的搜索选项
     * @param $timeStart 起始时间
     * @param $timeEnd 结束时间
     * @return mixed
     */
    static public function getCouponsBySearch($title, $type=NULL, $timeSwitch=0, $timeStart=NULL, $timeEnd=NULL)
    {
        $CouponsModel = self::uniacid()
            ->select(['id','display_order','name', 'enough',
                    'coupon_method', 'deduct', 'discount', 'get_type', 'created_at','total']);

        if(!empty($title)){
            $CouponsModel = $CouponsModel->where('name', 'like', '%'.$title.'%');
        }
        if(!empty($type)){
            $CouponsModel = $CouponsModel->where('get_type', '=', $type);
        }
        if($timeSwitch == 1 && !empty($timeStart) && !empty($timeEnd)){
            $CouponsModel = $CouponsModel->whereBetween('created_at', [$timeStart, $timeEnd]);
        }
        return $CouponsModel;
    }

    //删除优惠券
    public static function deleteCouponById($couponId)
    {
        return static::find($couponId)->delete();
    }

}
