<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-07-19
 * Time: 13:57
 */

namespace app\frontend\controllers;


use app\common\components\BaseController;
use app\common\services\ImageZip;
use app\platform\modules\system\models\SystemSetting;

class UploadController extends BaseController
{
    public function uploadPic()
    {
        $attach = request()->attach;
        $file = request()->file('file');

        if (!$file) {
            return $this->errorJson('请传入正确参数.');
        }

        if (!$file->isValid()) {
            return $this->errorJson('上传失败.');
        }

        if ($file->getClientSize() > 30*1024*1024) {
            return $this->errorJson('图片过大.');
        }

        $defaultImgType = [
            'jpg', 'bmp', 'eps', 'gif', 'mif', 'miff', 'png', 'tif',
            'tiff', 'svg', 'wmf', 'jpe', 'jpeg', 'dib', 'ico', 'tga', 'cut', 'pic'
        ];

        // 获取文件相关信息
        $originalName = $file->getClientOriginalName(); // 文件原名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        $ext = $file->getClientOriginalExtension(); //文件后缀

        if (!$ext) {
            $ext = 'jpg';
        }
        $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

        if (config('app.framework') == 'platform') {
            $setting = SystemSetting::settingLoad('global', 'system_global');

            $remote = SystemSetting::settingLoad('remote', 'system_remote');

            if (in_array($ext, $defaultImgType)) {
                if ($setting['image_extentions'] && !in_array($ext, array_filter($setting['image_extentions'])) ) {
                    return $this->errorJson('非规定类型的文件格式');
                }
                $defaultImgSize = $setting['img_size'] ? $setting['img_size'] * 1024 : 1024*1024*5; //默认大小为5M
                if ($file->getClientSize() > $defaultImgSize) {
                    return $this->errorJson('文件大小超出规定值');
                }
            }

            if ($setting['image']['zip_percentage']) {
                //执行图片压缩
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($newOriginalName),
                    yz_tomedia($newOriginalName),
                    $setting['image']['zip_percentage']
                );
            }

            if ($setting['thumb_width'] == 1 && $setting['thumb_width']) {
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($newOriginalName),
                    yz_tomedia($newOriginalName),
                    $setting['thumb_width']
                );
            }
        } else {
            //全局配置
            global $_W;

            //公众号独立配置信息 优先使用公众号独立配置
            $uni_setting = app('WqUniSetting')->get()->toArray();
            if (!empty($uni_setting['remote']) && iunserializer($uni_setting['remote'])['type'] != 0) {
                $setting['remote'] = iunserializer($uni_setting['remote']);
                $remote = $setting['remote'];
                $upload = $_W['setting']['upload'];
            } else {
                $remote = $_W['setting']['remote'];
                $upload = $_W['setting']['upload'];
            }

            if (in_array($ext, $defaultImgType)) {
                if ($upload['image']['extentions'] && !in_array($ext, $upload['image']['extentions'])) {
                    return $this->errorJson('非规定类型的文件格式');
                }
                $defaultImgSize = $upload['image']['limit'] ? $upload['image']['limit'] * 1024 : 5 * 1024 * 1024;
                if ($file->getClientSize() > $defaultImgSize) {
                    return $this->errorJson('文件大小超出规定值');
                }
            }

            if ($upload['image']['zip_percentage']) {
                //执行图片压缩
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($newOriginalName),
                    yz_tomedia($newOriginalName),
                    $upload['image']['zip_percentage']
                );
            }

            if ($upload['image']['thumb'] == 1 && $upload['image']['width']) {
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($newOriginalName),
                    yz_tomedia($newOriginalName),
                    $upload['image']['width']
                );
            }
        }

        if (config('app.framework') == 'platform') {
            //本地上传
            $result = \Storage::disk('newimages')->put($newOriginalName, file_get_contents($realPath));
            if (!$result){
                return $this->successJson('上传失败');
            }
            //远程上传
            if ($remote['type'] != 0) {
                file_remote_upload_new($newOriginalName, true, $remote);
            }

            $url = \Storage::disk('newimages')->url($newOriginalName);

            return $this->successJson('上传成功', [
                'img' => $url,
                'img_url' => yz_tomedia($url),
                'attach' => $attach,
            ]);
        } else {
            //本地上传
            $result = \Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));
            if (!$result){
                return $this->successJson('上传失败');
            }
            //远程上传
            if ($remote['type'] != 0) {
                file_remote_upload_wq($newOriginalName, true, $remote, true);
            }

            $url = \Storage::disk('image')->url($newOriginalName);

            return $this->successJson('上传成功', [
                'img' => $url,
                'img_url' => yz_tomedia($url),
                'attach' => $attach,
            ]);
        }
    }
}