<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 10:51
 */

namespace app\common\services\operation;


class SaleLog extends OperationBase
{
    public $modules = 'goods';

    public $type = 'update';

    protected function modifyDefault()
    {
        $this->setLog('mark', $this->model->getOriginal('goods_id'));
    }


    /**
     * 获取模型需要记录的字段
     * @return mixed
     */
    protected function recordField()
    {
        return [
            'ed_num'        => '单品满件包邮',
            'ed_money'      => '单品满额包邮',
            'ed_full_reduction'       => '单品满额立减',
            'award_balance' => '赠送余额',
            'point'         => '赠送积分',
            'max_point_deduct' => '积分最高抵扣',
            'min_point_deduct' => '积分最低抵扣',
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

            if ($model->isDirty('ed_full') || $model->isDirty('ed_reduction')) {
                $this->modify_fields['ed_full_reduction']['old_content'] = '满'.$model->getOriginal('ed_full').'元立减'.$model->getOriginal('ed_reduction').'元';
                $this->modify_fields['ed_full_reduction']['new_content'] = '满'.$model->ed_full.'元立减'.$model->ed_reduction.'元';
            }

            if ($model->isDirty($key)) {

                $this->modify_fields[$key]['old_content'] = $model->getOriginal($key);
                $this->modify_fields[$key]['new_content'] = $model->{$key};
            }
        }

        return $this->modify_fields;
    }
}