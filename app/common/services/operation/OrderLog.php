<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 11:20
 */

namespace app\common\services\operation;


class OrderLog extends OperationBase
{
    public $modules = 'order';

    public $type = 'operating';

    protected function modifyDefault()
    {
        $this->setLog('mark', $this->model->getOriginal('id'));
    }

    protected function special()
    {
        $this->setLog('mark', $this->model->getOriginal('order_id'));
        $this->setLog('field_name', '修改订单备注');
        $this->setLog('field', 'remark');
        $this->setLog('old_content',$this->model->getOriginal('remark'));
        $this->setLog('new_content', $this->model->remark);

    }

    /**
     * 获取模型需要记录的字段
     * @return mixed
     */
    protected function recordField()
    {
        return [
            'status' => ['field_name'=>'订单操作' , -1=> '关闭订单', 0=>'待付款', 1=>'待发货', 2=>'待收货', 3=>'订单完成'],
            //'status' => ['field_name'=>'订单操作' , -1=> '关闭订单', 0=>'订单确认付款', 1=>'订单确认发货', 2=>'订单确认收货', 3=>'订单完成'],
            'refund_id' => '订单退款处理',
            'price'     => '订单改价',

        ];
    }

    /**
     * 获取模型修改了哪些字段
     * @param object array
     * @return array
     */
    protected function modifyField()
    {
        $model = $this->model;

        if (is_null($model->getOriginal())) {
            return [];
        }

        foreach ($this->recordField() as $key => $item) {
            if ($model->isDirty($key)) {

                $this->modify_fields[$key]['old_content'] = $model->getOriginal($key);
                $this->modify_fields[$key]['new_content'] = $model->{$key};
            }
        }

        return $this->modify_fields;
    }
}