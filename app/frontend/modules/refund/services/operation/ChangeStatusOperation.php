<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/20
 * Time: 下午8:16
 */

namespace app\frontend\modules\refund\services\operation;

use app\common\exceptions\AppException;

abstract class ChangeStatusOperation extends RefundOperation
{
    /**
     * 改变后状态
     * @var int
     */
    protected $statusAfterChanged;
    /**
     * 更新申请表
     * @return bool
     */
    protected function updateTable(){
        $this->status = $this->statusAfterChanged;
        if(isset($this->timeField)){
            $timeFields = $this->timeField;
            $this->$timeFields = time();
        }
        return $this->save();
    }

    /**
     * 执行订单操作
     * @return bool
     * @throws AppException
     */
    public function execute()
    {
        if (!in_array($this->status, $this->statusBeforeChange)) {
            throw new AppException("售后申请状态不满足{$this->name}操作");
        }
        $result = $this->updateTable();
        //$this->fireEvent();
        return $result;
    }
}