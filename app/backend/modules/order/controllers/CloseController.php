<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2019/4/30
 * Time: 4:15 PM
 */

namespace app\backend\modules\order\controllers;

use app\common\components\BaseController;
use app\frontend\modules\order\services\OrderService;

class CloseController extends BaseController
{
    /**
     * @return mixed
     * @throws \app\common\exceptions\AppException
     */
    public function index()
    {
        OrderService::orderClose(request()->only(['order_id']));
        return $this->successJson();
    }
}