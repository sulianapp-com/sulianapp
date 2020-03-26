<?php

namespace app\frontend\modules\member\listeners;

use app\common\facades\Setting;
use app\common\models\UniAccount;
use app\frontend\models\MemberShopInfo;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\DB;


class MemberLevelValidity
{
    use DispatchesJobs;

    public $memberSet;
    public $setLog;

    public $uniacid;


    public function subscribe()
    {
        \Event::listen('cron.collectJobs', function () {
            \Cron::add('Member-validity', '*/10 * * * * *', function () {
                $this->handle();
                return;
            });
        });
    }

    public function handle()
    {
        \Log::info('会员等级到期');
        set_time_limit(0);
        $uniAccount = UniAccount::getEnable();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            Setting::$uniqueAccountId = $u->uniacid;
            $this->uniacid = $u->uniacid;

            $this->memberSet = Setting::get('shop.member');
            $this->setLog = Setting::get('plugin.member_log');
            if (!$this->memberSet['term']) {
                continue;
            }
            $this->setReduceLevelValidity();

            $this->setExpire();

        }
    }

    public function setReduceLevelValidity()
    {

        if ($this->setLog['current_d'] == date('d')) {
            return;
        }

        //设置当前执行日期
        $this->setLog['current_d'] = date('d');
        Setting::set('plugin.member_log', $this->setLog);

        MemberShopInfo::uniacid()
            ->where('validity', '>', '0')
            ->update(['validity' => DB::raw('`validity` - 1')]);
    }

    public function setExpire()
    {
        if ($this->memberSet['level_type'] != '2') {
            return;
        }
        MemberShopInfo::uniacid()
            ->where('level_id', '!=', '0')
            ->where('validity', 0)
            ->update(['level_id' => 0, 'downgrade_at' => time()]);
    }

}