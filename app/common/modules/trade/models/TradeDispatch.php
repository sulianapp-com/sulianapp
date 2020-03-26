<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/23
 * Time: 5:11 PM
 */

namespace app\common\modules\trade\models;

use app\common\models\BaseModel;


class TradeDispatch extends BaseModel
{
    protected $appends = ['delivery_method'];

    /**
     * @var Trade
     */
    private $trade;

    public function init(Trade $trade)
    {
        $this->trade = $trade;
        $this->setRelation('default_member_address', $this->getMemberAddress());
        return $this;
    }

    /**
     * @return mixed
     */
    private function getMemberAddress()
    {
        return $this->trade->orders->first()->orderAddress->getMemberAddress();
    }

    protected function _gteDeliveryMethod()
    {
        $parameter = [];
        $configs = \app\common\modules\shop\ShopConfig::current()->get('shop-foundation.order-delivery-method');
        if($configs) {
            foreach ($configs as $pluginName => $pluginOperators) {
                $class = array_get($pluginOperators,'class');
                $function =array_get($pluginOperators,'function');
                if(class_exists($class) && method_exists($class,$function) && is_callable([$class,$function])) {
                    $plugin_data = $class::$function();
                    if ($plugin_data) {
                        $parameter[] = $plugin_data;
                    }
                }
            }
        }
        return $parameter;
    }

    public function getDeliveryMethodAttribute()
    {
        return $this->_gteDeliveryMethod();
    }


}