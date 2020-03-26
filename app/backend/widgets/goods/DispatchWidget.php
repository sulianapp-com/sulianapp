<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets\goods;


use app\common\components\Widget;
use app\backend\modules\goods\models\GoodsDispatch;
use app\backend\modules\goods\models\Dispatch;

class DispatchWidget extends Widget
{

    public function run()
    {
        $dispatch = new GoodsDispatch();
        if ($this->goods_id && GoodsDispatch::getInfo($this->goods_id)) {
            $dispatch = GoodsDispatch::getInfo($this->goods_id);
        }

        $dispatch_templates = Dispatch::getAll();

        // edit 2018-06-01 by Yy
        if ($dispatch->dispatch_id) {
            $verify_dispatch = Dispatch::find($dispatch->dispatch_id);
            if ($verify_dispatch->is_plugin == 1) {
                $dispatch_templates = [
                    $verify_dispatch
                ];
            }
        }

        return view('goods.widgets.dispatch', [
            'dispatch' => $dispatch,
            'dispatch_templates' => $dispatch_templates
        ])->render();
    }
}