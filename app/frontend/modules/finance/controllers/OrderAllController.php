<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/1/14
 * Time: 17:06
 */

namespace app\frontend\modules\finance\controllers;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\MemberShopInfo;
use app\common\models\Order;

class OrderAllController extends ApiController
{
    public function index(){
        $data['today'] = $this->getMoney('today');
        $data['month'] = $this->getMoney('month');
        $data['all'] = $this->getMoney('all');
        $data['recent']['One'] = $this->getRecent(1);
        $data['recent']['Two'] = $this->getRecent(2);
        $data['recent']['Thr'] = $this->getRecent(3);
        $data['recent']['Fou'] = $this->getRecent(4);
        $data['recent']['Fiv'] = $this->getRecent(5);
        $data['recent']['Six'] = $this->getRecent(6);
        $data['recent']['Sev'] = $this->getRecent(7);
        return $this->successJson('ok',$data);
    }
    
    public static function isShow(){
        $set = Setting::get('shop.shop');
        $member_id = \YunShop::app()->getMemberId();
        $status = 1;
        if($set['achievement'] != 1){
            $status = 0;
        }
        if(!in_array(-1,$set['member_level'])){
            $member_level = MemberShopInfo::where('member_id',$member_id)->first();
            if(!in_array($member_level['level_id'],$set['member_level'])){
                $status = 0;
            }
        }
        return $status;
    }

    private function getMoney($mark){
        $range = [];
        $time=time();
        switch ($mark) {
            case 'today':
                $str=date("Y-m-d",time())." 0:0:0";
                $range[]=strtotime($str);
                $str=date("Y-m-d",time())." 23:59:59";
                $range[]=strtotime($str);
                break;
            case 'month':
                $range[]=mktime(0,0,0,date('m'),1,date('Y'));
                $range[]=mktime(23,59,59,date('m'),date('t'),date('Y'));
                break;
        }
        if($mark == "all"){
            $price=Order::where('status',Order::COMPLETE)->sum('price');
        }else {
            $price = Order::where('status', Order::COMPLETE)->whereBetween('finish_time',$range)->sum('price');
        }
        return round($price/10000,2);
    }

    private function getRecent($mark){
        $range = [];
        $str=date("Y-m-d",strtotime("-".$mark." day"))." 0:0:0";
        $range[]=strtotime($str);
        $str=date("Y-m-d",strtotime("-".$mark." day"))." 23:59:59";
        $range[]=strtotime($str);
        $price = Order::where('status', Order::COMPLETE)->whereBetween('finish_time',$range)->sum('price');
        $date=date("m-d",strtotime("-".$mark." day"));
        $data=['price'=>round($price/10000,2) , 'date'=>$date];
        return $data;
    }
}