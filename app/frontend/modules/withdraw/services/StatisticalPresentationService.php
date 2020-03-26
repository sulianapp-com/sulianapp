<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/16
 * Time: 15:35
 */

namespace app\frontend\modules\withdraw\services;

use app\frontend\modules\withdraw\models\Withdraw;


class StatisticalPresentationService
{
   
    //统计提现次数
    public function statisticalPresentation($type){
        $start = strtotime(date("Y-m-d"),time());
        $end = $start+60*60*24;
        $today_withdraw_count =  Withdraw::successfulWithdrawals($type,$start,$end);
        \Log::debug($type.'收入提现次数',$today_withdraw_count);
        if(app('plugins')->isEnabled('supplier')){
        	\Log::debug($type.'供应商提现次数',\Yunshop\Supplier\supplier\models\SupplierWithdraw::successfulWithdrawals($type,$start,$end));
            $today_withdraw_count += \Yunshop\Supplier\supplier\models\SupplierWithdraw::successfulWithdrawals($type,$start,$end);
        }

        return $today_withdraw_count;
    }

}