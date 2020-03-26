<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/22
 * Time: 3:50 PM
 */

namespace app\common\modules\qrCode;

use app\common\exceptions\ShopException;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;

/**
 * 解决了下面的问题
 *  临时二维码生成慢
 *  自动生成目录
 *  返回url全路径和文件全路径
 * Class QrCodeGenerator
 * @package app\common\modules\qrCode
 */
class QrCodeGenerator extends BaconQrCodeGenerator
{
    /**
     * 从缓存中读取二维码内容
     * @param $text
     * @return mixed
     */
    public function cache($text,$time = 10080)
    {
        // 以内容的md5作为key名
        $key = md5($text.'v=1');
        if (!\Cache::has('qrcode' . '/' . $key)) {
            // 二维码内容在缓存中保存一星期
            \Cache::put('qrcode' . '/' . $key, $this->generate($text), $time);
        }
        return \Cache::get('qrcode' . '/' . $key);
    }

    /**
     * 返回二维码url和文件全路径(不存在则生成)
     * @param $text
     * @param string $path
     * @param bool $force  强制重新生成
     * @return array
     * @throws ShopException
     */
    public function get($text, $path = 'app/public/qr',$force = false)
    {
        $name = md5($text.'&salt=2');
        if (!is_dir(storage_path($path))) {
            self::directory(storage_path($path));
            mkdir(storage_path($path), 0777);
        }
        if (!is_dir(storage_path($path))) {
            throw new ShopException('生成二维码目录失败');
        }

        if (!file_exists(storage_path($path . "/{$name}")) || $force) {
            unlink(storage_path($path . "/{$name}"));
            // 注意:format方法必须先调用,否则后续方法不生效
            $this->format('png')->size(240)->generate($text, storage_path($path . "/{$name}"));
        }

        if (!file_exists(storage_path($path . "/{$name}"))) {
            throw new ShopException('生成二维码失败');
        }

        if (config('app.framework') == 'platform') {
            $urlPath = '';
        } else {
            $urlPath = '/addons/yun_shop';
        }

        return [
            'path' => $path . DIRECTORY_SEPARATOR . $name,
            'url' => request()->getSchemeAndHttpHost() . $urlPath . \Storage::url($path . DIRECTORY_SEPARATOR . $name)
        ];
    }

    /**
     * 递归生成文件夹
     * @param $dir
     * @return bool
     */
    private function directory($dir)
    {
        return is_dir($dir) or self::directory(dirname($dir)) and mkdir($dir, 0777);

    }
}