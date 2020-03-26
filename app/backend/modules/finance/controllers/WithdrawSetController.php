<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/14 上午9:58
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\controllers;


use app\backend\modules\finance\models\Withdraw;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;

class WithdrawSetController extends BaseController
{
    public function see()
    {
        $set = Setting::get('withdraw.balance');
        $resultModel = \YunShop::request()->withdraw;
        if ($resultModel) {
            $validator = null;
            foreach ($resultModel as $key => $item) {
                $validator = (new Withdraw())->validator($item);
                if ($validator->fails()) {
                    $this->error($validator->messages());
                    break;
                }
            }
            if ($validator && !$validator->fails()) {
                foreach ($resultModel as $key => $item) {
                    if ($key == 'balance') {
                        (new \app\common\services\operation\BalanceSetLog(['type'=> 'withdraw.balance','old'=>Setting::get('withdraw.'.$key),'new'=>$item], 'update'));
                    } elseif ($key == 'income') {
                        (new \app\common\services\operation\IncomeSetLog(['old'=>Setting::get('withdraw.'.$key),'new'=>$item], 'update'));
                        //  去空值
                        foreach($item['servicetax'] as $k=>$v){
                            $item['servicetax'][$k] = array_filter($v);
                        }
                        $item['servicetax'] = array_filter($item['servicetax']);
                    }

                    Setting::set('withdraw.' . $key, $item);

                }
                return $this->message('设置保存成功', Url::absoluteWeb('finance.withdraw-set.see'));
            }
        }

        return view('finance.withdraw.withdraw-set', [
            'set' => $set
        ])->render();
    }


}
