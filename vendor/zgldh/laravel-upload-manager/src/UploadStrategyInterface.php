<?php namespace zgldh\UploadManager;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2015/7/23
 * Time: 16:50
 */


interface UploadStrategyInterface
{
    public function makeFileName($file);
    public function makeStorePath($filename);
}