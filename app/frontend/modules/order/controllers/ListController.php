<?php

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\frontend\models\Order;

class ListController extends ApiController
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @return Order
     */
    protected function getOrder()
    {
        if(!isset($this->order)){
            return $this->_getOrder();
        }
        return $this->order;
    }

    /**
     * @return Order
     */
    protected function _getOrder()
    {
        return $this->order = app('OrderManager')->make('Order')->newQueryWithoutScopes()->uid()->orders()->where(app('OrderManager')->make('Order')->getTable().'.status', '<>', '-1')->hidePluginIds()->where('plugin_id','<','900')->with(['hasOnePayType','process']);
    }

    protected function getData()
    {
        $pageSize = request()->input('pagesize',20);
        return $this->getOrder()->where(app('OrderManager')->make('Order')->getTable().'.is_member_deleted',0)->paginate($pageSize)->toArray();
    }

    /**
     * 所有订单(不包括"已删除"订单)
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->successJson($msg = 'ok', $data = $this->getData());

    }

    /**
     * 待付款订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitPay()
    {
        $this->getOrder()->waitPay();
        return $this->successJson($msg = 'ok', $data = $this->getData());

    }

    /**
     * 待发货订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitSend()
    {
        $this->getOrder()->waitSend();
        return $this->successJson($msg = 'ok', $data = $this->getData());

    }

    /**
     * 待收货订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function waitReceive()
    {
        $this->getOrder()->waitReceive();

        return $this->successJson($msg = 'ok', $data = $this->getData());
    }

    /**
     * 已完成订单
     * @return \Illuminate\Http\JsonResponse
     */
    public function completed()
    {
        $this->getOrder()->completed();

        return $this->successJson($msg = 'ok', $data = $this->getData());
    }
}