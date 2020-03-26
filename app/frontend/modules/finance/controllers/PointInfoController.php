<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/12
 * Time: 下午4:00
 */

namespace app\frontend\modules\finance\controllers;

use app\common\components\ApiController;
use app\common\services\finance\PointService;
use app\frontend\modules\finance\models\PointLog;

class PointInfoController extends ApiController
{
    public function index()
    {
        $member_id = \YunShop::app()->getMemberId();
        $type = \YunShop::request()->status;
        $list = PointLog::getPointLogList($member_id, $type)->paginate(15);
        return $this->successJson('成功', [
            'list' => $list
        ]);
    }
}