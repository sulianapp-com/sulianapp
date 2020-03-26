<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/7
 * Time: 上午9:53
 */

namespace app\common\models;

use app\common\models\member\MemberShopInfo as YzMember;

class UniAccount extends BaseModel
{
    protected $guarded = [];
    public $table = 'uni_account';
    public $primaryKey = 'uniacid';

    public function __construct()
    {
        if (config('app.framework') == 'platform') {
            $this->table = 'yz_uniacid_app';
        }
    }

    public static function checkIsExistsAccount($uniacid)
    {
        return self::find($uniacid);
    }
    public static function getEnable(){
        return YzMember::select('uniacid')->distinct()->get();
    }
}