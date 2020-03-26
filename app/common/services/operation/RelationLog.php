<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/3
 * Time: 9:58
 */

namespace app\common\services\operation;


class RelationLog extends OperationBase
{
    public $modules = 'member';

    public $type = 'relation';

    protected function modifyDefault()
    {
        $this->setLog('mark', $this->model->getOriginal('id'));
    }


    /**
     * 获取模型需要记录的字段
     * @return mixed
     */
    protected function recordField()
    {
        return [
            'status' => ['field_name'=>'会员关系链' , 1=> '启用关系链', 0=>' 禁用关系链'],
            'become_child' => ['field_name' => '会员成为下线条件',0=>'首次点击分享链接', 1=>'首次下单', 2=>'首次付款'],
            'become'       => '获得发展下线权利条件',

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

                if ($key == 'become') {
                    $this->modify_fields[$key]['old_content'] = $this->arrayValue($model,$model->getOriginal($key));
                    $this->modify_fields[$key]['new_content'] = $this->arrayValue($model,$model->{$key});
                } else {

                    $this->modify_fields[$key]['old_content'] = $model->getOriginal($key);
                    $this->modify_fields[$key]['new_content'] = $model->{$key};
                }
            }
        }

        return $this->modify_fields;
    }

    private function arrayValue($model, $key)
    {
        $array = [
            'key' => $key,
            0 => '无条件',
            1 => '申请',
            2 => '消费达到('.$model->become_ordercount.')次',
            3 => '消费达到('.$model->become_moneycount.')元',
            4 => '购买指定商品ID:'.$model->become_goods_id,
        ];

        return $array;
    }
}