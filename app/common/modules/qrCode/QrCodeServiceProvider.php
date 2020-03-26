<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/22
 * Time: 3:48 PM
 */

namespace app\common\modules\qrCode;


class QrCodeServiceProvider extends \SimpleSoftwareIO\QrCode\QrCodeServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind('qrcode', function () {
            return new QrCodeGenerator();
        });
    }
}
