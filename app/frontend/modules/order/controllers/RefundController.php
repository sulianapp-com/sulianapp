<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\refund\Apply;
use app\common\models\refund\RefundApply;
use app\frontend\models\Order;
use Request;
use app\backend\modules\goods\models\ReturnAddress;
use Yunshop\AreaDividend\models\AgentOrder;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/12
 * Time: 上午10:38
 */
class RefundController extends ApiController
{
    public function returnAddress() {
        $plugins_id = \YunShop::request()->plugins_id ? \YunShop::request()->plugins_id : 0;
        $store_id = \YunShop::request()->store_id ? \YunShop::request()->store_id : 0;
        $supplier_id = \YunShop::request()->supplier_id ? \YunShop::request()->supplier_id : 0;
        $address = ReturnAddress::getOneByPluginsId($plugins_id, $store_id, $supplier_id);

        if (app('plugins')->isEnabled('area-dividend') && request()->refund_id) {
            $orderRefund = RefundApply::select()
                ->where('id', request()->refund_id)
                ->first();
            $agentOrder = AgentOrder::select()
                ->where('order_id', $orderRefund->order_id)
                ->first();
            if ($orderRefund && $agentOrder) {
                $address = \app\common\models\goods\ReturnAddress::uniacid()
                    ->where('plugins_id', \app\common\modules\shop\ShopConfig::current()->get('plugins.area-dividend.id'))
                    ->where('store_id', $agentOrder->agent_id)
                    ->where('is_default', 1)
                    ->first();
            }
        }

        if ($address) {
            return $this->successJson('获取退货地址成功!', $address->toarray());
        }
        return $this->errorJson('获取退货地址失败',$address);
    }

}