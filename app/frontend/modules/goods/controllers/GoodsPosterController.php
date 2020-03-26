<?php
/**
 * Created
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2018/1/24
 * Time: 下午1:43
 */

namespace app\frontend\modules\goods\controllers;


use app\common\components\ApiController;
use app\common\models\Goods;
use app\common\models\Store;
use Setting;
use app\common\services\Utils;

/**
 * 商品海报
 */
class GoodsPosterController extends ApiController
{

    private $shopSet;
    private $goodsModel;
    private $storeid;
    private $hotel_id;
    private $mid;
    private $type;
    private $ingress;
    private $shop_id;
    //画布大小
    // private $canvas = [
    //     'width' => 600,
    //     'height' => 1000,
    // ];

    private $shopText = [
        'left'      => 50,
        'top'       => 45,
        'type'      => 1,
        'size'      => 30,
        'max_width' => 500,
        'br'        => true,
    ];

    private $goodsText = [
        'left'      => 30,
        'top'       => 810,
        'type'      => 1,
        'size'      => 24,
        'max_width' => 360,
        'br'        => false,
    ];

    /**
     * 字体路径
     *
     * @var string
     */
    private $fontPath;


    public function generateGoodsPoster()
    {
        $this->fontPath = $this->defaultFontPath();

        $id = intval(\YunShop::request()->id);

        $this->mid = \YunShop::app()->getMemberId();

        $this->storeid = intval(\YunShop::request()->storeid);
        $this->hotel_id = intval(\YunShop::request()->hotel_id);
        $this->shop_id = intval(\YunShop::request()->shop_id);
        $this->type = intval(\YunShop::request()->type);
        $this->ingress = \YunShop::request()->ingress ?: '';
        if (!$id) {
            return $this->errorJson('请传入正确参数.');
        }

        if (empty($this->storeid)) {

            $this->shopSet = \Setting::get('shop.shop');
        } else {

            if (app('plugins')->isEnabled('store-cashier')) {

                $store = \app\common\models\Store::find($this->storeid);
                $this->shopSet['name'] = $store->store_name;
                $this->shopSet['logo'] = $store->thumb;
            }
            if (app('plugins')->isEnabled('hotel')) {

                $hotel = \Yunshop\Hotel\common\models\Hotel::find($this->hotel_id);
                $this->shopSet['name'] = $hotel->hotel_name;
                $this->shopSet['logo'] = $hotel->thumb;
            }
        }
        if ($this->type == 2 && $this->ingress == 'weChatApplet') {
            if (!app('plugins')->isEnabled('min-app')) {
                return $this->errorJson('未开启小程序插件');
            }
        }
        //$this->goodsModel = Goods::uniacid()->with('hasOneShare')->where('plugin_id', 0)->where('status', 1)->find($id);
        $goods_model = \app\common\modules\shop\ShopConfig::current()->get('goods.models.commodity_classification');
        $goods_model = new $goods_model;
        $this->goodsModel = $goods_model->uniacid()->with('hasOneShare')->where('status', 1)->find($id);

        if (empty($this->goodsModel)) {
            return $this->errorJson('该商品不存在');
        }

        $imgPath = $this->get_lt();

        if (config('app.framework') == 'platform') {
            $urlPath = request()->getSchemeAndHttpHost() . '/' . substr($imgPath, strpos($imgPath, 'storage'));
        } else {
            $urlPath = request()->getSchemeAndHttpHost() . '/' . substr($imgPath, strpos($imgPath, 'addons'));
        }

        //return $this->successJson('ok', $urlPath);

        $data = $this->base64EncodeImage($imgPath);
        $data['image_url'] = $urlPath;

        return $this->successJson('ok', $data);

    }


    /**
     * 圆角图片
     * @param $radius 角度
     * @return img
     */
    public function get_lt_rounder_corner($radius)
    {
        $img = imagecreatetruecolor($radius, $radius);  // 创建一个正方形的图像
        $bgcolor = imagecolorallocate($img, 1, 1, 1);   // 图像的背景
        $fgcolor = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $bgcolor);
        // $radius,$radius：以图像的右下角开始画弧  
        // $radius*2, $radius*2：已宽度、高度画弧  
        // 180, 270：指定了角度的起始和结束点  
        // fgcolor：指定颜色  
        imagefilledarc($img, $radius, $radius, $radius * 2, $radius * 2, 180, 270, $fgcolor, IMG_ARC_PIE);
        // 将弧角图片的颜色设置为透明  
        imagecolortransparent($img, $fgcolor);
        return $img;
    }

    public function roundRadius($resource, $image_width, $image_height, $radius = 8)
    {
        // lt(左上角)  
        $lt_corner = $this->get_lt_rounder_corner($radius);

        // header('Content-Type: image/png');  
        // imagepng($lt_corner);  
        // exit;  
        imagecopymerge($resource, $lt_corner, 0, 0, 0, 0, $radius, $radius, 100);
        // lb(左下角)  
        $lb_corner = imagerotate($lt_corner, 90, 0);
        imagecopymerge($resource, $lb_corner, 0, $image_height - $radius, 0, 0, $radius, $radius, 100);
        // rb(右上角)  
        $rb_corner = imagerotate($lt_corner, 180, 0);
        imagecopymerge($resource, $rb_corner, $image_width - $radius, $image_height - $radius, 0, 0, $radius, $radius, 100);
        // rt(右下角)  
        $rt_corner = imagerotate($lt_corner, 270, 0);
        imagecopymerge($resource, $rt_corner, $image_width - $radius, 0, 0, 0, $radius, $radius, 100);

        // header('Content-Type: image/png');  
        // imagepng($resource);  
        // exit;  
        return $resource;
    }

    //图片返回base64 格式
    public function base64EncodeImage($strTmpName)
    {
        $base64Image = '';
        $imageInfo = getimagesize($strTmpName);
        //$imageData   = fread(fopen($strTmpName , 'r'), filesize($strTmpName));
        //$base64Image = 'data:' . $imageInfo['mime'] . ';base64,' . chunk_split(base64_encode($imageData));
        $base64Image = base64_encode(file_get_contents($strTmpName));
        return [
            'mime'        => $imageInfo['mime'],
            'base64Image' => $base64Image,
        ];
    }

    /**
     * 生成商品海报
     * @return string 商品海报绝对路径
     */
    public function get_lt()
    {

        set_time_limit(0);
        @ini_set('memory_limit', '256M');

        $image_width = 600; //335
        $image_height = 1000; //485

        $target = imagecreatetruecolor($image_width, $image_height);
        $white = imagecolorallocate($target, 255, 255, 255);
        $color = imagecolorallocate($target, 226, 226, 226);
        //设置白色背景色
        imagefill($target, 0, 0, $white);
        //设置线条
        //imageline($target, 0, 100, $image_width, 100, $color);

        $target = $this->roundRadius($target, $image_width, $image_height);

        $target = $this->createShopImage($target);

        if ($this->goodsModel->hasOneShare->share_thumb) {
            $goodsThumb = $this->goodsModel->hasOneShare->share_thumb;
        } else {
            $goodsThumb = $this->goodsModel->thumb;
        }

        $target = $this->mergeGoodsImage($target, $goodsThumb);

        //商品二维码
        $goodsQr = $this->generateQr();

        if ($this->goodsModel->hasOneShare->share_title) {
            $text = $this->goodsModel->hasOneShare->share_title;
        } else {
            $text = $this->goodsModel->title;
        }

        $target = $this->mergeQrImage($target, $goodsQr);

        $target = $this->mergeText($target, $this->goodsText, $text);

        $target = $this->mergePriceText($target);

        imagepng($target, $this->getGoodsPosterPath());

        imagedestroy($target);

        return $this->getGoodsPosterPath();

    }

    //商城logo 与 商城名称处理
    protected function createShopImage($target)
    {

        //计算商城名称的宽度
        $testbox = imagettfbbox($this->shopText['size'], 0, $this->fontPath, $this->shopSet['name']);
        $shopTextWidth = $testbox[2] > 500 ? 500 : $testbox[2];


        $image_width = $shopTextWidth + 50;
        $image_height = 60;
        $img = imagecreatetruecolor($image_width, $image_height);
        $white = imagecolorallocate($img, 255, 255, 255);
        //设置白色背景色
        imagefill($img, 0, 0, $white);

        $img = $this->mergeLogoImage($img);
        $img = $this->mergeText($img, $this->shopText, $this->shopSet['name']);

        imagecopyresized($target, $img, (600 - $image_width) / 2, 20, 0, 0, $image_width, $image_height, imagesx($img), imagesy($img));
        imagedestroy($img);

        return $target;

    }

    private function getGoodsPosterPath()
    {
        $path = storage_path('app/public/goods/' . \YunShop::app()->uniacid) . "/";

        Utils::mkdirs($path);

        $file_name = \YunShop::app()->uniacid . '-' . \YunShop::app()->getMemberId() . '-' . $this->goodsModel->id . '.png';

        return $path . $file_name;
    }

    /**
     * 合并商品图片到 $target
     * @param $target
     * @param $img
     * @return mixed
     */
    private function mergeGoodsImage($target, $thumb)
    {
        $thumb = $this->HttpAgreement(yz_tomedia($thumb));
        $img = imagecreatefromstring(\Curl::to($thumb)->get());
        $width = imagesx($img);
        $height = imagesy($img);

        imagecopyresized($target, $img, 30, 180, 0, 0, 540, 540, $width, $height);
        imagedestroy($img);


        return $target;
    }

    /**
     * 合并商城Logo 到 $target
     * @param [type] $target [description]
     * @param [type] $img    [description]
     */
    private function mergeLogoImage($target)
    {
        $logo = $this->HttpAgreement(yz_tomedia($this->shopSet['logo']));

        $img = imagecreatefromstring(\Curl::to($logo)->get());
        $width = imagesx($img);
        $height = imagesy($img);
        imagecopyresized($target, $img, 0, 5, 0, 0, 50, 50, $width, $height);
        imagedestroy($img);

        return $target;
    }

    /**
     * 合并商品二维码 到 $target
     * @param [type] $target [description]
     * @param [type] $img    [description]
     */
    private function mergeQrImage($target, $img, $dst_x = 400, $dst_y = 750)
    {
        $width = imagesx($img);
        $height = imagesy($img);
        if ($this->type == 2 && $this->ingress == 'weChatApplet') {
            imagecopyresized($target, $img, 370, $dst_y, 0, 0, 200, 200, $width, $height);
        } else {
            imagecopy($target, $img, $dst_x, $dst_y, 0, 0, $width, $height);
        }
        // imagecopy($target, $img, $dst_x, $dst_y, 0, 0, $width, $height);
        imagedestroy($img);

        return $target;
    }

    /**
     * 合并名称
     * @param $target
     * @param $params
     * @param $text
     * @return mixed
     */
    private function mergeText($target, $params, $text)
    {
        if ($params['type']) {
            $text = $this->autowrap($params['size'], 0, $this->fontPath, $text, $params['max_width'], $params['br']);
        }

        $black = imagecolorallocate($target, 51, 51, 51);//文字颜色
        imagettftext($target, $params['size'], 0, $params['left'], $params['top'], $black, $this->fontPath, $text);

        return $target;
    }

    /**
     * 合并商品价格
     * @return [type] [description]
     */
    private function mergePriceText($target)
    {

        $color = imagecolorallocate($target, 107, 107, 107);
        $this->goodsModel->vip_level_status;
        $price_display = '';
        if ($this->goodsModel->vip_level_status['status']){
            $price_display = $this->goodsModel->vip_level_status['word'];
        }else{
            $price_display = $this->goodsModel->price;
        }
        //$price = '现价:￥' .$price_display;// $this->goodsModel->price;
        $price = '￥' .$price_display;// $this->goodsModel->price;
        $market_price = '原价:￥' . $this->goodsModel->market_price;
        $black = imagecolorallocate($target, 241, 83, 83);//当前价格颜色

        $price_box = imagettfbbox(18, 0, $this->fontPath, $price);
        $market_price_box = imagettfbbox(24, 0, $this->fontPath, $market_price);
        $gray = imagecolorallocate($target, 107, 107, 107);//原价颜色

        //设置删除线条
        // imageline($target, $price_box[2] + 12, 900, $price_box[2]+$market_price_box[2] + 14, 900, $color);

        imagettftext($target, 24, 0, 30, 755, $black, $this->fontPath, $price);
        imagettftext($target, 19, 0, 180, 755, $gray, $this->fontPath, $market_price);

        return $target;

    }

    /**
     * 生成商品二维码
     * @return [type] [description]
     */

    private function generateQr()
    {
        if ($this->type == 2 && $this->ingress == 'weChatApplet') {
            //小程序海报生成
            $url = "pages/detail_v2/detail_v2";
            $img = $this->getWxacode($url);
            return $img;
        }
        if (empty($this->storeid)) {
            //商城商品二维码
            //微店商品二维码
            if ($this->shop_id) {
                $url = yzAppFullUrl('/goods/' . $this->goodsModel->id, ['mid' => $this->mid,'shop_id'=>$this->shop_id]);
                $file = 'shop-mid-' . $this->mid . '-shop_id-'.$this->shop_id. '-goods-' . $this->goodsModel->id . '.png';
            } else {
                $url = yzAppFullUrl('/goods/' . $this->goodsModel->id, ['mid' => $this->mid]);
                $file = 'shop-mid-' . $this->mid . '-goods-' . $this->goodsModel->id . '.png';
            }

        } else {
            //门店商品二维码
            $url = yzAppFullUrl('/goods/' . $this->goodsModel->id . '/o2o/' . $this->storeid, ['mid' => $this->mid]);

            $file = 'store-' . $this->storeid . '-mid-' . $this->mid . '-goods-' . $this->goodsModel->id . '.png';
        }

        $path = storage_path('app/public/goods/qrcode/' . \YunShop::app()->uniacid);

        Utils::mkdirs($path);


        if (!is_file($path . '/' . $file)) {

            \QrCode::format('png')->size(200)->generate($url, $path . '/' . $file);

        }
        $img = imagecreatefromstring(file_get_contents($path . '/' . $file));
        // unlink($path.'/'.$file);

        return $img;
    }


    /**
     * 字体换行
     * @param  [int] $fontsize [字体大小]
     * @param  [int] $angle    [角度]
     * @param  [string] $fontface [字体类型]
     * @param  [string] $string   [字符串]
     * @param  [int] $width    [预设宽度]
     * @param  [int] $br    [大于$width是否换行]
     * @return [string]           [处理好的字符串]
     */
    private function autowrap($fontsize, $angle, $fontface, $string, $width, $br)
    {
        $content = "";
        $num = 0;
        // 将字符串拆分成一个个单字 保存到数组 letter 中
        for ($i = 0; $i < mb_strlen($string); $i++) {
            $letter[] = mb_substr($string, $i, 1);
        }
        foreach ($letter as $l) {
            $teststr = $content . " " . $l;
            $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
            // 判断拼接后的字符串是否超过预设的宽度
            if (($testbox[2] > $width) && ($content !== "")) {
                $num += 1;
                if ($num > 1 || $br) {
                    $content .= '..';
                    // dd($content);
                    return $content;
                }
                $content .= "\n";
            }
            $content .= $l;
        }
        return $content;
    }

    /**
     * 补全http协议
     * @param [string] $src 图片地址
     * @return [string]
     */
    protected function HttpAgreement($src)
    {
        $t = strtolower($src);
        if (strexists($t, 'http://') || strexists($t, 'https://')) {
            return $src;
        }
        $src = 'https://' . ltrim($src, '//');

        return $src;
    }

    //生成小程序二维码
    function getWxacode($goods_url)
    {
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?";
        $token = $this->getToken();
        \Log::debug('===========access_token===========', $token);
        $url .= "access_token=" . $token;
        $postdata = [
            "scene" => 'id=' . $this->goodsModel->id . ',mid=' . \YunShop::app()->getMemberId(),
            "page"  => $goods_url,
        ];
        $path = storage_path('app/public/goods/qrcode/' . \YunShop::app()->uniacid);
        if (!is_dir($path)) {
            Utils::mkdirs($path);
        }
        \Log::debug('=====地址信息=======', $postdata);
        $res = $this->curl_post($url, json_encode($postdata), $options = array());
        $erroe = json_decode($res);
        if (isset($erroe->errcode)) {
            return $this->errorJson('错误码' . $erroe->errcode . ';错误信息' . $erroe->errmsg);
        }
        \Log::debug('===========生成二维码===========', $res);
        $file = 'mid-' . $this->mid . '-goods-' . $this->goodsModel->id . '.png';
        file_put_contents($path . '/' . $file, $res);
        $img = imagecreatefromstring(file_get_contents($path . '/' . $file));
        return $img;
    }


    //发送获取token请求,获取token(2小时)
    public function getToken()
    {
        $url = $this->getTokenUrlStr();
        $res = $this->curl_post($url, $postdata = '', $options = array());

        $data = json_decode($res, JSON_FORCE_OBJECT);
        return $data['access_token'];
    }

    //获取token的url参数拼接
    public function getTokenUrlStr()
    {
        $set = Setting::get('plugin.min_app');
        $getTokenUrl = "https://api.weixin.qq.com/cgi-bin/token?"; //获取token的url
        $WXappid = $set['key']; //APPID
        $WXsecret = $set['secret']; //secret
        $str = $getTokenUrl;
        $str .= "grant_type=client_credential&";
        $str .= "appid=" . $WXappid . "&";
        $str .= "secret=" . $WXsecret;
        return $str;


    }

    public function curl_post($url = '', $postdata = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 默认字体路径
     *
     * @return string
     */
    private function defaultFontPath()
    {
        return base_path() . DIRECTORY_SEPARATOR . "static" . DIRECTORY_SEPARATOR . "fonts" . DIRECTORY_SEPARATOR . "source_han_sans.ttf";
    }
}
