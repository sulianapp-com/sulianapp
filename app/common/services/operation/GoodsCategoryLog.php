<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 16:10
 */

namespace app\common\services\operation;


class GoodsCategoryLog extends OperationBase
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
             'category_id'        => '商品分类',
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

             if ( $model->isDirty($key)) {

                 $this->modify_fields[$key]['old_content'] = '商品ID:'.$model->goods_id;
                 $this->modify_fields[$key]['new_content'] = '分类ID:'.$model->{$key};
             }
         }

         return $this->modify_fields;
     }
}