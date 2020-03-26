<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 15:33
 */

namespace app\common\services\operation;


use app\common\models\MemberLevel;

class DiscountLog extends OperationBase
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
            //'level_discount_type' => ['field_name' => '折扣类型',1=>'会员等级'],
            //'discount_method' => ['field_name' => '折扣方式',1=>'折扣', 2=>'固定金额'],
            'discount_value'  => '会员等级折扣',
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

                if ($key == 'discount_value') {
                    $level_name = MemberLevel::where('id', $model->level_id)->value('level_name');
                    $level_name = is_null($level_name) ? '普通会员': $level_name;

                    $this->modify_fields['discount_value']['old_content'] = ['key'=> $model->discount_method,1=>'折扣方式:折扣', 2=>'折扣方式:固定金额'];
                    $this->modify_fields['discount_value']['new_content'] = ['key'=> $model->discount_method ,1=>$level_name.'(等级折扣:'.$model->discount_value.'%)', 2=>$level_name.'(等级折扣:'.$model->discount_value.'元)'];
                }
            }

        }

        return $this->modify_fields;
    }
}