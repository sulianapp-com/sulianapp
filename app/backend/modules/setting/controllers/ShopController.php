<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 下午5:26
 */

namespace app\backend\modules\setting\controllers;

use app\backend\modules\goods\models\Goods;
use app\common\components\BaseController;
use app\common\helpers\Cache;
use app\common\helpers\Url;
use app\common\facades\Setting;
use app\common\models\AccountWechats;
use app\common\models\MemberLevel;
use app\common\models\notice\MessageTemp;
use app\common\modules\refund\services\RefundService;
use app\common\services\MyLink;
use app\common\services\Utils;
use Hamcrest\Core\Set;
use Mews\Captcha\Captcha;
use Yunshop\Diyform\models\DiyformTypeModel;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use app\common\models\PayType;
use app\common\models\notice\MinAppTemplateMessage;
class ShopController extends BaseController
{
    /**
     * 商城设置
     * @return mixed
     */
    public function index()
    {
        $shop = Setting::get('shop.shop');
        $level = MemberLevel::get(['id','level_name']);
        $requestModel = \YunShop::request()->shop;
        \Log::debug('data', $requestModel);
        if ($requestModel) {
            if(Cache::has('shop_setting')){
                Cache::forget('shop_setting');
            }
            $requestModel['credit']=empty($requestModel['credit'])?"余额":$requestModel['credit'];
            if (Setting::set('shop.shop', $requestModel)) {
                //PayType::updateDalance(3,$requestModel['credit'])?:$this->error("余额字样保存失败");
                return $this->message('商城设置成功', Url::absoluteWeb('setting.shop.index'));
            } else {
                $this->error('商城设置失败');
            }
        }



        return view('setting.shop.shop', [
            'set' => $shop,
            'level' => $level
        ])->render();
    }

    /**
     * 会员设置
     * @return mixed
     */
    public function member()
    {
        $member = Setting::get('shop.member');
        $shop = Setting::get('shop.shop');
        $requestModel = \YunShop::request()->member;

        if ($requestModel) {
            if(Cache::has('shop_member')){
                Cache::forget('shop_member');
            }

            if (Setting::set('shop.member', $requestModel)) {
                return $this->message('会员设置成功', Url::absoluteWeb('setting.shop.member'));
            } else {
                $this->error('会员设置失败');
            }
        }
        $is_diyform = \YunShop::plugin()->get('diyform');

        $diyForm = [];
        if($is_diyform){
            $diyForm = DiyformTypeModel::getDiyformList()->get();
            
        }
        
        return view('setting.shop.member', [
            'set' => $member,
            'shop' => $shop,
            'is_diyform' => $is_diyform,
            'diyForm' => $diyForm,
        ])->render();
    }

    /**
     * 订单设置
     * @return mixed|string
     * @throws \Throwable
     */
    public function order()
    {
        $order = Setting::get('shop.order');
        $requestModel = \YunShop::request()->order;

        if ($requestModel) {
            if(Cache::has('shop_order')){
                Cache::forget('shop_order');
            }

            if (Setting::set('shop.order', $requestModel)) {
                return $this->message('订单设置成功', Url::absoluteWeb('setting.shop.order'));
            } else {
                $this->error('订单设置失败');
            }
        }
        if (!empty($order['goods'])) {
            $goods = Goods::select('id', 'title', 'thumb')
                ->where('status', 1)
                ->whereIn('id', $order['goods'])
                ->where('plugin_id', 0)
                ->get();
            if (!$goods->isEmpty()) {
                $goods = set_medias($goods->toArray(), array('thumb', 'share_icon'));
            }
        } else {
            $goods = [];
        }

        return view('setting.shop.order', [
            'set' => $order,
            'goods' => $goods
        ])->render();
    }

    /**
     * 模板设置
     * @return mixed
     */
    public function temp()
    {
        $temp = Setting::get('shop.temp');
        $styles = [];//模板数据,数据如何来的待定?
        $styles_pc = [];//pc模板数据,待定
        $requestModel = \YunShop::request()->temp;
        if ($requestModel) {
            if (Setting::set('shop.temp', $requestModel)) {
                return $this->message(' 模板设置成功', Url::absoluteWeb('setting.shop.temp'));
            } else {
                $this->error('模板设置失败');
            }
        }

        return view('setting.shop.temp', [
            'set' => $temp,
            'styles' => $styles,
            'styles_pc' => $styles_pc
        ])->render();
    }

    /**
     * 分类层级设置
     * @return mixed
     */
    public function category()
    {
        $category = Setting::get('shop.category');
        $requestModel = \YunShop::request()->category;
        if ($requestModel) {
            if (Setting::set('shop.category', $requestModel)) {
                if(Cache::has('shop_category')){
                    Cache::forget('shop_category');
                }

                return $this->message(' 分类层级设置成功', Url::absoluteWeb('setting.shop.category'));
            } else {
                $this->error('分类层级设置失败');
            }
        }
        return view('setting.shop.category', [
            'set' => $category,
        ])->render();
    }

    /**
     * 联系方式设置
     * @return mixed
     */
    public function contact()
    {
        $contact = Setting::get('shop.contact');
        $requestModel = \YunShop::request()->contact;
        if ($requestModel) {
            if (Setting::set('shop.contact', $requestModel)) {
                return $this->message(' 联系方式设置成功', Url::absoluteWeb('setting.shop.contact'));
            } else {
                $this->error('联系方式设置失败');
            }
        }
        return view('setting.shop.contact', [
            'set' => $contact,
        ])->render();
    }

    /**
     * 短信设置
     * @return mixed
     */
    public function sms()
    {
        $sms = Setting::get('shop.sms');
        $requestModel = \YunShop::request()->sms;
        if ($requestModel) {
            if (Setting::set('shop.sms', $requestModel)) {
                return $this->message(' 短信设置成功', Url::absoluteWeb('setting.shop.sms'));
            } else {
                $this->error('短信设置失败');
            }
        }
        return view('setting.shop.sms', [
            'set' => $sms,
        ])->render();
    }

    //验证码测试
    public static function captchapp()
    {
        $phrase = new PhraseBuilder();
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);

        $builder->setBackgroundColor(150, 150, 150);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);

        $builder->build($width = 100, $height = 40, $font = null);
        $phrase = $builder->getPhrase();

        \Session::flash('code', $phrase);

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-Type: image/jpeg');
        $builder->output();
    }

    /**
     * 分享引导设置
     * @return mixed
     */
    public function share()
    {
        $share = Setting::get('shop.share');
        $requestModel = \YunShop::request()->share;
        if ($requestModel) {
            if (Setting::set('shop.share', $requestModel)) {
                return $this->message(' 引导分享设置成功', Url::absoluteWeb('setting.shop.share'));
            } else {
                $this->error('引导分享设置失败');
            }
        }
        return view('setting.shop.share', [
            'set' => $share,
        ])->render();
    }

    /**
     * 消息提醒设置
     * @return mixed
     */
    public function notice()
    {
        $noticeConfig = \app\common\modules\shop\ShopConfig::current()->get('notice.not-send');
        $notice = Setting::get('shop.notice');
//        $salers = []; //订单通知的商家列表,数据如何取待定?
        //$new_type = []; //通知方式的数组,数据如何来的待定?
        $requestModel = \YunShop::request()->yz_notice;

        $temp_list = MessageTemp::getList();

        if (!empty($requestModel)) {
            if (Setting::set('shop.notice', $requestModel)) {
                return $this->message(' 消息提醒设置成功', Url::absoluteWeb('setting.shop.notice'));
            } else {
                $this->error('消息提醒设置失败');
            }
        }
        return view('setting.shop.notice', [
            'set' => $notice,
            'temp_list' => $temp_list
        ])->render();
    }
    /**
     * 小程序消息提醒设置
     * @return mixed
     */
    public function miniNotice()
    {
        $notice = Setting::get('shop.miniNotice');
//        $salers = []; //订单通知的商家列表,数据如何取待定?
        //$new_type = []; //通知方式的数组,数据如何来的待定?
        $requestModel = \YunShop::request()->yz_notice;

        $temp_list = MessageTemp::getList();

        if (!empty($requestModel)) {
            if (Setting::set('shop.miniNotice', $requestModel)) {
                return $this->message(' 消息提醒设置成功', Url::absoluteWeb('setting.shop.notice'));
            } else {
                $this->error('消息提醒设置失败');
            }
        }

        return view('setting.shop.mini-notice', [
            'set' => $notice,
            'temp_list' => $temp_list
        ])->render();
    }

    /**
     * 交易设置
     * @return mixed
     */
    public function trade()
    {
        $trade = Setting::get('shop.trade');
        $requestModel = \YunShop::request()->trade;
        $refund_status = RefundService::allowRefund();
        if ($requestModel) {
            if (Setting::set('shop.trade', $requestModel)) {
                return $this->message(' 交易设置成功', Url::absoluteWeb('setting.shop.trade'));
            } else {
                $this->error('交易设置失败');
            }
        }
        return view('setting.shop.trade', [
            'set' => $trade,
            'refund_status' => $refund_status,
        ])->render();
    }

    /**
     * 支付方式设置
     * @return mixed
     */
    public function pay()
    {
        $pay = Setting::get('shop.pay');

        self::shareDefault();

        $account = AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid);

        if (empty($pay['weixin_appid']) && empty($pay['weixin_secret']) && !empty($account)) {
            $pay['weixin_appid'] = $account->key;
            $pay['weixin_secret'] = $account->secret;
        }

        $data = [
            'weixin_jie_cert' => '',
            'weixin_jie_key' => '',
            'weixin_jie_root' => ''
        ];//借用微信支付证书,在哪里取得数据待定?
        $requestModel = \YunShop::request()->pay;

        if ($requestModel) {
            $updatefile = $this->updateFile($_FILES);

            if (!is_null($updatefile)) {
                if ($updatefile['status'] == -1) {
                    return $this->message('上传文件类型错误', Url::absoluteWeb('setting.shop.pay'), 'warning');
                }

                if ($updatefile['status'] == 0) {
                    return $this->message('上传文件失败', Url::absoluteWeb('setting.shop.pay'), 'warning');
                }

                $requestModel = array_merge($requestModel, $updatefile['data']);
            }

            if (isset($requestModel['weixin_version']) && $requestModel['weixin_version'] == 1) {
                if (!empty($requestModel['new_weixin_cert']) && !empty($requestModel['new_weixin_key'])) {
                    $updatefile_v2 = $this->updateFileV2(['weixin_cert' => $requestModel['new_weixin_cert'], 'weixin_key' => $requestModel['new_weixin_key']]);


                    if ($updatefile_v2['status'] == 0) {
                        return $this->message('文件保存失败1', Url::absoluteWeb('setting.shop.pay'), 'warning');
                    }

                    $requestModel = array_merge($requestModel, $updatefile_v2['data']);
                }
            }


            if (isset($pay['secret']) && 1 == $pay['secret']) {
                Utils::dataEncrypt($requestModel);
            }
            if (Setting::set('shop.pay', $requestModel)) {
                (new \app\common\services\operation\ShopPayLog(['old' => $pay, 'new' => $requestModel], 'update'));
                $this->setAlipayParams($requestModel);
                return $this->message('支付方式设置成功', Url::absoluteWeb('setting.shop.pay'));
            } else {
                $this->error('支付方式设置失败');
            }
        }

        if (isset($pay['secret']) && 1 == $pay['secret']) {
            Utils::dataDecrypt($pay);
        }

        return view('setting.shop.pay', [
            'set' => $pay,
            'data' => $data
        ])->render();
    }

    public function protocol()
    {
        $shop = Setting::get('shop.protocol');
        $requestModel = \YunShop::request()->protocol;

        if ($requestModel) {
            if (Setting::set('shop.protocol', $requestModel)) {
                return $this->message('注册协议设置成功', Url::absoluteWeb('setting.shop.protocol'));
            } else {
                $this->error('注册协议设置失败');
            }
        }

        return view('setting.shop.protocol', [
            'set' => $shop
        ])->render();
    }
    private function setAlipayParams($data)
    {
        Setting::set('alipay.pem', storage_path() . '/cert/cacert.pem');
        Setting::set('alipay.partner_id', $data['alipay_partner']);
        Setting::set('alipay.seller_id', $data['alipay_account']);
        Setting::set('alipay-mobile.sign_type', 'RSA');
        Setting::set('alipay-mobile.private_key_path', storage_path() . '/cert/private_key.pem');
        Setting::set('alipay-mobile.public_key_path', storage_path() . '/cert/public_key.pem');
        Setting::set('alipay-mobile.notify_url', Url::shopSchemeUrl('payment/alipay/notifyUrl.php'));
        Setting::set('alipay-web.key', $data['alipay_secret']);
        Setting::set('alipay-web.sign_type', 'MD5');
        Setting::set('alipay-web.notify_url', Url::shopSchemeUrl('payment/alipay/notifyUrl.php'));
        Setting::set('alipay-web.return_url', Url::shopSchemeUrl('payment/alipay/returnUrl.php'));
    }


    private function  upload($fileinput)
    {
        $valid_ext = ['pem'];

        if (\Request::isMethod('post')) {
            $file = \Request::file($fileinput);

            if ($file->isValid()) {

                // 获取文件相关信息
                $originalName = $file->getClientOriginalName(); // 文件原名
                $ext = $file->getClientOriginalExtension();     // 扩展名
                $realPath = $file->getRealPath();   //临时文件的绝对路径
                $i = \YunShop::app()->uniacid;

                $upload_file = $i . '_' . $originalName;

                if (!in_array($ext, $valid_ext)) {
                    return ['status' => -1];
                }

                $bool = \Storage::disk('cert')->put($upload_file, file_get_contents($realPath));

                return $bool ? ['status' => 1, 'file' => $originalName] : ['status' => 0];
            }
        }
    }

    /**
     * 前端入口二维码
     *
     * @return string
     */
    public function entry()
    {
        return view('setting.shop.entry', [

        ])->render();
    }

    private function updateFile($file)
    {
        $data = [];

        foreach ($file as $key => $val) {
            if ($val['name']) {
                $update = $this->upload($key);

                if ($update['status'] == -1 || $update['status'] == 0) {
                    return $update;
                }

                if ($update['status'] == 1) {
                    $uniacid = \YunShop::app()->uniacid;
                    $data[$key] = storage_path('cert/' . $uniacid . "_" . $update['file']);
                }
            }
        }

        if (!empty($data)) {
            return ['status' => 1, 'data' => $data];
        }

        return null;
    }

    private function updateFileV2($file_data)
    {
        $data = [];
        $uniacid = \YunShop::app()->uniacid;
        $file_suffix = '.pem';
        foreach ($file_data as $key => $value) {
            $file_name = $uniacid."_".$key.$file_suffix;
            $bool = \Storage::disk('cert')->put($file_name, $value);

            if ($bool) {
                $data[$key] = storage_path('cert/' . $file_name);
            } else {
                return ['status' => 0];
            }

        }

        if (!empty($data)) {
            return ['status' => 1, 'data' => $data];
        }

        return null;
    }

    /**
     * 设置分享默认值
     */
    public function shareDefault() {
        $share = \Setting::get('shop.share');
        if (!$share) {
            $requestModel = [
                'follow_url' => '',
                'title' => '',
                'icon' => '',
                'desc' => '',
                'url' => ''
            ];
            \Setting::set('shop.share', $requestModel);
        }
    }
    /**
     * 设置物流查询
     */
    public function expressInfo() {
        $set = Setting::get('shop.express_info');//快递鸟1002状态为免费，8001状态为收费

        $requestModel = \YunShop::request()->express_info;
        if ($requestModel) {
            if (Setting::set('shop.express_info', $requestModel)) {
                return $this->message(' 物流查询信息设置成功', Url::absoluteWeb('setting.shop.express-info'));
            } else {
                $this->error('物流查询信息设置失败');
            }
        }
        return view('setting.shop.express_info', [
            'set' => $set,
        ])->render();
    }
}