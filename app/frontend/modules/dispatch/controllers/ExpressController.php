<?php

namespace app\frontend\modules\dispatch\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\Order;
use Yunshop\JdSupply\services\JdOrderService;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/6
 * Time: 下午4:03
 */
class ExpressController extends ApiController
{
    protected $order;


    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function index()
    {
        $order = $this->getOrder();
        if (!isset($order)) {
            throw new AppException('未找到订单');
        }
        if (!isset($order->express) && $order->plugin_id != 44) {
            throw new AppException('未找到配送信息');
        }
        //供货链订单
        if ($order->plugin_id==44 && app('plugins')->isEnabled('jd-supply')) {
            $list = JdOrderService::jdExpressInfo($order->order_sn,$order->hasManyJdOrderGoods[0]->jd_option_id);
            $data['express_sn'] = $list['data']['info']['no'];
            $data['company_name'] = $list['data']['info']['name'];
            foreach ($list['data']['list'] as $k=>$v) {
                $data['data'][$k]['context'] = $v['message'];
                $data['data'][$k]['ftime'] = date('Y-m-d H:i:s',$v['time']);
            }
            $data['thumb'] = $order->hasManyOrderGoods[0]->thumb;
        } else {
            //$data
            $express = $order->express->getExpress($order->express->express_code, $order->express->express_sn);
            \Log::info('---发货1express---', ['express' => $express, 'express_code' => $order->express->express_code, 'express_sn' => $order->express->express_sn]);
            $data['express_sn'] = $order->express->express_sn;
            $data['company_name'] = $order->express->express_company_name;
            $data['data'] = $express['data'];
            $data['thumb'] = $order->hasManyOrderGoods[0]->thumb;
            $data['tel'] = $express['tel'];
            $data['status_name'] = $express['status_name'];
            \Log::info('---发货2express---', $data);
        }
        return $this->successJson('成功', $data);
    }
    
    protected function _getOrder($order_id){
        return Order::find($order_id);

    }
    private function getOrder()
    {
        if (!isset($this->order)) {
            $order_id = request('order_id');
            $this->order = $this->_getOrder($order_id);

        }
        return $this->order;
    }

}