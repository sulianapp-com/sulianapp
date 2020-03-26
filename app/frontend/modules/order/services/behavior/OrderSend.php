<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午3:43
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\DispatchType;
use app\common\models\Order;
use app\common\models\order\Express;
use app\common\repositories\ExpressCompany;

class OrderSend extends ChangeStatusOperation
{
    protected $statusBeforeChange = [ORDER::WAIT_SEND];
    protected $statusAfterChanged = ORDER::WAIT_RECEIVE;
    protected $name = '发货';
    protected $time_field = 'send_time';
    public $params = [];
    protected $past_tense_class_name = 'OrderSent';

    protected function _fireEvent()
    {
        $this->fireSentEvent();
    }
    /**
     * @return bool|void
     */
    protected function updateTable()
    {
        $data = $this->params ? $this->params : request()->input();

        if ($data['express_code']) {
            
            //实体订单
            // $order_id = request()->input('order_id');   
            $order_id = $data['order_id'];   

            $db_express_model = Express::where('order_id', $order_id)->first();

            !$db_express_model && $db_express_model = new Express();

            $db_express_model->order_id = $order_id;
            // $db_express_model->express_code = request()->input('express_code','');
            $db_express_model->express_code = $data['express_code']?:'';

            // $db_express_model->express_company_name = request()->input('express_company_name', function (){
            //     return array_get(ExpressCompany::create()->where('value',request()->input('express_code',''))->first(),'name','');
            // });
            $db_express_model->express_company_name = array_get(ExpressCompany::create()->where('value', $data['express_code'])->first(),'name','');
            
            // $db_express_model->express_sn = request()->input('express_sn','');
            $db_express_model->express_sn = $data['express_sn']?:'';

            // dd($db_express_model->express_sn);
            $db_express_model->save();
        }
        parent::updateTable();
    }

}