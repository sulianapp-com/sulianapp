<?php namespace zgldh\UploadManager;

use RuntimeException;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2015/8/12
 * Time: 16:50
 */
class UploadException extends RuntimeException
{
    public $errors = null;

    public function __construct($errors)
    {
        $this->errors = $errors;
    }
}