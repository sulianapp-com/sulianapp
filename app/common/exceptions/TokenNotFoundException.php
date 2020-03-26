<?php

namespace app\common\exceptions;

class TokenNotFoundException extends AppException
{
    public function __construct($message = "", $data = [], $redirect = '')
    {
        $message = $message ?: 'token不存在';
        parent::__construct($message, $data, $redirect);
    }
}