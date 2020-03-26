<?php

namespace app\common\exceptions;

class TokenHasRevokedException extends AppException
{
    public function __construct($message = "", $data = [], $redirect = '')
    {
        $message = $message ?: 'token已失效';
        parent::__construct($message, $data, $redirect);
    }
}