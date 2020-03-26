<?php

namespace app\common\modules\goods;

use app\common\models\Goods;
use app\framework\Repository\Repository;

class GoodsRepository extends Repository
{
    protected $modelName = Goods::class;
    protected $excludeFields = ['content'];
}