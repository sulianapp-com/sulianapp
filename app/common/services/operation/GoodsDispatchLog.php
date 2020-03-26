<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 15:32
 */

namespace app\common\services\operation;


class GoodsDispatchLog extends OperationBase
{
    public $modules = 'goods';

    public $type = 'update';

    protected function modifyDefault()
    {
        $this->setLog('mark', $this->model->goods_id);
    }

    /**
     * 获取模型需要记录的字段
     * @return mixed
     */
    protected function recordField()
    {
        return [
            'dispatch_type' => '运费设置',
            'dispatch_id'   => '',
            'dispatch_price' => '',
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

        foreach ($this->recordField() as $key => $item) {


            if ($model->isDirty($key)) {

                if ($key == 'dispatch_type' || $key = 'dispatch_id' || $key == 'dispatch_price') {
                    $arr_1 = $this->arrayValue($model->getOriginal('dispatch_type'));
                    $arr_2 = $this->arrayValue($model->dispatch_type);
                    $str_1 = $arr_1['value'].':'.$model->getOriginal($arr_1['key']);
                    $str_2 =  $arr_2['value'].':'. $model->{$arr_2['key']};
                    $this->modify_fields['dispatch_type']['old_content'] = $str_1;
                    $this->modify_fields['dispatch_type']['new_content'] = $str_2;
                }
            }
        }
        return $this->modify_fields;
    }

    protected  function arrayValue($key) {

        $dispatch_type= [0=>['value'=>'运费模板', 'key'=> 'dispatch_id'], 1=>['value'=>'统一邮费', 'key'=> 'dispatch_price']];
        return $dispatch_type[$key];
    }
}