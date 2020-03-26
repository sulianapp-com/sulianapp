<?php

use app\common\models\UniAccount;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveThirdLevel extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('yz_setting')) {
            $uniAccount = UniAccount::getEnable();
            foreach ($uniAccount as $u) {
                \YunShop::app()->uniacid = $u->uniacid;
                \Setting::$uniqueAccountId = $u->uniacid;
                $this->updateCommission();
                $this->updateMicro();
                $this->updateClock();
                $this->updateCar();
                $this->updateHotel();
                $this->updateStore();
                $this->updateLove();
                $this->updateAsset();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }

    public function updateCommission()
    {
        if (app('plugins')->isEnabled('commission')) {
            $plugin = \Setting::get('plugin.commission');
            if (!$plugin) {
                return;
            }
            if ($plugin['goods_detail_level'] == 3) {
                $plugin['goods_detail_level'] = 2;
            }
            if ($plugin['level'] == 3) {
                $plugin['level'] = 2;
                unset($plugin['third_level']);
            }
            \Setting::set('plugin.commission', $plugin);
            $commissionNotice = \Setting::get('plugin.commission_notice');
            $temp_id = $commissionNotice['commission_upgrade'];
            if (!is_null( \app\common\models\notice\MessageTemp::find($temp_id))) {
                \app\common\models\notice\MessageTemp::where('id',$temp_id) ->delete();
            }
        }
    }

    public function updateMicro()
    {
        if (app('plugins')->isEnabled('micro')) {
            $plugin = \Setting::get('plugin.micro');
            if (!$plugin) {
                return;
            }
            if ($plugin['agent_bonus_level'] >= 2 ) {
                $plugin['agent_bonus_level'] = 1;
                unset($plugin['bonus_third_level']);
                unset($plugin['gold_second_level']);
            }
            unset($plugin['agent_gold_level']);
            unset($plugin['gold_first_level']);
            unset($plugin['gold_second_level']);
            unset($plugin['gold_third_level']);

            \Setting::set('plugin.micro', $plugin);
        }
    }

    public function updateClock()
    {
        if (app('plugins')->isEnabled('clock-in')) {
            $plugin = \Setting::get('plugin.clock_in');
            if (!$plugin) {
                return;
            }
            if ($plugin['level'] == 3) {
                $plugin['level'] = 2;
                unset($plugin['third_level']);
            }
            foreach ($plugin['commission_level'] as $key => $level) {
                unset($plugin['commission_level'][$key]['third_level']);
            }
            \Setting::set('plugin.clock_in', $plugin);
        }

    }

    public function updateCar()
    {
        if (app('plugins')->isEnabled('net-car')) {
            $plugin = \Setting::get('plugin.net_car');

            if (!$plugin) {
                return;
            }
            unset($plugin['user_referrer_rate']['three']);

            \Setting::set('plugin.net_car', $plugin);
        }
    }

    public function updateStore()
    {
        if (app('plugins')->isEnabled('store-cashier')) {
            $stores = \Yunshop\StoreCashier\common\models\StoreSetting::where('key','commission')->get();
            if ($stores->isEmpty()) {
                return;
            }
            foreach ($stores as $store) {
                $commission = $store->value;

                if ($commission['level'] == 3) {
                    $commission['level'] = 2;
                    unset($commission['third_level']);
                }
                foreach ($commission['rule'] as $key => $level) {
                    unset($commission['rule'][$key]['third_level_rate']);
                }
                $store->value = $commission;
                $store->save();
            }
        }
    }
    public function updateHotel()
    {
        if (app('plugins')->isEnabled('hotel')) {
            $hotels = \Yunshop\Hotel\common\models\HotelSetting::where('key','commission')->get();
            if ($hotels->isEmpty()) {
                return;
            }
            foreach ($hotels as $hotel) {
                $commission = $hotel->value;
                if ($commission['level'] == 3) {
                    $commission['level'] = 2;
                    unset($commission['third_level']);
                }
                foreach ($commission['rule'] as $key => $level) {
                    unset($commission['rule'][$key]['third_level_rate']);
                }
                $hotel->value = $commission;
                $hotel->save();
            }
        }
    }

    public function updateLove()
    {
        if (app('plugins')->isEnabled('love')) {
            $goods_love = \Yunshop\Love\Common\Models\GoodsLove::uniacid()->where('goods_id',96)->get();
            if (!$goods_love->isEmpty()) {
                foreach ($goods_love as $love) {
                    $love->third_award_proportion = 0;
                    $love->third_award_fixed = 0;
                    if ($love->commission) {
                        $commission = unserialize($love->commission);
                        foreach ($commission['rule'] as $key => $level) {
                            unset($commission['rule'][$key]['third_level_rate']);
                            unset($commission['rule'][$key]['third_level_fixed']);
                        }
                        $love->commission = serialize($commission);
                    }
                    $love->save();
                }
            }
            $plugin = array_pluck(\Setting::getAllByGroup('Love')->toArray(), 'value', 'key');
            if ($plugin['third_award_proportion']) {
                $plugin['third_award_proportion'] = 0;
            }
            if ($plugin['commission']) {
                $plugin['commission'] = unserialize($plugin['commission']);
                foreach ($plugin['commission']['rule'] as $key => $commission) {
                    unset($plugin['commission']['rule'][$key]['third_level_rate']);
                }
                $plugin['commission'] = serialize($plugin['commission']);
            }
            foreach ($plugin as $key => $item) {
                \Setting::set('love.' . $key, $item);
            }
        }
    }

    public function updateAsset()
    {
        if (app('plugins')->isEnabled('asset')) {
            $assets = \Yunshop\Asset\Common\Models\AssetDigitizationModel::select(['goods_id','sell_goods'])->uniacid()->get();
            if (!$assets->isEmpty()) {
                return;
            }
            $assets = $assets->toArray();
            $goods_id = array_column($assets,'goods_id');
            $sell_goods = array_column($assets, 'sell_goods');
            $love_goods = array_merge($goods_id, $sell_goods);

            if (app('plugins')->isEnabled('team-dividend')) {
                \Yunshop\TeamDividend\models\GoodsTeamDividend::whereIn('goods_id',$goods_id)->delete();
            }
            if (app('plugins')->isEnabled('commission')) {
                \Yunshop\Commission\models\Commission::whereIn('goods_id',$goods_id)->delete();
            }
            if (app('plugins')->isEnabled('area-dividend')) {
                \Yunshop\AreaDividend\models\AreaDividendGoods::whereIn('goods_id',$goods_id)->delete();
            }
            if (app('plugins')->isEnabled('love')) {
                \Yunshop\Love\Common\Models\GoodsLove::whereIn('goods_id',$love_goods)->delete();
            }

        }
    }

}
