<?php

namespace app\common\models;


use app\common\exceptions\TokenHasExpiredException;
use app\common\exceptions\TokenHasRevokedException;
use app\common\exceptions\TokenNotFoundException;
use Carbon\Carbon;

/**
 * Class ErpApiRefreshToken
 * @package Yunshop\ErpApi\common\models
 * @property bool revoked
 * @property Carbon expires_at
 * @property string refresh_token
 * @property int uniacid
 * @property int id
 */
class ApiRefreshToken extends BaseModel
{
    public $table = 'yz_api_refresh_token';
    public $timestamps = true;
    protected $guarded = [''];

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
        $refreshToken = self::where([
            'uniacid' => $uniacid,
            'refresh_token' => $token,
        ])->first();
        if (!$refreshToken) {
            throw new TokenNotFoundException();
        }
        return $refreshToken->valid();
    }

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
}