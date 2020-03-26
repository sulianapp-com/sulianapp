<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 上午11:32
 */

namespace app\backend\widgets\finance;

use app\common\components\Widget;
use app\common\facades\Setting;

class IncomeWidget extends Widget
{

    public function run()
    {
        $set = Setting::get('withdraw.income');

        $set['servicetax'] = array_values($set['servicetax']);

        return view('finance.withdraw.withdraw-income', [
            'set' => $set,
            'income_count' => count($set['servicetax']),
        ])->render();
    }
}

