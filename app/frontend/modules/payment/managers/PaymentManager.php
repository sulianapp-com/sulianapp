<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/11/7
 * Time: 下午6:22
 */

namespace app\frontend\modules\payment\managers;

use Illuminate\Container\Container;

class PaymentManager extends Container
{
    function __construct()
    {
        $this->singleton('PaymentTypeManager',function(PaymentManager $manager){
            return new PaymentTypeManager($manager);
        });
        $this->singleton('OrderPaymentTypeManager',function(PaymentManager $manager){
            return new OrderPaymentTypeManager($manager);
        });
        $this->singleton('OrderPaymentTypeSettingManager',function(PaymentManager $manager){
            return new OrderPaymentTypeSettingManager();
        });

    }
}