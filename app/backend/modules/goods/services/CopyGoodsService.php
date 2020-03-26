<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/3
 * Time: 上午11:52
 */

namespace app\backend\modules\goods\services;


use app\backend\modules\goods\models\GoodsOption;
use app\backend\modules\goods\models\GoodsSpec;
use app\backend\modules\goods\models\GoodsSpecItem;
use app\common\models\Goods;

class CopyGoodsService
{
    public static function copyGoods($goods_id)
    {
        $goodsModel = Goods::uniacid()->find($goods_id);
        if (!$goodsModel) {
            return false;
            //$this->error('商品不存在.');
        }

        $newGoods = $goodsModel->replicate();
        $newGoods->save();

        $goodsModel->load('hasOneShare', 'hasOneDiscount','hasOneSale', 'hasOneGoodsDispatch', 'hasOnePrivilege');
        foreach($goodsModel->getRelations() as $relation => $item){
            if ($item) {
                unset($item->id);
                $newGoods->{$relation}()->create($item->toArray());
            }
        }

        $goodsModel->setRelations([]);
        $goodsModel->load('hasManyParams', 'hasManyOptions');
        foreach($goodsModel->getRelations() as $relation => $items){
            foreach($items as $item){
                if ($item) {
                    unset($item->id);
                    $newGoods->{$relation}()->create($item->toArray());
                }
            }
        }

        $goodsModel->setRelations([]);
        $goodsModel->load('hasManyGoodsCategory');
        foreach($goodsModel->getRelations() as $relation => $items){
            foreach($items as $item){
                if ($item) {
                    unset($item->id);
                    $item->goods_id = $newGoods->id;
                    $newGoods->{$relation}()->create($item->toArray());
                }
            }
        }
        //todo, 先复制老的规格,再复制规格项,再更新规格content字段,最后复制option,更新option specs字段
        $goodsSpecs = GoodsSpec::uniacid()->where('goods_id', $goodsModel->id)->get();

        $specItemIds = [];
        $item_ids = [];
        foreach($goodsSpecs as $goodsSpec){
            $newGoodsSpecModel = $goodsSpec->replicate();
            $newGoodsSpecModel->goods_id = $newGoods->id;
            //dd($newGoodsSpecModel);
            $newGoodsSpecModel->save();

            //获取旧的规格项
            $goodsSpecItems = GoodsSpecItem::uniacid()->where("specid", $goodsSpec->id)->get();

            foreach($goodsSpecItems as $goodsSpecItem){
                $newGoodsSpecItem = $goodsSpecItem->replicate();
                $newGoodsSpecItem->specid = $newGoodsSpecModel->id;
                $newGoodsSpecItem->save();

                $items = [
                    'old_item' => $goodsSpecItem->id,
                    'new_item' => $newGoodsSpecItem->id,
                ];

                array_push($item_ids, $items);
                array_push($specItemIds, $newGoodsSpecItem->id);
            }

            $newGoodsSpecModel->content = serialize($specItemIds);
            $newGoodsSpecModel->save();
        }

        $goodsOptions = GoodsOption::uniacid()->where('goods_id', $newGoods->id)->get();
        foreach($goodsOptions as $goodsOption){
            $specs = explode("_", $goodsOption->specs);
            $newSpecs = [];
            foreach($specs as $spec){
                foreach($item_ids as $item){
                    if ($item['old_item'] == $spec){
                        $newSpecs[] = $item['new_item'];
                    }
                }
            }
            $goodsOption->specs = implode("_", $newSpecs);
            $goodsOption->save();
        }
        return $newGoods;
    }
}