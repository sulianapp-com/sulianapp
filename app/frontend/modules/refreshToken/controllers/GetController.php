<?php

namespace app\frontend\modules\refreshToken\controllers;

use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\modules\tripartiteApi\AppSecret;
use app\frontend\modules\refreshToken\PreApiRefreshToken;

class GetController extends BaseController
{
    public function index()
    {
        $this->validate([
            'app_secret' => 'required'
        ]);

        if(AppSecret::get() != request()->input('app_secret')){
            throw new ShopException('app_secret无效');
        }
        $refreshToken = new PreApiRefreshToken();
        $refreshToken->save();
        return $this->successJson('成功', array_only($refreshToken->toArray(),['expires_at','uniacid','refresh_token']));
    }
}