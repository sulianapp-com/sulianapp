<?php namespace zgldh\UploadManager;

use SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Created by PhpStorm.
 * User: zgldh
 * Date: 2015/7/23
 * Time: 16:50
 */
class UploadStrategy implements UploadStrategyInterface
{
    /**
     * 生成文件名
     * @param $file UploadedFile|SplFileInfo|string
     * @return string
     */
    public function makeFileName($file)
    {
        if (is_a($file, UploadedFile::class)) {
            $filename = date('Y-m-d-') . md5(md5_file($file->getRealPath()) . time()) . '.' . $file->getClientOriginalExtension();
        } elseif (is_a($file, SplFileInfo::class)) {
            $filename = date('Y-m-d-') . md5(md5_file($file->getRealPath()) . time()) . '.' . $file->getExtension();
        } elseif (is_string($file)) {
            $extension = \File::extension($file);
            $filename = date('Y-m-d-') . md5($file . time()) . '.' . $extension;
        } else {
            throw new \RuntimeException(__METHOD__ . ' needs a UploadedFile|SplFileInfo|string instance or a file path string');
        }
        return $filename;
    }

    /**
     * 生成储存的相对路径
     * @param $filename
     * @return string
     */
    public function makeStorePath($filename)
    {
        $path = 'uploads/' . $filename;
        return $path;
    }
}