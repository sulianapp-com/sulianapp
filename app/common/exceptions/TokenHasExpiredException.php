<?php

namespace app\common\exceptions;

class TokenHasExpiredException extends AppException
{
    public function __construct($message = "", $data = [], $redirect = '')
    {
        $message = $message ?: 'token已过期';
        parent::__construct($message, $data, $redirect);
    }
}