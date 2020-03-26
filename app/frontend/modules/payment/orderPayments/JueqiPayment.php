<?php


namespace app\frontend\modules\payment\orderPayments;

use app\common\helpers\Client;

class JueqiPayment extends BasePayment
{
    public function canUse()
    {
        //todo
        if(\Setting::get('plugin.jueqi_pay_set.switch') != 1 ){
            return false;
        }
        if(Client::is_weixin() !== true){
            return false;
        }
//        parent::canUse()//暂时注释，不读门店设置
        return  \YunShop::plugin()->get('jueqi-pay');
    }
}