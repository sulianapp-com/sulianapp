<?php

namespace app\frontend\modules\refreshToken;


use app\common\models\ApiRefreshToken;

class PreApiRefreshToken extends ApiRefreshToken
{
    public function touchAttributes()
    {
        //30天过期
        $this->expires_at = $this->getExpiresAt();
        $this->uniacid = (int)\YunShop::app()->uniacid;
        $this->refresh_token = $this->getRefreshToken();
        $this->revoked = false;


    }

    private function getRefreshToken()
    {
        return base64_encode(md5(md5($this->uniacid) . time() . range(0, 10000)));
    }

    private function getExpiresAt()
    {
        return time() + 60 * 60 * 24 * 30;
    }

    public function toArray()
    {
        $this->touchAttributes();
        return parent::toArray();
    }

    //public function
    public function save(array $options = [])
    {
        $this->touchAttributes();
        return parent::save($options);
    }
}