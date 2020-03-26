<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/17
 * Time: 下午6:10
 */

namespace app\common\exceptions;


class UniAccountNotFoundException extends ShopException
{

    public function getStatusCode()
    {
        return ErrorCode::UNI_ACCOUNT_NOT_FOUND;
    }

}