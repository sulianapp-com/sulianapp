<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/12/14
 * Time: 上午11:38
 */

namespace app\frontend\repositories;

use app\frontend\modules\member\models\YzMemberAddress;
use app\frontend\modules\member\models\MemberAddress;

class MemberAddressRepository extends Repository
{
    public function model()
    {
        if (\Setting::get('shop.trade.is_street')) {
            return YzMemberAddress::class;
        } else {
            return MemberAddress::class;
        }
    }

}