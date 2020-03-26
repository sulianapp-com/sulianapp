<?php
/**
 * Created by PhpStorm.
 * User: blank
 * Date: 2018/11/28
 * Time: 13:56
 */

namespace app\backend\modules\member\controllers;

use app\backend\modules\income\Income;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\services\popularize\PopularizePageShowFactory;

class PopularizePageShowController extends BaseController
{
    //微信
    public function wechatSet()
    {
        $info = Setting::get("popularize.wechat");
        if (\Request::isMethod('post')) {
            $set = request()->input('set');
            if (Setting::set("popularize.wechat", $set)) {
                //$this->toJson();
                return $this->message('保存成功', Url::absoluteWeb('member.popularize-page-show.wechat-set'));
            } else {
                throw new ShopException('保存失败');
            }
        }

        return view('member.popularize.index',[
            'info' => $info,
            'plugin_page' => $this->getData(),
        ])->render();
    }

    //微信小程序
    public function miniSet()
    {
        $info = Setting::get("popularize.mini");


        if (\Request::isMethod('post')) {
            $set = request()->input('set');

            if (Setting::set("popularize.mini", $set)) {
                //$this->toJson();
                return $this->message('保存成功', Url::absoluteWeb('member.popularize-page-show.mini-set'));
            } else {
                throw new ShopException('保存失败');
            }
        }

        $info['min-app'] = 1;

        return view('member.popularize.index',[
            'info' => $info,
            'plugin_page' => $this->getData(),
        ])->render();
    }

    //手机浏览器 pc
    public function wapSet()
    {
        $info = Setting::get("popularize.wap");

        if (\Request::isMethod('post')) {
            $set = request()->input('set');

            if (Setting::set("popularize.wap", $set)) {
                //$this->toJson();
                return $this->message('保存成功', Url::absoluteWeb('member.popularize-page-show.wap-set'));
            } else {
                throw new ShopException('保存失败');
            }
        }

        return view('member.popularize.index',[
            'info' => $info,
            'plugin_page' => $this->getData(),
        ])->render();
    }

    //app
    public function appSet()
    {
        $info = Setting::get("popularize.app");

        if (\Request::isMethod('post')) {
            $set = request()->input('set');

            if (Setting::set("popularize.app", $set)) {
                //$this->toJson();
                return $this->message('保存成功', Url::absoluteWeb('member.popularize-page-show.app-set'));
            } else {
                throw new ShopException('保存失败');
            }
        }

        return view('member.popularize.index',[
            'info' => $info,
            'plugin_page' => $this->getData(),
        ])->render();
    }

    //支付宝
    public function alipaySet()
    {
        $info = Setting::get("popularize.alipay");


        if (\Request::isMethod('post')) {
            $set = request()->input('set');

            if (Setting::set("popularize.alipay", $set)) {
                //$this->toJson();
                return $this->message('保存成功', Url::absoluteWeb('member.popularize-page-show.alipay-set'));
            } else {
                throw new ShopException('保存失败');
            }
        }

        return view('member.popularize.index',[
            'info' => $info,
            'plugin_page' => $this->getData(),
        ])->render();
    }

    /**
     * 获取商城开启的插件
     * @return array 开启的插件页面路由与名称
     */
    protected function getData()
    {
        $lang_set = $this->getLangSet();

        $config = $this->getIncomePageConfig();

        $plugins = $this->getPlugins();

        foreach ($config as $key => $item) {

            $incomeFactory = new PopularizePageShowFactory(new $item['class'], $lang_set);

            if ($plugins[$incomeFactory->getMark()]) {
                $array[] = [
                    'url' => $plugins[$incomeFactory->getMark()],
                    'title' => $incomeFactory->getTitle(),
                    'mark'  => $incomeFactory->getMark(),
                    'status' => 1,
                ];
            } else {
                $array[] = [
                    'url' => $incomeFactory->getAppUrl(),
                    'title' => $incomeFactory->getTitle(),
                    'mark'  => $incomeFactory->getMark(),
                    'status' => 0,
                ];
            }
        }
        return $array;
    }

    //当收入页面显示的前端路由有两个时,需要在把俩个路由都添加进来
    // \Config::set(['popularize_page_show.收入页面类的getMark()方法返回的值' => 路由数组);
    protected function getPlugins()
    {

        \Config::set([
            'popularize_page_show.area_dividend' => ['regionalAgencyCenter','applyRegionalAgency'],
            'popularize_page_show.store_cashier' => ['cashier','storeApply'],
            'popularize_page_show.store_withdraw' => ['storeManage','storeApply'],
            'popularize_page_show.hotel_cashier' => ['HotelCashier','storeApply'],
            'popularize_page_show.hotel_withdraw' => ['HotelManage', 'hotelApply'],
            'popularize_page_show.merchant' => ['enterprise_index', 'enterprise_apply'],
            'popularize_page_show.micro' => ['microShop_ShopKeeperCenter', 'microShop_apply'],
        ]);
        $popularize_page_show = [
            'area_dividend' => ['regionalAgencyCenter','applyRegionalAgency'],
            'store_cashier' => ['cashier','storeApply'],
            'store_withdraw' => ['storeManage','storeApply'],
            'hotel_cashier' => ['HotelCashier','storeApply'],
            'hotel_withdraw' => ['HotelManage', 'hotelApply'],
            'merchant' => ['enterprise_index', 'enterprise_apply'],
            'micro' => ['microShop_ShopKeeperCenter', 'microShop_apply'],
        ];

        $plugin = \app\common\modules\shop\ShopConfig::current()->get('popularize_page_show')?:[];

        return array_merge($popularize_page_show, $plugin);
    }

    /**
     * 生成js文件给前端用
     */
    protected function toJson()
    {
        //放弃使用这个方法，原因无法匹配多个公众号
        return ;

        $all_set =  Setting::getByGroup("popularize");

        $data = [
            'wechat' => [
                'vue_route' => !empty($all_set['wechat']['vue_route'])?$all_set['wechat']['vue_route']:[],
                'url' => !empty($all_set['wechat']['callback_url'])?$all_set['wechat']['callback_url']:'',
            ],
            'mini' => [
                'vue_route' => !empty($all_set['mini']['vue_route'])?$all_set['mini']['vue_route']:[],
                'url' => !empty($all_set['mini']['callback_url'])?$all_set['mini']['callback_url']:'',
            ],
            'wap' => [
                'vue_route' => !empty($all_set['wap']['vue_route'])?$all_set['wap']['vue_route']:[],
                'url' => !empty($all_set['wap']['callback_url'])?$all_set['wap']['callback_url']:'',
            ],
            'app' => [
                'vue_route' => !empty($all_set['app']['vue_route'])?$all_set['app']['vue_route']:[],
                'url' => !empty($all_set['app']['callback_url'])?$all_set['app']['callback_url']:'',
            ],
            'alipay' => [
                'vue_route' => !empty($all_set['alipay']['vue_route'])?$all_set['alipay']['vue_route']:[],
                'url' => !empty($all_set['alipay']['callback_url'])?$all_set['alipay']['callback_url']:'',
            ],
        ];
        $string =  json_encode($data);
        $sj = date('Y-m-d H:i:s', time());
        $json_str =<<<json
//update $sj
let popularize = {$string};
if (typeof define === "function") {
    define(popularize)
} else {
    window.\$popularize = popularize;
}
json;

        $path = 'static'.DIRECTORY_SEPARATOR.'yunshop'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR.'popularize'.DIRECTORY_SEPARATOR;
        $absolute_file = $path.'popularize_'.\YunShop::app()->uniacid.'.js';

        // 生成目录
        if (!is_dir(base_path($path))) {
            mkdir(base_path($path), 0777);
        }

        return file_put_contents(base_path($absolute_file), $json_str);
    }

    /**
     * 收入页面配置 config
     *
     * @return mixed
     */
    private function getIncomePageConfig()
    {
        return Income::current()->getPageItems();
    }


    /**
     * 获取商城中的插件名称自定义设置
     *
     * @return mixed
     */
    private function getLangSet()
    {
        $lang = \Setting::get('shop.lang', ['lang' => 'zh_cn']);

        return $lang[$lang['lang']];
    }

}