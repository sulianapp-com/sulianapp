<?php

namespace app\common\modules\option;

use app\common\models\Option;
use app\framework\Repository\Repository;

class OptionRepository extends Repository
{
    protected $modelName = Option::class;

}