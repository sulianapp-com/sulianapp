<?php

namespace app\common\components;

use app\common\models\ApiAccessToken;

class TripartiteApiController extends BaseController
{
    /**
     * @throws \app\common\exceptions\AppException
     * @throws \app\common\exceptions\TokenHasExpiredException
     * @throws \app\common\exceptions\TokenHasRevokedException
     * @throws \app\common\exceptions\TokenNotFoundException
     */
    public function preAction()
    {
        $this->validate([
            'access_token' => 'required',
        ]);
        ApiAccessToken::verify(\YunShop::app()->uniacid, request()->input('access_token'));

        parent::preAction();
    }
}