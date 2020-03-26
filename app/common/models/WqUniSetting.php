<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-10-18
 * Time: 10:39
 */

namespace app\common\models;


class WqUniSetting extends BaseModel
{
    protected $connection = 'mysql';

    public $table = 'uni_settings';

    public $timestamps = false;

}