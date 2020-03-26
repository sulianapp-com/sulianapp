<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午10:39
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\memberCart\MemberCartCollection;
use app\common\models\Order;
use app\frontend\modules\order\services\MessageService;
use app\frontend\modules\order\services\OtherMessageService;
use app\common\models\MemberMiniAppModel;
use app\frontend\modules\order\services\MiniMessageService;
use app\common\models\FormId;

class MiniAppController extends ApiController
{
    public function index()
    {

        $order = Order::find(\Yunshop::request()->orderId);
        $formId = \Yunshop::request()->formID;
        \Log::debug('===========发送模板消息',$formId);
        (new MiniMessageService($order,$formId,2,'订单支付成功通知'))->received();
        return $this->successJson('成功');
    }
    public function formId(){
        $formId = \Yunshop::request()->formID;
        $ar = array(
            'formid' => $formId,
            'addtime' => time(),
        );
        FormId::insert($ar);
    }    
    // public function formId(){

    //     $memberId = \Yunshop::request()->memberId;
    //     $ingress = \Yunshop::request()->ingress;
    //     $type = \Yunshop::request()->type;
    //     if ($ingress != 'weChatApplet' && $type !=2){
    //         return ;
    //     }
    //     $formId = \Yunshop::request()->formID;
    //     $formIdTrem = MemberMiniAppModel::select()->where('member_id',$memberId)->first();
    //     $time = strtotime (date("Y-m-d H:i:s")); //当前时间
    //     $minute = floor(($time - $formIdTrem->formId_create_time) % 86400/60);
    //     if ($minute > 10080 ){
    //         MemberMiniAppModel::where('member_id',$memberId)
    //             ->uniacid()
    //             ->update([
    //                 'formId'=>$formId,
    //                 'formId_create_time' =>$time,
    //             ]);
    //     }else{
    //         if (!empty($formIdTrem->formId)){
    //             $formId = $formIdTrem->formId.'#'.$formId;
    //         }
    //         MemberMiniAppModel::where('member_id',$memberId)
    //             ->uniacid()
    //             ->update([
    //                 'formId'=> $formId,
    //                 'formId_create_time' =>$time,
    //             ]);
    //     }
    //     return $this->successJson('成功');
    // }
}