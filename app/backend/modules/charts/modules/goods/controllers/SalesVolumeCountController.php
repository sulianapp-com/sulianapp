<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2019/1/2
 * Time: 18:51
 */

namespace app\backend\modules\charts\modules\goods\controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\models\Goods;

class SalesVolumeCountController extends BaseController
{
    public function index()
    {
        $list = Goods::uniacid()->isPlugin()->where('plugin_id', 0)->orderBy('real_sales','desc')->where('status', 1)->paginate(20);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());;

        return view('charts.goods.sales_volume_count',[
            'list' => $list,
            'pager' => $pager,
        ])->render();
    }

    public function salesPrice()
    {

        $list = Goods::uniacid()
            ->selectRaw('(price * real_sales) as sales_price, title')
            ->isPlugin()
            ->where('plugin_id', 0)
            ->orderBy('sales_price','desc')
            ->where('status', 1)
            ->paginate(20);

        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());;

        return view('charts.goods.sales_count',[
            'list' => $list,
            'pager' => $pager,
        ])->render();
    }

}