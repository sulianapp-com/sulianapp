<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4
 * Time: 14:37
 */

namespace app\frontend\modules\finance\controllers;


use app\common\components\BaseController;
use app\common\facades\Setting;
class PopularizePageShowController extends BaseController
{


    public function index($request,$integrated = null)
    {
        $all_set =  \Setting::getByGroup("popularize");
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
                'baidu' => !empty(Setting::get('shop.shop.baidu'))?Setting::get('shop.shop.baidu'):null,
        ];

        if (is_null($integrated)) {
            return $this->successJson('成功', $data);
        } else {
            return show_json(1, $data);
        }
    }

    protected function moRen()
    {
        return [
            'wechat' => [
                'vue_route' =>[],
                'url' => '',
            ],
            'mini' => [
                'vue_route' => [],
                'url' => '',
            ],
            'wap' => [
                'vue_route' => [],
                'url' => '',
            ],
            'app' => [
                'vue_route' => [],
                'url' => '',
            ],
            'alipay' => [
                'vue_route' => [],
                'url' => '',
            ],
        ];
    }
}