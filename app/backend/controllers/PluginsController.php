<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 10/03/2017
 * Time: 16:42
 */

namespace app\backend\controllers;


use app\backend\modules\menu\Menu;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use app\common\services\plugin\DeliveryDriverSet;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use app\common\services\PermissionService;
use app\common\models\user\User;

class PluginsController extends BaseController
{
    public function showManage()
    {
        return view('public.admin.plugins');
    }

    public function config($name, Request $request)
    {
        $plugin = plugin($name);
        if ($plugin && $plugin->isEnabled() && $plugin->hasConfigView()) {
            return $plugin->getConfigView();
        } else {
            abort(404, trans('admin.plugins.operations.no-config-notice'));
        }
    }

    public function manage()
    {
        $name = \YunShop::request()->name;
        $action = \YunShop::request()->action;

        $plugins = app('app\common\services\PluginManager');
        $plugin = plugin($name);

        if (app()->environment() == 'production') {
            $this->proAuth($name, $action);
        }

        if ($plugin) {
            // pass the plugin title through the translator
            $plugin->title = trans($plugin->title);

            switch (\YunShop::request()->action) {
                case 'enable':
                    $plugins->enable($name);
                    \Artisan::call('config:cache');
                    \Cache::flush();
                    return $this->successJson('启用成功');
                case 'disable':
                    $plugins->disable($name);
                    \Artisan::call('config:cache');
                    \Cache::flush();
                    return $this->successJson('禁用成功');
                case 'delete':
                    $plugins->uninstall($name);
                    \Artisan::call('config:cache');
                    \Cache::flush();
                    return $this->successJson('删除成功');
                default:
                    # code...
                    break;
            }
        }
    }

    public function batchMange()
    {
        $plugins = app('app\common\services\PluginManager');
        $names = explode(',', \YunShop::request()->names);
        $action = \YunShop::request()->action;

        foreach ($names as $name) {
            if (app()->environment() == 'production') {
                $this->proAuth($name, $action);
            }

            $plugin = plugin($name);
            if ($plugin) {
                $plugin->title = trans($plugin->title);
                switch (\YunShop::request()->action) {
                    case 'enable':
                        $plugins->enable($name);
                        break;
                    case 'disable':
                        $plugins->disable($name);
                        break;
                    default:
                        die(json_encode(array(
                            "result" => 0,
                            "error" => "操作错误"
                        )));
                        break;
                }
            }
        }
    }

    public function getPluginData()
    {
        $installed = app('plugins')->getPlugins();

        return view('admin.plugins', [
            'installed' => $installed
        ]);
    }


    public function getPluginList()
    {

//        $dividend['name'] = '分润类';
//        $industry['name'] = '行业类';
//        $marketing['name'] = '营销类';
//        $tool['name'] = '工具类';
//        $recharge['name'] = '生活充值';
//        $api['name'] = '接口类';
        //\Cache::flush();
        $class = $this->getType();
        $data = [];

        $plugins = Menu::current()->getPluginMenus();//全部插件

        foreach ($plugins as $key => $plugin) {

            if (!$plugin['type']) {
                continue;
            }
            $data[$plugin['type']][$key] = $plugin;
            $data[$plugin['type']][$key]['description'] = app('plugins')->getPlugin($key)->description;
            $data[$plugin['type']][$key]['icon_url'] = static_url("yunshop/plugins/list-icon/img/{$plugin['list_icon']}.png");


            //$data[$plugin['type']][$key]['url'] = $this->canAccess($key);

        }

        return view('admin.pluginslist', [
            'plugins' => $plugins,
            'data' => $data,
            'class' => $class
        ]);
    }

    public static function canAccess($item)
    {
        $current_menu = Menu::current()->getPluginMenus()[$item];
        $url = $current_menu['url'];

        if (PermissionService::isFounder()) {
            return $url;
        }
        if (PermissionService::isOwner()) {
            return $url;
        }
        if (PermissionService::isManager()) {
            return $url;
        }
        if (PermissionService::checkNoPermission($item) === true) {
            return $url;
        }

        if (!isset($current_menu['child'])) {
            return $url;
        }

        $userPermission = User::userPermissionCache();
        //检测当前 key 下路由是否有权限访问
        foreach ($current_menu['child'] as $key => $value) {

            if ($value['url'] == $current_menu['url'] && in_array($key, $userPermission) && $value['menu'] == 1) {
                return $url;
                break;
            }
            continue;
        }
        //上面条件都不满足时，找第一个有权限访问的路由
        foreach ($current_menu['child'] as $key => $value) {

            if (in_array($key, $userPermission) && $value['menu'] == 1) {
                return $value['url'];
                break;
            }
            continue;
        }
        return 'index.index';
    }

    public function setTopShow()
    {
        $data = request()->input();

//        $data['action'] ?: app('plugins')->enTopShow($data['name'], 1);
        if ($data['action']) {
            app('plugins')->enTopShow($data['name'], 0);
            return $this->message('取消顶部栏成功', Url::web('plugins.getPluginList'));
        } else {

            $menu = config(config('app.menu_key','menu'));
            $counts = 0;
            //常用功能
            foreach ($menu as $key => $itme) {
                if(isset($itme['menu']) && $itme['menu'] == 1 && can($key) && ($itme['top_show'] == 1 || app('plugins')->isTopShow($key)))
                {
                    ++$counts;
                    if ($counts > 7){
                        return $this->message('顶部栏最大数量为八个');
                    }
                }
            }

            app('plugins')->enTopShow($data['name'], 1);
            return $this->message('添加顶部栏成功', Url::web('plugins.getPluginList'));
        }
    }

    public function proAuth($name, $action)
    {
        if ($action == 'enable') {
            $key = \Setting::get('shop.key')['key'];
            $secret = \Setting::get('shop.key')['secret'];

            $url = config('auto-update.proAuthUrl') . "/chkname/{$name}";

            $res = \Curl::to($url)
                ->withHeader(
                    "Authorization: Basic " . base64_encode("{$key}:{$secret}")
                )
                ->asJsonResponse(true)
                ->get();
            // dd($res);

            // \Log::debug('-------update res-----', $res);
            if (0 == $res['status']) {
                throw new ShopException('应用未授权');
            }
        }
    }

    public function getType()
    {
        return [
            'dividend' => [
                'name' => '分润类',
                'color' => '#F15353',
            ],
            'industry' => [
                'name' => '行业类',
                'color' => '#eb6f50',
            ],
            'marketing' => [
                'name' => '营销类',
                'color' => '#f0b652',
            ],
            'tool' => [
                'name' => '工具类',
                'color' => '#f59753',
            ],
            'recharge' => [
                'name' => '生活充值',
                'color' => '#50d9a7',
            ],
            'api' => [
                'name' => '接口类',
                'color' => '#53d5f0',
            ],
            'blockchain' => [
                'name' => '区块链类',
                'color' => '#469de2',
            ],
        ];
    }

}