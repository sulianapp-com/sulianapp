<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/13
 * Time: 下午2:00
 */

namespace app\frontend\modules\refund\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\common\models\refund\RefundApply;

class DetailController extends ApiController
{
    public function index(\Illuminate\Http\Request $request){
        $this->validate([
            'refund_id' => 'required|integer',
        ]);
        $refundApply = RefundApply::find($request->query('refund_id'));
        if(!isset($refundApply)){
            throw new AppException('未找到该退款申请');
        }

        //判断是门店还是供应商
        $plugin = RefundApply::getIsPlugin($refundApply->order_id);
        if($plugin->is_plugin) {
            $refundApply->is_plugin = $plugin->is_plugin;
            $refundApply->supplier_id = RefundApply::getSupplierId($refundApply->order_id);
        }
        if ($plugin->plugin_id) {
            $refundApply->plugin_id = $plugin->plugin_id;
            $refundApply->store_id = RefundApply::getStoreId($refundApply->order_id);
        }



        return $this->successJson('成功',$refundApply);
    }
}