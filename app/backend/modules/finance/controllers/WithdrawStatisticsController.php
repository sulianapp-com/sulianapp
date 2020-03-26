<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/10/14 下午10:23
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\controllers;


use app\common\components\BaseController;
use app\common\models\Withdraw;

class WithdrawStatisticsController extends BaseController
{

    private $withdrawModel;

    public function preAction()
    {
        parent::preAction();
        $this->withdrawModel = new Withdraw();

    }

    public function index()
    {

        $search = \YunShop::request()->search;

        if ($search) {
            $time_data[] = ['start_time' =>strtotime($search['times']['start']), 'end_time' => strtotime($search['times']['end'])];
        } else {
            $time_data = $this->getDate();
        }

        $data = [];
        foreach ($time_data as $key => $item) {
            $data[] = [
                'time' => $search ? '时间段搜索' : date('Y-m-d', $item['start_time']),
                'balance' => $this->getWithdrawToBalanceAmounts($item['start_time'],$item['end_time']),
                'wechat' => $this->getWithdrawToWeChatAmounts($item['start_time'],$item['end_time']),
                'alipay' => $this->getWithdrawToAlipayAmounts($item['start_time'],$item['end_time']),
            ];
        }

        return view('finance.withdraw.withdraw-statistics',['data' => $data])->render();
    }



    private function getDate($begin_today = '', $end_today = '', $length = 6)
    {

        $begin_today = $begin_today ?: mktime(0,0,0,date('m'),date('d'),date('Y'));

        $end_today = $end_today ?: mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        $data = [];
        for ($i = 0; $i <= $length; $i++) {
            $data[] = ['start_time' => $begin_today, 'end_time' => $end_today];
            $begin_today = $begin_today - 86400;
            $end_today   = $end_today - 86400;
        }

        return $data;
    }


    private function getWithdrawToBalanceAmounts($start_time,$end_time)
    {
        return $this->withdrawModel->uniacid()->where('pay_way','balance')->whereBetween('created_at',[$start_time,$end_time])->sum('amounts');
    }

    private function getWithdrawToWeChatAmounts($start_time,$end_time)
    {
        return $this->withdrawModel->uniacid()->where('pay_way','wechat')->whereBetween('created_at',[$start_time,$end_time])->sum('amounts');
    }

    private function getWithdrawToAlipayAmounts($start_time,$end_time)
    {
        return $this->withdrawModel->uniacid()->where('pay_way','alipay')->whereBetween('created_at',[$start_time,$end_time])->sum('amounts');
    }

}
