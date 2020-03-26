<?php
/**
 * Created by PhpStorm.
 * User: win 10
 * Date: 2019/7/16
 * Time: 14:53
 */

namespace app\frontend\modules\member\controllers;


use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\models\Member;
use app\frontend\controllers\HomePageController;
use app\frontend\modules\coupon\controllers\MemberCouponController;
use Yunshop\Diyform\api\DiyFormController;
use Yunshop\Designer\models\MemberDesigner;
use Yunshop\Designer\services\DesignerService;
use app\frontend\modules\member\models\MemberModel;

class MemberDesignerController extends ApiController
{
     public function index($request, $integrated = null)
     {//代码结构有机会一定要重新弄一下。。。
         $res = [];
         $res['status'] = false;
         $res['data'] = [];
         $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
         $goods_model = new $goods_model;

         if (app('plugins')->isEnabled('designer')) {
            $designer =  $this->getDesigner();
            if($designer->datas)
            {
                $datas = (new DesignerService())->getMemberData($designer->datas);

                $memberData = $this->getMemberData();
                //收银台属于插件第二个按钮，特殊处理
                $is_cashier = 0;
                $has_cashier = 1;
                if($memberData['merchants_arr']['cashier'])
                {
                    $is_cashier = 1;
                }

                $is_love_open = app('plugins')->isEnabled('love');
                foreach ($datas as $dkey=>$design)
                {
                    if($design['temp'] == 'membercenter')
                    {
                       if($design['params']['memberredlove'] == true || $design['params']['memberwhitelove'] == true){
                           if(!$is_love_open){
                               $datas[$dkey]['params']['memberredlove'] = false;
                               $datas[$dkey]['params']['memberwhitelove'] = false;
                           }
                       }
                    }
                    if($design['temp'] == 'membertool')
                    {
                         foreach ($design['data']['part'] as $pkey=>$par)
                         {
                             if(!in_array($par['name'],$memberData['tools']) || $par['is_open'] == false){
                                 unset($datas[$dkey]['data']['part'][$pkey]);
                             }
                         }
                        $datas[$dkey]['data']['part'] = array_values($datas[$dkey]['data']['part']);
                    }
                    if($design['temp'] == 'membermerchant')
                    {
                        foreach ($design['data']['part'] as $pkey=>$par)
                        {
                            if(in_array($par['name'],['store-cashier','hotel','supplier','micro']))
                            {
                                $datas[$dkey]['data']['part'][$pkey]['title'] = $memberData['merchants_arr'][$par['name']]['title'];
                                $datas[$dkey]['data']['part'][$pkey]['url'] = $memberData['merchants_arr'][$par['name']]['url'];
                            }
                            if($par['name'] == 'cashier')
                            {
                                $has_cashier = 0;
                            }
                            if(!in_array($par['name'],$memberData['merchants']) || $par['is_open'] == false){
                                unset($datas[$dkey]['data']['part'][$pkey]);
                            }
                        }
                        $datas[$dkey]['data']['part'] = array_values($datas[$dkey]['data']['part']);
                        if($is_cashier == 1 && $has_cashier == 1)
                        {
                            $datas[$dkey]['data']['part'][] = $memberData['merchants_arr']['cashier'];
                        }
                    }
                    if($design['temp'] == 'membermarket')
                    {
                        foreach ($design['data']['part'] as $pkey=>$par)
                        {
                            if(!in_array($par['name'],$memberData['markets']) || $par['is_open'] == false){
                                unset($datas[$dkey]['data']['part'][$pkey]);
                            }
                        }
                        $datas[$dkey]['data']['part'] = array_values($datas[$dkey]['data']['part']);
                    }
                    if($design['temp'] == 'memberasset')
                    {
                        foreach ($design['data']['part'] as $pkey=>$par)
                        {
                            if(!in_array($par['name'],$memberData['assets']) || $par['is_open'] == false){
                                unset($datas[$dkey]['data']['part'][$pkey]);
                            }
                        }
                        $datas[$dkey]['data']['part'] = array_values($datas[$dkey]['data']['part']);
                    }
                    if($design['temp'] == 'membercarorder')
                    {
                        if (!app('plugins')->isEnabled('net-car')) {
                            unset($datas[$dkey]);
                        }
                    }
                    if($design['temp'] == 'memberhotelorder')
                    {
                        if (!app('plugins')->isEnabled('hotel')) {
                            unset($datas[$dkey]);
                        }
                    }
                    if($design['temp'] == 'memberleaseorder')
                    {
                        if (!app('plugins')->isEnabled('lease-toy')) {
                            unset($datas[$dkey]);
                        }
                    }
                    if($design['temp'] == 'membergoruporder')
                    {
                        if (!app('plugins')->isEnabled('fight-groups')) {
                            unset($datas[$dkey]);
                        }
                    }
                    if($design['temp'] == 'diyform')
                    {
                        if (!app('plugins')->isEnabled('diyform')) {
                            unset($datas[$dkey]);
                        }else{
                            $getInfo = (new DiyFormController())->getDiyFormTypeMemberData('', true,$design['data']['form_id']);
                            $datas[$dkey]['get_info'] = $getInfo['status'] == 1?$getInfo['json']:[];
                        }
                    }
                    if($design['temp'] == 'coupon')
                    {
                        $getInfo = (new MemberCouponController())->couponsForDesigner('', true);
                        $datas[$dkey]['get_info'] = $getInfo['status'] == 1?$getInfo['json']:[];
                    }
                    //以下从店铺装修移植过来的，不一定全
                    if ($design['temp'] == 'sign'){
                        $shop = Setting::get('shop.shop')['credit1'] ? :'积分';
                        $datas[$dkey]['params']['award_content'] = str_replace( '积分',$shop,$design['params']['award_content']);
                    }

                    if ($design['temp']=='goods' || $design['temp']=='assemble' || $design['temp']=='flashsale'){
                         if($is_love_open){
                             foreach ($design['data'] as $gkey=>$goode_award){
                                 $HomePage = new HomePageController();
                                 $datas[$dkey]['data'][$gkey]['award'] = $HomePage->getLoveGoods($goode_award['goodid']);
                                 $datas[$dkey]['data'][$gkey]['stock'] = $HomePage->getMemberGoodsStock($goode_award['goodid']);
                             }
                         }else{
                             foreach ($design['data'] as $gkey=>$goode_award){
                                 $datas[$dkey]['data'][$gkey]['award'] = 0;
                             }
                         }
                        foreach ($design['data'] as $key => $goods){
                            $goods_data = $goods_model->find($goods['goodid']);
//                            $design['data'][$key]['vip_level_status']  = $goods_data->vip_level_status;
                            $datas[$dkey]['data'][$key]['vip_level_status'] = $goods_data->vip_level_status;
                        }
                    }
                }
                $datas = array_values($datas);
                $res['data'] = $datas;
                $res['status'] = true;
            }
         }
         if (is_null($integrated)) {
             return $this->successJson('成功', $res);
         } else {
             return show_json(1, $res);
         }
     }

    /**
     * 获取可用模板
     */
     private function getDesigner()
     {
         if(\Yunshop::request()->ingress == 'weChatApplet'){
             $pageType = 9;
         }else{
             $pageType = \Yunshop::request()->type;
         }
         $designer =  MemberDesigner::uniacid()
             ->whereRaw('FIND_IN_SET(?,page_type)', [$pageType])
             ->where(['shop_page_type'=>MemberDesigner::PAGE_MEMBER_CENTER,'is_default'=>1])
             ->first();
         return $designer;
     }

    /**
     * @return array
     * 获取可用插件按钮
     */
     private function getMemberData()
     {
         $memberId = \YunShop::app()->getMemberId();
         $arr = (new \app\common\services\member\MemberCenterService())->getMemberData($memberId);

         $tools = ['m-collection','m-footprint','m-address','m-info'];
         $merchants = [];
         //控制二维码显示，由member-data方法搬来
         $member_info = MemberModel::getUserInfos_v2(\YunShop::app()->getMemberId())->first();

         if (empty($member_info)) {
             $mid = Member::getMid();
             $this->jump = true;
             return $this->jumpUrl(\YunShop::request()->type, $mid);
         }

         $member_info = $member_info->toArray();
         $is_agent = $member_info['yz_member']['is_agent'] == 1 && $member_info['yz_member']['status'] == 2 ? true : false;
         if($is_agent){
             $markets = ['m-erweima'];
         }else{
             $markets = [];
         }
         $markets = array_merge($markets,['m-pinglun','m-guanxi','m-coupon']);
         $assets = [];
         $merchants_arr = [];
         foreach ($arr['tool'] as $v){
            $tools[] = $v['name'];
         }
         foreach ($arr['merchant'] as $v){
             $merchants[] = $v['name'];
             $merchants_arr[$v['name']] = $v;
         }
         foreach ($arr['market'] as $v){
             $markets[] = $v['name'];
         }
         foreach ($arr['asset_equity'] as $v){
             $assets[] = $v['name'];
         }

         return [
             'tools'=>$tools,
             'merchants'=>$merchants,
             'markets'=>$markets,
             'assets'=>$assets,
             'merchants_arr' => $merchants_arr
         ];
     }
}