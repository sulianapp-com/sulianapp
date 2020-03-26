<?php

namespace app\frontend\controllers;

use app\common\components\BaseController;
use app\common\models\Option;
use app\common\modules\goods\GoodsRepository;
use app\common\modules\option\OptionRepository;
use Yunshop\Love\Modules\Goods\GoodsLoveRepository;

class TestController extends BaseController
{
    public function index()
    {
        $a = GoodsLoveRepository::findManyBy('goods_id',[63, 17]);
        dd($a);
    }
}