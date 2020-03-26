<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/7/30
 * Time: 上午10:42
 */

namespace app\common\repositories;

use Illuminate\Database\Eloquent\Collection;

class ExpressCompany extends Collection
{
    static public function create()
    {
        $file = implode([app()->path(), '..', 'static', 'source', 'expresscom.json'], DIRECTORY_SEPARATOR);
        $json = file_get_contents($file);

        $items = json_decode($json, true);
        return new static($items);
    }
}