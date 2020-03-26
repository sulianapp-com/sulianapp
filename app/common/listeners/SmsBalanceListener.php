<?php


namespace app\common\listeners;

use app\common\models\UniAccount;
use app\backend\modules\member\models\Member;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Events\Dispatcher;


class SmsBalanceListener
{
    use DispatchesJobs;

    public function subscribe(Dispatcher $events)
    {

        $events->listen('cron.collectJobs', function () {
            \Log::debug('-------------IN_IA-----------',defined('IN_IA'));
            if (defined('IN_IA')) {
                \Log::debug('----定时任务执行----');
                $uniAccounts = UniAccount::getEnable();
                foreach ($uniAccounts as $uniAccount) {
                    \YunShop::app()->uniacid = $uniAccount->uniacid;
                    \Setting::$uniqueAccountId = $uniAccount->uniacid;
                    $unicid = \YunShop::app()->uniacid;
                    $balanceSet = \Setting::get('finance.balance');
                    if ($balanceSet['sms_send'] == 0) {
                        \Log::debug($uniAccount->uniacid . '未开启');
                        continue;
                    }

                    $smsSet = \Setting::get('shop.sms');
                    //sms_hour 时间
                    //sms_hour_amount 金额
                    if ($smsSet['type'] != 3 && $smsSet['aly_templateBalanceCode'] == null) {
                        \Log::debug('短信功能设置' . $smsSet);
                        continue;
                    }
                    $time = '0 ' . $balanceSet['sms_hour'] . ' * * * *';
                    \Log::debug('-----------time--------',$time);
                    \Cron::add('smsMeaggeToMemberMobile' . $uniAccount->uniacid, $time, function () use ($unicid) {
                        $this->handle($unicid);
                    });
                }
            }
        });
    }

    /**
     * 定时发送短信
     * @return bool
     */
    public function handle($uniacid)
    {
        \Log::debug('----------定时短信发送----------');

        \YunShop::app()->uniacid = $uniacid;
        \Setting::$uniqueAccountId = $uniacid;
        $balanceSet = \Setting::get('finance.balance');
        //sms_send 是否开启
        if ($balanceSet['sms_send'] == 0) {
            \Log::debug($uniacid . '未开启');
            return true;
        }
        $smsSet = \Setting::get('shop.sms');
        //sms_hour 时间
        //sms_hour_amount 金额
        if ($smsSet['type'] != 3 || empty($smsSet['aly_templateBalanceCode'])) {
            \Log::debug('短信功能设置' . $smsSet);
            return true;
        }
        //查询余额,获取余额超过该值的用户，并把没有手机号的筛选掉
        $mobile = Member::uniacid()
            ->select('uid', 'mobile', 'credit2')
            ->whereNotNull('mobile')
            ->where('credit2', '>', $balanceSet['sms_hour_amount'])
            ->get();
        if (empty($mobile)) {
            \Log::debug('未找到满足条件会员');
            return true;
        } else {
            $mobile = $mobile->toArray();
        }

        $u = UniAccount::where('uniacid',$uniacid)->first();
        foreach ($mobile as $key => $value) {
            if (!$value['mobile']) {
                continue;
            }
            //todo 发送短信
            $aly_sms = new \app\common\services\aliyun\AliyunSMS(trim($smsSet['aly_appkey']), trim($smsSet['aly_secret']));
            $response = $aly_sms->sendSms(
                $smsSet['aly_signname'], // 短信签名
                $smsSet['aly_templateBalanceCode'], // 发货提醒短信
                $value['mobile'], // 短信接收者
                Array(  // 短信模板中字段的值
                    'preshop' => $u->name,
                    'amount' => $value['credit2'],
                    'endshop' => $u->name,
                )
            );
            if ($response->Code == 'OK' && $response->Message == 'OK') {
                \Log::debug($value['mobile'] . '阿里云短信发送成功');
            } else {
                \Log::debug($value['mobile'] . '阿里云短信发送失败' . $response->Message);
            }
        }
        return true;
    }
}