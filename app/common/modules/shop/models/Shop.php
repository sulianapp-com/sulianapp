<?php

/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/19
 * Time: 2:45 PM
 */

namespace app\common\modules\shop\models;

use app\common\models\AccountWechats;
use app\common\models\MemberRelation;

/**
 * todo 商城类
 * Class Shop
 * @package app\common\modules\trade\models
 * @property int uniacid
 * @property int weid
 * @property int acid
 * @property AccountWechats account
 * @property MemberRelation memberRelation
 */
class Shop extends \app\common\models\Shop
{
    static $current;
    static $currentUniacid;

    /**
     * 当前公众号对应的商城
     * @return Shop
     */
    public static function current()
    {
        if (!isset(self::$current) || self::$currentUniacid != \YunShop::app()->uniacid) {
            self::$currentUniacid = \YunShop::app()->uniacid;
            self::$current = new self();
            self::$current->uniacid = self::$currentUniacid;
        }

        return self::$current;
    }

    public function memberRelation()
    {
        return $this->hasOne(MemberRelation::class, 'uniacid', 'uniacid');
    }

}
