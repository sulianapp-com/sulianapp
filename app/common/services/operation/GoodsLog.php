<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 10:11
 */

namespace app\common\services\operation;

use app\common\models\OperationLog;

class GoodsLog extends OperationBase
{

    public $modules = 'goods';

    public $type = 'update';


    public $modify_fields;

    public function __construct($model, $type = null)
    {
        parent::__construct($model, $type);
    }

    protected function modifyDefault()
    {
        $this->setLog('mark', $this->model->id);
    }


    /**
     * 获取模型需要记录的字段
     * @return mixed
     */
     protected function recordField()
     {
         return [
             'title'        => '商品标题',
             'price'        => '现价',
             'market_price' => '原价',
             'cost_price'   => '成本价',
             'type'         => ['field_name' => '商品类型',1=> '实体', 2=>' 虚拟'],
             'is_recommand' => ['field_name' => '商品属性',0=>'取消推荐', 1=>'推荐'],
             'is_new'       => ['field_name' => '商品属性',0=>'取消新品', 1=>'新品'],
             'is_hot'       => ['field_name' => '商品属性',0=>'取消热卖', 1=>'热卖'],
             'is_discount'  => ['field_name' => '商品属性',0=>'取消促销', 1=>'促销'],
             'weight'       => '商品重量',
             'stock'        => '商品库存',
             'virtual_sales'=> '商品虚拟销量',
             'reduce_stock_method' => ['field_name' => '减库存方式',0=>'拍下减库存',1=>'付款减库存',2=>'永不减库存'],
             'status'       => ['field_name' => '上下架',0=>'下架',1=>'上架'],
         ];
     }

    /**
     * 获取模型修改了哪些字段
     * @return array
     */
    protected function modifyField()
    {
        $model = $this->model;

        foreach ($this->recordField() as $key => $item) {

            if ($model->isDirty($key)) {

                 $this->modify_fields[$key]['old_content'] = $model->getOriginal($key);
                 $this->modify_fields[$key]['new_content'] = $model->{$key};
            }
        }

        return $this->modify_fields;
    }

    protected function createLog()
    {
        $model = $this->model;

        $this->setLog('type', 'create');
        $this->setLog('field', 'id');
        $this->setLog('field_name', '商品ID');
        $this->setLog('old_content', $model->id);
        $this->setLog('new_content', $model->id);

        OperationLog::create($this->logs);
    }

}