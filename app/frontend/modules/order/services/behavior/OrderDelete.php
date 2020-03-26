<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午11:21
 * comment: 订单删除
 */

namespace app\frontend\modules\order\services\behavior;

use app\common\models\Order;

class OrderDelete extends OrderOperation
{
    protected $statusBeforeChange = [ORDER::CLOSE, ORDER::COMPLETE];
    //protected $status_after_changed = -1;
    protected $name = '删除';
    protected $past_tense_class_name = 'OrderDeleted';

    /**
     * @return bool
     * @throws \app\common\exceptions\AppException
     */
    public function handle()
    {
        parent::handle();
        $this->is_member_deleted = 1;
        return $this->save();
    }

}