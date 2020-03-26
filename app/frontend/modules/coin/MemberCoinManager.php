<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/11
 * Time: 下午3:01
 */

namespace app\frontend\modules\coin;

use app\frontend\modules\finance\models\MemberPointCoin;
use Illuminate\Container\Container;

class MemberCoinManager extends Container
{
    public function __construct()
    {

        $this->bind('point',function($memberCoinManger,array $params){
            return new MemberPointCoin($params[0]);
        });
    }
}