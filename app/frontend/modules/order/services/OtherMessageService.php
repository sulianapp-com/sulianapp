<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/9/9 下午1:57
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\order\services;


use app\common\models\notice\MessageTemp;
use app\common\services\MessageService as Notice;
use app\frontend\models\Member;

class OtherMessageService
{
    private $orderModel;

    private $memberModel;

    protected $formId;

    protected $noticeType;


    function __construct($orderModel,$formId = '',$type = 1)
    {
        $this->orderModel = $orderModel;
        $this->memberModel = $this->getMemberModel();
        $this->formId = $formId;
        $this->noticeType = $type;
    }

    public function created()
    {
        $params = [
            ['name' => '下级昵称', 'value' => $this->getMemberName()],
            ['name' => '订单状态', 'value' => '下单（未付款）'],
            ['name' => '订单号', 'value' => $this->orderModel->order_sn],
            ['name' => '订单金额', 'value' => $this->orderModel->price],
        ];

        $this->notice($params);
    }

    public function paid()
    {

        $params = [
            ['name' => '下级昵称', 'value' => $this->getMemberName()],
            ['name' => '订单状态', 'value' => '已支付'],
            ['name' => '订单号', 'value' => $this->orderModel->order_sn],
            ['name' => '订单金额', 'value' => $this->orderModel->price],
        ];

        $this->notice($params);
    }

    public function sent()
    {
        $params = [
            ['name' => '下级昵称', 'value' => $this->getMemberName()],
            ['name' => '订单状态', 'value' => '已发货'],
            ['name' => '订单号', 'value' => $this->orderModel->order_sn],
            ['name' => '订单金额', 'value' => $this->orderModel->price],
        ];

        $this->notice($params);
    }

    public function received()
    {
        $params = [
            ['name' => '下级昵称', 'value' => $this->getMemberName()],
            ['name' => '订单状态', 'value' => '已完成'],
            ['name' => '订单号', 'value' => $this->orderModel->order_sn],
            ['name' => '订单金额', 'value' => $this->orderModel->price],
        ];

        $this->notice($params);
    }

    private function notice($params)
    {
        if (!\Setting::get('shop.notice.other_toggle')) {
            return;
        }
        \Log::info('二级消息通知,设置通过');


        $template_id = \Setting::get('shop.notice.other_toggle_temp');
        if (!$template_id) {
            return;
        }
        \Log::info('二级消息通知,模版ID通过');


        //\Log::info('二级消息通知,模版ID通过'.$this->memberModel->yzMember->parent_id, print_r($this->memberModel->toArray(),true));
        if (isset($this->memberModel->yzMember) && $this->memberModel->yzMember->parent_id) {
            \Log::info('二级消息通知,一级消息通过');

            $params_first = $params;
            $params_first[] = ['name' => '下级层级', 'value' => '一级'];

            $msg = MessageTemp::getSendMsg($template_id, $params_first);
            if (!$msg) {
                return;
            }
            $miniApp['type'] = \Setting::get('shop.notice.seller_order_pay.type');
            $miniApp['formId'] =  \Setting::get('shop.notice.seller_order_pay.formId');
            $news_link = MessageTemp::find($template_id)->news_link;
            $news_link = $news_link ?:'';
            Notice::notice(MessageTemp::$template_id,$msg,$this->memberModel->yzMember->parent_id,'',$news_link,$miniApp);
        }

        $twoSuperior = $this->getMemberModel($this->memberModel->yzMember->parent_id);
        if (isset($twoSuperior->yzMember) && $twoSuperior->yzMember->parent_id) {
            \Log::info('二级消息通知,二级消息通过');

            $params_second = $params;
            $params_second[] = ['name' => '下级层级', 'value' => '二级'];

            $msg = MessageTemp::getSendMsg($template_id, $params_second);
            if (!$msg) {
                return;
            }

            Notice::notice(MessageTemp::$template_id,$msg,$twoSuperior->yzMember->parent_id);
        }
        return;
    }

    private function getMemberName()
    {
        return $this->memberModel->realname ? $this->memberModel->realname : $this->memberModel->nickname;
    }

    private function getMemberModel($memberId = '')
    {
        $memberId = $memberId ? $memberId : $this->orderModel->uid;
        return Member::select('uid','realname','nickname')->with(['yzMember'=>function($query) {
            return $query->select('member_id','parent_id','relation');
        }])->where('uid',$memberId)->first();
    }
}
