<?php


namespace app\frontend\modules\order\services;


class SmsMessageService
{
    private $orderModel;

    function __construct($orderModel,$formId = '',$type = 1)
    {
        $this->orderModel = $orderModel;
    }

    public function sent()
    {
        \Log::debug('订单发货短信通知');
        $set = \Setting::get('shop.sms');
        if($set['type'] != 3 || empty($set['aly_templateSendMessageCode'])){
            \Log::debug('模板未设置');
            return false;
        }
        //查询手机号
        $mobile = \app\common\models\Member::find($this->orderModel->uid)->mobile;
        //todo 发送短信
        $aly_sms = new \app\common\services\aliyun\AliyunSMS(trim($set['aly_appkey']), trim($set['aly_secret']));
        $response = $aly_sms->sendSms(
            $set['aly_signname'], // 短信签名
            $set['aly_templateSendMessageCode'], // 发货提醒短信
            $mobile, // 短信接收者
            Array(  // 短信模板中字段的值
                "shop" => \Setting::get('shop.shop')['name'],
            )
        );
        if ($response->Code == 'OK' && $response->Message == 'OK') {
            \Log::debug('模板阿里云短信发送成功');
        } else {
            \Log::debug($response->Message);
        }
        return true;
    }

}