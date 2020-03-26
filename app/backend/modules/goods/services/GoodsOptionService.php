<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 11:08
 */

namespace app\backend\modules\goods\services;

use app\backend\modules\goods\models\GoodsOption;

class GoodsOptionService
{


    public static function getOptions($goods_id, $allSpecs)
    {
        $options = GoodsOption::where('goods_id', $goods_id)->get();
        $specs = [];
        $html = '';
        if (count($options) > 0) {
            $specitemids = explode("_", $options[0]['specs']);
            foreach ($specitemids as $itemid) {
                foreach ($allSpecs as $spec) {
                    $specItems = $spec['items'];
                    foreach ($specItems as $specItem) {
                        if ($specItem['id'] == $itemid) {
                            $specs[] = $spec;
                            break;
                        }
                    }
                }
            }

            $html .= '<table class="table table-bordered table-condensed">';
            $html .= '<thead>';
            $html .= '<tr class="active">';
            $specs_len = count($specs);
            $newlen = 1;
            $h = [];
            $rowspans = [];
            for ($i = 0; $i < $specs_len; $i++) {
                $html .= "<th style='width:18%;'>" . $specs[$i]['title'] . "</th>";
                $itemlen = count($specs[$i]['items']);
                if ($itemlen <= 0) {
                    $itemlen = 1;
                }
                $newlen *= $itemlen;
                $h = array();
                for ($j = 0; $j < $newlen; $j++) {
                    $h[$i][$j] = [];
                }
                $l = count($specs[$i]['items']);
                $rowspans[$i] = 1;
                for ($j = $i + 1; $j < $specs_len; $j++) {
                    $rowspans[$i] *= count($specs[$j]['items']);
                }
            }
            $html .= '<th class="info" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">库存</div><div class="input-group"><input type="text" class="form-control option_stock_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
            $html .= '<th class="success" style="width:10%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">市场价格</div><div class="input-group"><input type="text" class="form-control option_marketprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
            $html .= '<th class="warning" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">销售价格</div><div class="input-group"><input type="text" class="form-control option_productprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
            $html .= '<th class="danger" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">成本价格</div><div class="input-group"><input type="text" class="form-control option_costprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
            $html .= '<th class="warning" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">红包价格</div><div class="input-group"><input type="text" class="form-control option_redprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_redprice\');"></a></span></div></div></th>';
            $html .= '<th class="primary" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品编码</div><div class="input-group"><input type="text" class="form-control option_goodssn_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_goodssn\');"></a></span></div></div></th>';
            $html .= '<th class="danger" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品条码</div><div class="input-group"><input type="text" class="form-control option_productsn_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productsn\');"></a></span></div></div></th>';
            $html .= '<th class="info" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">重量（克）</div><div class="input-group"><input type="text" class="form-control option_weight_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
            $html .= '<th class="info" style="width:13%;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">点击图片上传<br /> 推荐（100*100）</div></div></th>';

            $html .= '</tr></thead>';

            for ($m = 0; $m < $specs_len; $m++) {
                $k = 0;
                $kid = 0;
                $n = 0;
                for ($j = 0; $j < $newlen; $j++) {
                    $rowspan = $rowspans[$m];
                    if ($j % $rowspan == 0) {
                        $h[$m][$j] = array(
                            "html" => "<td rowspan='" . $rowspan . "'>" . $specs[$m]['items'][$kid]['title'] . "</td>",
                            "id" => $specs[$m]['items'][$kid]['id']
                        );
                    } else {
                        $h[$m][$j] = array(
                            "html" => "",
                            "id" => $specs[$m]['items'][$kid]['id']
                        );
                    }
                    $n++;
                    if ($n == $rowspan) {
                        $kid++;
                        if ($kid > count($specs[$m]['items']) - 1) {
                            $kid = 0;
                        }
                        $n = 0;
                    }
                }
            }
            $hh = "";
            for ($i = 0; $i < $newlen; $i++) {
                $hh .= "<tr>";
                $ids = [];
                for ($j = 0; $j < $specs_len; $j++) {
                    $hh .= $h[$j][$i]['html'];
                    $ids[] = $h[$j][$i]['id'];
                }
                $ids = implode("_", $ids);
                $val = [
                    "id" => "",
                    "title" => "",
                    "stock" => "",
                    "cost_price" => "",
                    "product_price" => "",
                    "market_price" => "",
                    "weight" => "",
                    'virtual' => '',
                    "red_price" => '',
                    'thumb' => '',
                ];
                foreach ($options as $option) {
                    if ($ids === $option['specs']) {
                        $val = [
                            "id" => $option['id'],
                            "title" => $option['title'],
                            "stock" => $option['stock'],
                            "cost_price" => $option['cost_price'],
                            "product_price" => $option['product_price'],
                            "market_price" => $option['market_price'],
                            "goods_sn" => $option['goods_sn'],
                            "product_sn" => $option['product_sn'],
                            "weight" => $option['weight'],
                            'virtual' => $option['virtual'],
                            'red_price' => $option['red_price'],
                            'thumb' => $option['thumb'],
                            'url' => yz_tomedia($option['thumb']),
                            //'option_ladder' => unserialize($o['option_ladders'])
                        ];
                        break;
                    }
                }
                $hh .= '<td class="info">';
                $hh .= '<input name="option_stock_' . $ids . '[]"  type="text" class="form-control option_stock option_stock_' . $ids . '" value="' . $val['stock'] . '"/>';
                $hh .= '<input name="option_id_' . $ids . '[]"  type="hidden" class="form-control option_id option_id_' . $ids . '" value="' . $val['id'] . '"/>';
                $hh .= '<input name="option_ids[]"  type="hidden" class="form-control option_ids option_ids_' . $ids . '" value="' . $ids . '"/>';
                $hh .= '<input name="option_title_' . $ids . '[]"  type="hidden" class="form-control option_title option_title_' . $ids . '" value="' . $val['title'] . '"/>';
                $hh .= '<input name="option_virtual_' . $ids . '[]"  type="hidden" class="form-control option_title option_virtual_' . $ids . '" value="' . $val['virtual'] . '"/>';
                $hh .= '</td>';
                //$hh .= '<td class="success"><input name="option_marketprice_' . $ids . '[]" type="text" class="form-control option_marketprice option_marketprice_' . $ids . '" value="' . $val['marketprice'] . '"/></td>';
                $hh .= '<td class="success"><input name="option_marketprice_' . $ids . '[]" type="text" class="form-control option_marketprice option_marketprice_' . $ids . '" value="' . $val['market_price'] . '"/>';

                $hh .= '</td>';
                $hh .= '<td class="warning"><input name="option_productprice_' . $ids . '[]" type="text" class="form-control option_productprice option_productprice_' . $ids . '" " value="' . $val['product_price'] . '"/></td>';

                $hh .= '<td class="danger"><input name="option_costprice_' . $ids . '[]" type="text" class="form-control option_costprice option_costprice_' . $ids . '" " value="' . $val['cost_price'] . '"/></td>';

                $hh .= '<td class="warning"><input name="option_redprice_' . $ids . '[]" type="text" class="form-control option_redprice option_redprice_' . $ids . '" " value="' . $val['red_price'] . '"/></td>';
                $hh .= '<td class="primary"><input name="option_goodssn_' . $ids . '[]" type="text" class="form-control option_goodssn option_goodssn_' . $ids . '" " value="' . $val['goods_sn'] . '"/></td>';
                    $hh .= '<td class="danger"><input name="option_productsn_' . $ids . '[]" type="text" class="form-control option_productsn option_productsn_' . $ids . '" " value="' . $val['product_sn'] . '"/></td>';
                $hh .= '<td class="info"><input name="option_weight_' . $ids . '[]" type="text" class="form-control option_weight option_weight_' . $ids . '" " value="' . $val['weight'] . '"/></td>';

                $hh .= '<td class="info"><div class="input-group"><input name="option_thumb_'.$ids.'[]" type="hidden" class="option_thumb_'.$ids.'" url="'.$val['url'].'" value="'.$val['thumb'].'"/><span><button style="display:none" class="btn btn-default" onclick="showImageDialog(this);" type="button">上传图片</button></span></div><div class="input-group" onclick="tu(this)" style="margin-top:.5em;"><img src="'.$val['url'].'" onerror="nofind()" class="img-responsive img-thumbnail" style="width:50px;height:50px"></div></td>';

                $hh .= '</tr>';

            }
            $html .= $hh;
            $html .= "</table>";
        }

        return $html;
    }
}