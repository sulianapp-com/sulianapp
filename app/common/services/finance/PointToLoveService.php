<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/22 下午2:10
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\common\services\finance;


use app\common\facades\Setting;
use app\common\models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yunshop\Love\Common\Services\LoveChangeService;

class PointToLoveService
{
    public function handleTransferQueue($uniacid)
    {
        \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $uniacid;

        $result = $this->transferStart();
        if ($result !== true ) {
            \Log::info('--积分自动转入爱心值Uniacid:'.$uniacid.'自动转入失败--');
        }
        //\Setting::set('love.last_month_activation',date('m'));
        //\Setting::set('love.last_week_activation',date('W'));
        \Log::info('--积分自动转入爱心值Uniacid:'.$uniacid.'自动转入完成--');
    }


    private function transferStart()
    {
        $members = Member::uniacid()->where('credit1','>',0)->with('pointLove')->get();


        DB::beginTransaction();
        foreach ($members as $key => $member) {

            $rate = $this->getRate($member);
            if ($rate < 0) {
                continue;
            }

            $change_value = bcdiv(bcmul($member->credit1,$rate,4),100,2);
            if ($change_value <= 0) {
                continue;
            }

            $point_change_data = [
                'point_income_type' => PointService::POINT_INCOME_LOSE,
                'point_mode'        => PointService::POINT_MODE_TRANSFER_LOVE,
                'member_id'         => $member->uid,
                'point'             => -$change_value,
                'remark'            => '积分自动转入：'.$change_value. '转入比例：' . $rate,
            ];
            //修改用户积分
            $result = (new PointService($point_change_data))->changePoint();

            if (!$result) {
                Log::info('积分自动转入爱心值失败',print_r($point_change_data,true));
                DB::rollBack();
                return false;
            }


            $change_value = $this->getExchange($member,$change_value);

            $love_change_data = [
                'member_id'         => $member->uid,
                'change_value'      => $change_value,
                'operator'          => 0,
                'operator_id'       => 0,
                'remark'            => '积分自动转入：'.$change_value. '转入比例：' . $rate,
                'relation'          => ''
            ];

            //修改爱心值
            $result = (new LoveChangeService())->pointTransfer($love_change_data);
            if (!$result) {
                Log::info('积分自动转入爱心值失败',print_r($love_change_data,true));
                DB::rollBack();
                return false;
            }
        }
        DB::commit();

        return true;
    }



    private function getRate($memberModel)
    {
        $set = Setting::get('point.set');

        $rate = 0;

        //如果全局比例为空、为零
        if (empty($set['transfer_love_rate'])) {
            $rate = 0;
        }

        //全局比例设置
        if (isset($set['transfer_love_rate']) && $set['transfer_love_rate'] > 0) {
            $rate = $set['transfer_love_rate'];
        }

        //会员独立设置判断
        if (isset($memberModel->pointLove) && $memberModel->pointLove->rate > 0) {
            $rate              =  $memberModel->pointLove->rate;

        }

        //独立设置为 -1，跳过此会员
        if (isset($memberModel->pointLove) && $memberModel->pointLove->rate == -1) {
            $rate = 0;
        }

        return $rate;
    }



    private function getExchange($memberModel,$change_value)
    {
        $set = Setting::get('point.set');

        $transfer_integral = 1;

        $transfer_love = 1;

        //如果全局比例为空
        if (empty($set['transfer_integral']) || empty($set['transfer_integral_love'])){
            $transfer_integral = 1;

            $transfer_love = 1;
        }

        //全局比例设置
        if (isset($set['transfer_integral']) && $set['transfer_integral'] > 0) {
            $transfer_integral = $set['transfer_integral'];
        }

        //全局比例设置
        if (isset($set['transfer_integral_love']) && $set['transfer_integral_love'] > 0) {
            $transfer_love = $set['transfer_integral_love'];
        }

        //会员独立设置判断
        if (isset($memberModel->pointLove) && $memberModel->pointLove > 0) {
            //判断会员是否单独设置积分转入爱心值比例
            if ($memberModel->pointLove->transfer_love && $memberModel->pointLove->transfer_integral) {

                $transfer_love = $memberModel->pointLove->transfer_love;

                $transfer_integral = $memberModel->pointLove->transfer_integral;
            }
        }

        $rate = bcmul(bcdiv($transfer_love,$transfer_integral,4),$change_value,2);

        return $rate;

    }

}
