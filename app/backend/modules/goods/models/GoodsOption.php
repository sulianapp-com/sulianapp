<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 下午18:16
 */
namespace app\backend\modules\goods\models;


class GoodsOption extends \app\common\models\GoodsOption
{
    static protected $needLog = true;

    public static function saveOption($optionPost, $goods_id, $spec_items, $uniacid)
    {
        $option_ids = $optionPost['option_ids'];
        $len = count($option_ids);
        $optionids = [];
        for ($k = 0; $k < $len; $k++) {
            $ids = $option_ids[$k];
            $get_option_id = $optionPost['option_id_' . $ids][0];
            $idsarr = explode("_", $ids);
            $newids = array();
            foreach ($idsarr as $key => $ida) {
                foreach ($spec_items as $it) {
                    if ($it['get_id'] == $ida) {
                        $newids[] = $it['id'];
                        break;
                    }
                }
            }
            $newids = implode("_", $newids);
            $goodsOption = [
                "uniacid" => $uniacid,
                "title" => $optionPost['option_title_' . $ids][0],
                "product_price" => floatVal($optionPost['option_productprice_' . $ids][0]),
                "cost_price" => floatVal($optionPost['option_costprice_' . $ids][0]),
                "market_price" => floatVal($optionPost['option_marketprice_' . $ids][0]),
                "stock" => $optionPost['option_stock_' . $ids][0] ? $optionPost['option_stock_' . $ids][0] : 0,
                "weight" => floatVal($optionPost['option_weight_' . $ids][0]),
                "goods_sn" => $optionPost['option_goodssn_' . $ids][0],
                "product_sn" => $optionPost['option_productsn_' . $ids][0],
                "goods_id" => $goods_id,
                "specs" => $newids,
                //'virtual' => $data['type'] == 3 ? $_GPC['option_virtual_' . $ids][0] : 0,
                'virtual' => 0,
                "red_price" => $optionPost['option_redprice_' . $ids][0],
                'thumb' => $optionPost['option_thumb_' . $ids][0],
            ];


            //$totalstocks += $a['stock'];
            if (empty($get_option_id)) {
                //dd($goodsOption);
                $goodsOptionModel = static::create($goodsOption);
                $option_id = $goodsOptionModel->id;
            } else {
                //exit;
                static::updateOrCreate(['id' => $get_option_id], $goodsOption);
                $option_id = $get_option_id;
            }
            $optionids[] = $option_id;
        }
        if (count($optionids) > 0) {
            static::where('goods_id', '=', $goods_id)->whereNotIn('id', $optionids )->delete();
        } else {
            static::where('goods_id', '=', $goods_id)->delete();
        }
    }
}