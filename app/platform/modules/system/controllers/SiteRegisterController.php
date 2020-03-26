<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/5/9
 * Time: 14:57
 */

namespace app\platform\modules\system\controllers;

use app\platform\controllers\BaseController;
use app\common\facades\Setting;
use app\common\models\Address;
use app\common\services\AutoUpdate;
use Ixudra\Curl\Facades\Curl;
use app\common\models\Setting as SettingModel;

class SiteRegisterController extends BaseController
{
    public function __construct()
    {
        $this->_log = app('log');
    }

    /**
     * 密钥填写
     *
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     * @return mixed
     */
    public function index()
    {
        $upgrade  = Setting::getNotUniacid('platform_shop.key');
        $page     = 'auth';

        if (empty($upgrade['key']) && empty($upgrade['secret'])) {
            $domain = request()->getHttpHost();
            $url = config('auto-update.registerUrl') . '/check_domain.json';

            $auth_str = Curl::to($url)
                ->withData(['domain' => $domain])
                ->asJsonResponse(true)
                ->get();

            if (empty($auth_str['data']['key']) && empty($auth_str['data']['secret'])) {
                $page = 'free';
            } else {
                $upgrade = $auth_str['data'];

                $this->processingKey($upgrade, 'create');

                $free_plugins = SettingModel::where('group', 'free')->where('key', 'plugin')->first();

                if (!is_null($free_plugins)) {
                    Setting::set('free.plugin', unserialize($free_plugins->value));
                }
            }
        }

        $auth_url = '';     //yzWebFullUrl('setting.key.index', ['page' => 'auth']);
        $free_url = '';     //yzWebFullUrl('setting.key.index', ['page' => 'free']);

        $btn = empty($upgrade['key']) || empty($upgrade['secret']) ? 1 : 0;

        // 获取省级列表
        $province = Address::getProvince();

        return $this->successJson('成功', [
            'province' => ['data' => $province->toArray()],
            'page' => ['type' => $page],
            'url' => ['free' => $free_url, 'auth' =>$auth_url],
            'set' => ['key' => $upgrade['key'], 'secret' => $upgrade['secret'], 'btn' => $btn]
        ]);
    }

    /**
     * 处理信息
     *
     * @param $requestModel
     * @param $type
     * @return bool
     */
    private function processingKey($requestModel, $type)
    {
        $domain = request()->getHttpHost();
        $data = [
            'key' => $requestModel['key'],
            'secret' => $requestModel['secret'],
            'domain' => $domain
        ];

        if($type == 'create') {

            $content = Curl::to(config('auto-update.checkUrl').'/app-account/create')
                ->withData($data)
                ->get();
            $writeRes = Setting::set('platform_shop.key', $requestModel);

            \Cache::forget('app_auth');

            return $writeRes && $content;

        } else if($type == 'cancel') {

            $content = Curl::to(config('auto-update.checkUrl').'/app-account/cancel')
                ->withData($data)
                ->get();

            $writeRes = Setting::set('platform_shop.key', '');

            \Cache::forget('app_auth');

            return $writeRes && $content ;
        }
    }

    /*
     * 检测是否有数据存在
     */
    public function isExist($data)
    {
        $type = request()->type;
        $domain = request()->getHttpHost();

        $filename = config('auto-update.checkUrl').'/check_isKey.json';
        $postData = [
            'type' => $type,
            'domain' => $domain
        ];
        $update = new AutoUpdate();
        $res = $update->isKeySecretExists($filename, $data, $postData, 'auto_update');
        return $res;
    }

    /**
     * 获取城市
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCity()
    {
        $data = request()->data;

        $addressData = Address::getCityByParentId($data['id']);

        return $this->successJson('ok', $addressData);
    }

    /**
     * 获取地区
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArea()
    {
        $data = request()->data;

        $addressData = Address::getAreaByParentId($data['id']);

        return $this->successJson('ok', $addressData);
    }

    public function register()
    {
        $data = request()->setdata;

        $auth_url = '/admin/system/siteRegister/index';

        $key = 'free';
        $secret = request()->getSchemeAndHttpHost();

        $url = config('auto-update.registerUrl') . "/free/{$data['name']}/{$data['trades']}/{$data['province']['areaname']}/{$data['city']['areaname']}/{$data['area']['areaname']}/{$data['address']}/{$data['mobile']}/{$data['captcha']}";

        $register = Curl::to($url)
            ->withHeader(
                "Authorization: Basic " . base64_encode("{$key}:{$secret}")
            )
            ->asJsonResponse(true)
            ->get();

        if (!is_null($register) && 1 == $register['result']) {
            if ($register['data']) {
                //检测数据是否存在
                $res = $this ->isExist($register['data']['shop']);
                //var_dump($res);exit();
                if(!$res['isExists']) {
                    if($res['message'] == 'amount exceeded')
                        $this->errorJson('您已经没有剩余站点数量了，如添加新站点，请取消之前的站点或者联系我们的客服人员！');
                    else
                        $this->errorJson('Key或者密钥出错了！');
                } else {
                    if ($this->processingKey($register['data']['shop'], 'create')) {
                        if ($register['data']['plugins']) {
                            Setting::set('free.plugin', $register['data']['plugins']);
                        }
                        return $this->successJson("站点添加成功", ['url' => $auth_url]);
                    }
                }
            }
        }

        return $this->errorJson("站点添加失败");
    }

    /**
     * 获取手机验证码
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSms()
    {
        $key = 'free';
        $secret = request()->getHttpHost();
        $mobile = request()->mobile;

        $url = config('auto-update.registerUrl') . "/sendsms/{$mobile}";

        $res = Curl::to($url)
            ->withHeader(
                "Authorization: Basic " . base64_encode("{$key}:{$secret}")
            )
            ->asJsonResponse(true)
            ->get();

        return $this->successJson('验证码已发送', $res);
    }

    public function resetSecretKey()
    {
        $data = request()->data;

        $setting = Setting::getNotUniacid('shop.key');

        if ($data['key'] && $data['secret']) {
            try {
                Setting::setNotUniacid('platform_shop.key', $data);
            }  catch (\Exception $e) {
                return $this->errorJson($e->getMessage());
            }
        }

        return $this->successJson('成功', $setting);
    }
}