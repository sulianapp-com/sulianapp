<?php
/**
 * Author 芸众商城 www.yunzshop.com
 * Date: 2018/3/30
 */

namespace app\backend\widgets\goods;

use app\common\components\Widget;
use app\common\facades\Setting;
use app\common\models\goods\GoodsFiltering;
use app\common\models\SearchFiltering;

class FilteringWidget extends Widget
{
    
    public function run()
    {
        $filtering = $this->getFilteringList();

        $goods_filter = GoodsFiltering::select('filtering_id')->ofGoodsId($this->goods_id)->get()->toArray();
        $goods_filter = array_pluck($goods_filter, 'filtering_id');

        return view('goods.widgets.filtering', [
            'filtering' => $filtering,
            'goods_filter' =>  $goods_filter,                                                           
        ])->render();
    }

    public function getFilteringList()
    {
        $filtering = SearchFiltering::where('parent_id', 0)->where('is_show', 0)->get();

        foreach ($filtering as $key => &$value) {
            $value['value'] = SearchFiltering::select('id', 'parent_id', 'name')->where('parent_id', $value->id)->get()->toArray();
        }
        return $filtering->toArray();
    }

}