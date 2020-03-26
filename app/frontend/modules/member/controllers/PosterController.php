<?php

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\facades\Setting;
use app\common\helpers\ImageHelper;
use app\common\services\Utils;
use app\frontend\models\Member;
use app\frontend\modules\member\models\MemberModel;
use Yunshop\Poster\models\Poster;
use Yunshop\Poster\models\PosterRecord;
use Yunshop\Poster\services\CreatePosterService;

class PosterController extends ApiController
{
    public function index()
    {
        $this->type = intval(\YunShop::request()->type);
        $imageUrl = $this->getPoster();
        $this->exist_poster($imageUrl);
        return $this->successJson('成功', ['image_url' => $imageUrl]);
    }

    //判断会员生成的海报存不存在
    private function exist_poster($imageUrl)
    {
        //如果会员第一次生成海报就写入记录表
        $posterModel = Poster::uniacid()->select('id', 'is_open')->where('center_show', 1)->first();
        if(PosterRecord::where('url',$imageUrl)->get()->isEmpty() && !empty($imageUrl) && $posterModel){
            $posterRecord = new PosterRecord();
            $posterRecord->url = $imageUrl;
            $posterRecord->poster_id =  $posterModel->id;
            $posterRecord->member_id =\YunShop::app()->getMemberId();
            $posterRecord->created_at = time();
            $posterRecord->save();
        }

    }
    /**
     * 会员中心推广二维码(包含会员是否有生成海报权限)
     *
     * @param $isAgent
     *
     * @return string
     */
    private function getPoster()
    {
        if (\YunShop::plugin()->get('poster')) {
            if (\Schema::hasColumn('yz_poster', 'center_show')) {
                $posterModel = Poster::uniacid()->select('id', 'is_open')->where('center_show', 1)->first();
                if (($posterModel && $posterModel->is_open) || ($posterModel && !$posterModel->is_open && Member::current()->yzMember->is_agent)) {
                    $file_path = (new CreatePosterService(\YunShop::app()->getMemberId(), $posterModel->id, request('type')))->getMemberPosterPath();
                    if (!$file_path) {
                        return '';
                    }
                    return ImageHelper::getImageUrl($file_path);
                    //return request()->getSchemeAndHttpHost() . '/' . config('app.webPath') . $file_path;
                }
            }
        }
        return $this->createPoster();
    }


    private function createPoster()
    {

        $width = 320;
        $height = 540;

        $logo_width = 40;
        $logo_height = 40;

        $font_size = 15;
        $font_size_show = 20;

        $member_id = \YunShop::app()->getMemberId();

        $shopInfo = Setting::get('shop.shop');
        $shopName = $shopInfo['name'] ?: '商城'; //todo 默认值需要更新
        $shopLogo = $shopInfo['logo'] ? replace_yunshop(yz_tomedia($shopInfo['logo'])) : base_path() . '/static/images/logo.png'; //todo 默认值需要更新
        $shopImg = $shopInfo['signimg'] ? replace_yunshop(yz_tomedia($shopInfo['signimg'])) : base_path() . '/static/images/photo-mr.jpg'; //todo 默认值需要更新

        $str_lenght = $logo_width + $font_size_show * mb_strlen($shopName);

        $space = ($width - $str_lenght) / 2;

        $uniacid = \YunShop::app()->uniacid;
        $path = storage_path('app/public/personalposter/' . $uniacid);

        Utils::mkdirs($path);

        $md5 = md5($member_id . $shopInfo['name'] . $shopInfo['logo'] . $shopInfo['signimg'] . $this->type . '2'); //用于标识组成元素是否有变化
        $extend = '.png';
        $file = $md5 . $extend;
        \Log::debug('+++++++++++++ 111 +++++++++++');
        if (!file_exists($path . '/' . $file)) {
            $targetImg = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($targetImg, 255, 255, 255);
            imagefill($targetImg, 0, 0, $white);

            $imgSource = imagecreatefromstring(\Curl::to($shopImg)->get());
            $logoSource = imagecreatefromstring(\Curl::to($shopLogo)->get());
            if (2 == $this->type and request()->input('ingress') == 'weChatApplet') {
                $qrcode = MemberModel::getWxacode();
                $qrSource = imagecreatefromstring(\Curl::to($qrcode)->get());
            } else {
                $qrcode = MemberModel::getAgentQR();
                $qrSource = imagecreatefromstring(\Curl::to($qrcode)->get());
            }
            $fingerPrintImg = imagecreatefromstring(file_get_contents($this->getImgUrl('ewm.png')));
            $mergeData = [
                'dst_left' => $space,
                'dst_top' => 10,
                'dst_width' => $logo_width,
                'dst_height' => $logo_height,
            ];
            self::mergeImage($targetImg, $logoSource, $mergeData); //合并商城logo图片
            $mergeData = [
                'size' => $font_size,
                'left' => $space + $logo_width + 10,
                'top' => 37,
            ];
            self::mergeText($targetImg, $shopName, $mergeData);//合并商城名称(文字)
            $mergeData = [
                'dst_left' => 0,
                'dst_top' => 60,
                'dst_width' => 320,
                'dst_height' => 320,
            ];
            self::mergeImage($targetImg, $imgSource, $mergeData); //合并商城海报图片
            $mergeData = [
                'dst_left' => 0,
                'dst_top' => 380,
                'dst_width' => 160,
                'dst_height' => 160,
            ];
            self::mergeImage($targetImg, $fingerPrintImg, $mergeData); //合并指纹图片
            if ($this->type == 2) {
                $mergeData = [
                    'dst_left' => 180,
                    'dst_top' => 390,
                    'dst_width' => 120,
                    'dst_height' => 120,
                ];
            } else {
                $mergeData = [
                    'dst_left' => 160,
                    'dst_top' => 380,
                    'dst_width' => 160,
                    'dst_height' => 160,
                ];
            }
            self::mergeImage($targetImg, $qrSource, $mergeData); //合并二维码图片

            header("Content-Type: image/png");
            $imgPath = $path . "/" . $file;
            imagepng($targetImg, $imgPath);
        }

        $file = $path . '/' . $file;

        $imgUrl = ImageHelper::getImageUrl($file);
        \Log::debug('0000000000000000000000000000000000', $imgUrl);
        return $imgUrl;
    }

    //合并图片并指定图片大小
    private static function mergeImage($destinationImg, $sourceImg, $data)
    {
        $w = imagesx($sourceImg);
        $h = imagesy($sourceImg);
        imagecopyresized($destinationImg, $sourceImg, $data['dst_left'], $data['dst_top'], 0, 0, $data['dst_width'],
            $data['dst_height'], $w, $h);
        imagedestroy($sourceImg);
        return $destinationImg;
    }

    //合并字符串
    private static function mergeText($destinationImg, $text, $data)
    {
        putenv('GDFONTPATH=' . base_path('static/fonts'));
        $font = "source_han_sans";

        $black = imagecolorallocate($destinationImg, 0, 0, 0);
        imagettftext($destinationImg, $data['size'], 0, $data['left'], $data['top'], $black, $font, $text);
        return $destinationImg;
    }

    private function getImgUrl($file){
        if (config('app.framework') == 'platform') {
            return request()->getSchemeAndHttpHost().'/addons/yun_shop/static/app/images/'.$file;
        } else {
            return base_path() . '/static/app/images/'.$file;
        }
    }
}