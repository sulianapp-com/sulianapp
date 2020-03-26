<?php

namespace app\common\models;

use app\common\exceptions\TokenHasExpiredException;
use app\common\exceptions\TokenHasRevokedException;
use app\common\exceptions\TokenNotFoundException;
use Carbon\Carbon;

/**
 * Class ErpApiAccessToken
 * @package Yunshop\ErpApi\common\models
 * @property bool revoked
 * @property Carbon expires_at
 * @property string access_token
 * @property int uniacid
 * @property int id
 */
class ApiAccessToken extends BaseModel
{
    public $table = 'yz_api_access_token';
    public $timestamps = true;
    protected $guarded = [''];

    /**
     * @return bool
     * @throws TokenHasExpiredException
     * @throws TokenHasRevokedException
     */
    public function valid()
    {
        if ($this->expires_at <= time()) {
            throw new TokenHasExpiredException();
        }
        if ($this->revoked) {
            throw new TokenHasRevokedException();
        }
        return true;
    }

    /**
     * @param $uniacid
     * @param $token
     * @return bool
     * @throws TokenHasExpiredException
     * @throws TokenHasRevokedException
     * @throws TokenNotFoundException
     */
    static public function verify($uniacid, $token)
    {
        $accessToken = self::where([
            'uniacid' => $uniacid,
            'access_token' => $token,
        ])->first();
        if (!$accessToken) {
            throw new TokenNotFoundException();
        }
        return $accessToken->valid();
    }
}