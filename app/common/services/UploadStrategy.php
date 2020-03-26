<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 29/03/2017
 * Time: 17:01
 */

namespace app\common\services;


use zgldh\UploadManager\UploadStrategy as BaseUploadStrategy;
use zgldh\UploadManager\UploadStrategyInterface;

class UploadStrategy extends BaseUploadStrategy implements UploadStrategyInterface
{


    /**
     * 得到 disk local 内上传的文件的URL
     * @param $path
     * @return string
     */
    public function getLocalUrl($path)
    {
        $url = \Storage::url( $path);
        return $url;
    }

    public function getPublicUrl($path)
    {
        $url = \Storage::url( 'app/public/'.$path);
        return $url;
    }

    public function getImageUrl($path)
    {
        $url = \URL::route('image.preview',[$path]);
        return $url;
    }

    public function makeStorePath($filename)
    {
        $path =  $filename;
        return $path;
    }

    /**
     * 得到 disk qiniu 内上传的文件的URL
     * @param $path
     * @return string
     */
    public function getQiniuUrl($path)
    {
        $url = 'http://' . trim(\Config::get('filesystems.disks.qiniu.domain'), '/') . '/' . trim($path, '/');
        return $url;
    }
}