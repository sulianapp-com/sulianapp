<?php

namespace app\frontend\modules\accessToken\controllers;

use app\common\components\BaseController;
use app\common\models\ApiRefreshToken;
use app\frontend\modules\accessToken\PreApiAccessToken;

class GetController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function index()
    {
        $this->validate(
            [
                'refresh_token' => 'required'
            ]
        );
        ApiRefreshToken::verify(\YunShop::app()->uniacid, request()->input('refresh_token'));
        $accessToken = new PreApiAccessToken();
        $accessToken->save();
        return $this->successJson('成功', array_only($accessToken->toArray(),['expires_at','uniacid','access_token']));
    }
}