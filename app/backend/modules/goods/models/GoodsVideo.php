<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/12
 * Time: 9:23
 */

namespace app\backend\modules\goods\models;

use app\common\services\Utils;


/**
 * Class GoodsVideo
 * @package app\backend\modules\goods\models
 */
class GoodsVideo extends \app\common\models\goods\GoodsVideo
{
//     public static function relationValidator($goodsId, $data, $operate)
//     {
//         return true;
//     }

    public static function relationSave($goodsId, $data, $operate = '')
    {
        if (!$goodsId) {
            return false;
        }
        if (!$data) {
            return false;
        }

        $model = self::getThis($goodsId, $operate);

        //判断deleted
        if ($operate == 'deleted') {
            return $model->delete();
        }
        $attr['goods_id'] = $goodsId;
        //商品视频地址
        $attr['goods_video'] = $data['goods_video'];

        $attr['video_image'] = $data['video_image'];


        $model->setRawAttributes($attr);

        return $model->save();
    }

    public static function getThis($goodsId, $operate)
    {
        $model = false;
        if ($operate != 'created') {
            $model = static::where(['goods_id' => $goodsId])->first();
        }
        !$model && $model = new static;

        return $model;
    }

    /**
     * 无法使用 exec 已被禁用
     */
    public function test($data)
    {

        if ($data['goods_video']) {
            $path = storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'goods'.DIRECTORY_SEPARATOR.'video-image'.DIRECTORY_SEPARATOR.\YunShop::app()->uniacid.DIRECTORY_SEPARATOR.date('Y', time()).DIRECTORY_SEPARATOR.date('m', time()));

            Utils::mkdirs($path);

            $file_path = self::getFile($path);

            $command = 'ffmpeg -i '.$data['goods_video'].' -y -f image2 -t 0.003 -s 352x240 '.$file_path;

            exec($command, $output,$return_val);

            if ($return_val !== 0) {
                $data['status'] = 1;
            } else {
                $data['video_image'] = substr($file_path, strpos($file_path, 'app'));
            }
        }
    }

    public static  function getFile($path)
    {
        $str = str_replace('.', '-', uniqid('YZ',true));

        return $path.DIRECTORY_SEPARATOR.$str.'.jpg';

    }
}