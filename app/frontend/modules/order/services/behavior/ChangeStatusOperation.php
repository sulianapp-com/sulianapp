<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/20
 * Time: 下午8:16
 */

namespace app\frontend\modules\order\services\behavior;


abstract class ChangeStatusOperation extends OrderOperation
{
    /**
     * @var int 改变后状态
     */
    protected $statusAfterChanged;
    /**
     * 更新订单表
     * @return bool
     */
    protected function updateTable(){
        $this->status = $this->statusAfterChanged;
        if(isset($this->time_field)){
            $time_fields = $this->time_field;
            $this->$time_fields = time();
        }
        return $this->save();
    }

    /**
     * 执行订单操作
     * @return bool|void
     * @throws \app\common\exceptions\AppException
     */
    public function handle()
    {
        parent::handle();
        $this->updateTable();
        $this->_fireEvent();
    }
}