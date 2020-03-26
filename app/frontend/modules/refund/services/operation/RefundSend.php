<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/3
 * Time: 下午3:43
 */

namespace app\frontend\modules\refund\services\operation;

use app\common\models\refund\ReturnExpress;
use \Request;

class RefundSend extends ChangeStatusOperation
{
    protected $statusBeforeChange = [self::WAIT_RETURN_GOODS];
    protected $statusAfterChanged = self::WAIT_RECEIVE_RETURN_GOODS;
    protected $name = '发货';
    protected $timeField = 'send_time';

    protected $past_tense_class_name = '';

    protected function updateTable()
    {
        $data = Request::only(['refund_id', 'express_sn']);
        $returnExpress = new ReturnExpress($data);
        //$data = Request::only(['refund_id', 'express_code', 'express_sn', 'express_company_name']);
        $returnExpress->express_company_name = Request::input('express_company_name');
        $returnExpress->express_code = Request::input('express_company_code');
        $returnExpress->save();
        parent::updateTable();
    }
}