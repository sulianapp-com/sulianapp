<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/2/23
 * Time: 下午10:57
 */

namespace app\backend\modules\system\modules;

use Illuminate\Database\Eloquent\Model;

class SyssetModel extends Model
{
    public $table = 'sz_yi_sysset';

    public static function getSysInfo($uniacid)
    {
        return SyssetModel::where('uniacid', $uniacid)->first();
    }
}