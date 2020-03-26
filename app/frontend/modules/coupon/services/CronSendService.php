<?php
/**
 * Created by PhpStorm.
 * User: CGOD
 * Date: 2019/12/18
 * Time: 17:33
 */

namespace app\frontend\modules\coupon\services;


use app\common\models\Coupon;
use app\common\models\MemberCoupon;


class CronSendService
{
    public $record;

    public $coupon;

    public $type;

    public $sendNum;

    public $numReason;
    
    public function __construct($record,$numReason,$type)
    {
        $this->record = $record;
        $this->numReason = $numReason;
        $this->type = $type;//1:订单完成 2:每月发放
    }

    public function sendCoupon()
    {
        $coupon = Coupon::uniacid()->where('id',$this->record->coupon_id)->first();
        if($coupon)
        {
            $this->coupon = $coupon;
        }else{
            $this->numReason = $this->numReason.'优惠券不存在';
            return;
        }
        $res = $this->judgeCoupon();
        if($res && $this->sendNum>0)
        {
            for ($i = 1; $i <= $this->sendNum; $i++) {
                (new CouponSendService())->sendCouponToMember($this->record->hasOneOrderGoods->uid, $this->record->coupon_id, 4, $this->record->hasOneOrderGoods->hasOneOrder->order_sn);
            }
       }
       if($this->type == 1)
       {
           $this->endOrderSend();
       }elseif ($this->type == 2){
            $this->endMonthSend();
       }
    }

    private function judgeCoupon()//判断能发多少张
    {
        $this->sendNum = $num = $this->record->coupon_several;
        if($this->coupon->total != -1)
        {
            $all = MemberCoupon::uniacid()->where("coupon_id", $this->record->coupon_id)->count();//优惠券发放总数
            $afterAll = bcadd($all,$num);
            if($afterAll > $this->coupon->total)
            {
                if($all >= $this->coupon->total)
                {
                    $this->numReason = $this->numReason.'优惠券已达发放总数';
                    return false;
                } else{
                    $num = bcsub($afterAll,$this->coupon->total);
                    $this->numReason = $this->numReason.'优惠券发放后达总数，发'.$num.'张';
                }
            }
        }
//        if($this->coupon->get_type == 1){
//            if($this->coupon->get_max != -1)
//            {
//                $person = MemberCoupon::uniacid()
//                    ->where(["coupon_id"=>$this->record->coupon_id,"uid"=>$this->record->hasOneOrderGoods->uid])
//                    ->count();//会员已有数量
//                $afterPerson = bcadd($person,$num);
//                if($afterPerson > $this->coupon->get_max)
//                {
//                    if($person >= $this->coupon->get_max)
//                    {
//                        $this->numReason = $this->numReason.'优惠券已达个人领取总数';
//                        return false;
//                    } else{
//                        $num = bcsub($afterPerson,$this->coupon->get_max);
//                        $this->numReason = $this->numReason.'优惠券发放后达个人领取总数，发'.$num.'张';
//                    }
//                }
//            }
//        }
        $this->sendNum = $num;
        return true;
    }

    private function endOrderSend()
    {
        $model = $this->record;
        $model->status = 1;
        $model->num_reason = $this->numReason;
        $model->save();
    }

    private function endMonthSend()
    {
        $model = $this->record;
        $model->end_send_num += 1;
        $model->num_reason = $this->numReason;
        if($model->end_send_num >= $model->send_num)
        {
            $model->status = 1;
        }
        $model->save();
    }
}