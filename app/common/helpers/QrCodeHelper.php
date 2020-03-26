<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/9/26
 * Time: 上午11:55
 */

namespace app\common\helpers;


use app\common\exceptions\ShopException;

class QrCodeHelper
{
    private $patch;
    private $url;
    private $fileName;

    /**
     * QrCodeHelper constructor.
     * @param $url
     * @param $patch
     * @throws ShopException
     */
    function __construct($url, $patch)
    {
        $this->patch = $patch;
        $this->url = $url;
        $this->fileName = $this->getFileName();
    }

    public function filePath()
    {
        return "$this->patch/{$this->fileName}.png";
    }

    public function url()
    {
        return request()->getSchemeAndHttpHost() . config('app.webPath') . \Storage::url($this->patch . "/{$this->fileName}.png");
    }

    /**
     * @return string
     * @throws ShopException
     */
    private function getFileName()
    {

        $name = md5($this->url);
        if (!is_dir(storage_path($this->patch))) {
            self::directory(storage_path($this->patch));
            mkdir(storage_path($this->patch), 0777);
        }
        if (!is_dir(storage_path($this->patch))) {
            throw new ShopException('生成二维码目录失败');
        }

        if (!file_exists(storage_path($this->patch . "/{$name}.png")) || request()->input('new')) {
            unlink(storage_path($this->patch . "/{$name}.png"));
            // 注意:format方法必须先调用,否则后续方法不生效

            \QrCode::format('png')->size(240)->generate($this->url, storage_path($this->patch . "/{$name}.png"));
        }
        if (!file_exists(storage_path($this->patch . "/{$name}.png"))) {
            throw new ShopException('生成二维码失败');
        }
        return $name;
    }

    private function directory($dir)
    {

        return is_dir($dir) or self::directory(dirname($dir)) and mkdir($dir, 0777);

    }
}