<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/20
 * Time: 下午2:21
 */

namespace app\frontend\modules\order\controllers;

use app\common\exceptions\AppException;
use app\frontend\models\Member;

class CreateAllController extends CreateController
{

    /**
     * @return void|static
     * @throws AppException
     * @throws \app\common\exceptions\MemberNotLoginException
     */
    protected function _getMemberCarts()
    {
        $memberCarts =  Member::current()->memberCarts;
        if($memberCarts->isEmpty()){
            throw new AppException("该用户(".Member::current()->uid.")购物车记录为空");
        }
    }
}