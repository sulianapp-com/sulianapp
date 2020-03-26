<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/3/30
 */

namespace app\backend\modules\goods\models;

/**
* 
*/
class GoodsFiltering extends \app\common\models\goods\GoodsFiltering
{
    
    // public function relationValidator($goodsId, $data, $operate)
    // {
    // }

    public function relationSave($goodsId, $data, $operate)
    {
        if (!$goodsId) {
            return false;
        }
        //判断deleted
        if ($operate == 'deleted') {
            return GoodsFiltering::where('goods_id', $goodsId)->delete();
        }
        if ($operate != 'created') {
            GoodsFiltering::where('goods_id', $goodsId)->delete();
        }
        $data = array_filter($data);
        if ($data) {
            foreach ($data as $key => $value) {
                GoodsFiltering::insert([
                    'goods_id' => $goodsId,
                    'filtering_id' => $value
                ]);         
            }
            
        }

    }
   
}