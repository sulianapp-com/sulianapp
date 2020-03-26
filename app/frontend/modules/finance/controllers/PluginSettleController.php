<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 13:47
 */

namespace app\frontend\modules\finance\controllers;

use app\backend\modules\finance\models\Advertisement;
use app\backend\modules\income\Income;
use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\frontend\modules\finance\services\PluginSettleService;
use Yunshop\StoreCashier\common\services\AdvertisementService;

class PluginSettleController extends ApiController
{
    public function pluginList()
    {
        $list = [];
        $config = \app\backend\modules\income\Income::current()->getItems();
        foreach ($config as $key => $value) {
            $list[] = $this->available($key, $value);
        }
        $list = array_filter($list);
        sort($list);
        if ($list) {
            return $this->successJson('获取数据成功', $list);
        }
        return $this->errorJson('未开启手动结算');
    }

    protected function available($key, $value)
    {
        $lang = \Setting::get('shop.lang', ['lang' => 'zh_cn'])['zh_cn'];
        $arr = [];
        switch ($key) {
            case 'merchant':
                if (\Setting::get('plugin.merchant.settlement_model')) {
                    $arr =  [
                        'title' => $value['title'],
                        'type'  => $value['type'],
                        'amount'=>  $value['class']::getNotSettleAmount(\YunShop::app()->getMemberId()),
                        'api'   => 'finance.plugin-settle.plugin-merchant',
                        'icon'  => 'income_d',
                    ];
                }
                break;
            case 'commission':
                if (\Setting::get('plugin.commission.settlement_model')) {
                    $arr =  [
                        'title' => $value['title'],
                        'type'  => $value['type'],
                        'amount'=>  $value['class']::getNotSettleAmount(\YunShop::app()->getMemberId()),
                        'api'   => 'finance.plugin-settle.plugin-commission',
                        'icon'  => 'income_a',
                    ];
                }
                break;
            case 'areaDividend':
                if (\Setting::get('plugin.area_dividend.settlement_model')) {
                    $arr =  [
                        'title' => $lang['area_dividend']['title']?:$value['title'],
                        'type'  => $value['type'],
                        'amount'=>  $value['class']::getNotSettleAmount(\YunShop::app()->getMemberId()),
                        'api'   => 'finance.plugin-settle.plugin-area-dividend',
                        'icon'  => 'income_c',
                    ];
                }
                break;
            case 'teamDividend':
                if (\Setting::get('plugin.team_dividend.settlement_model')) {
                    $arr =  [
                        'title' => $lang['team_dividend']['title']?:$value['title'],
                        'type'  => $value['type'],
                        'amount'=>  $value['class']::getNotSettleAmount(\YunShop::app()->getMemberId()),
                        'api'   => 'finance.plugin-settle.plugin-team-dividend',
                        'icon'  => 'income_b',
                    ];
                }
                break;
            default:
                $arr = [];
        }

        return $arr;
    }

    //获取插件可结算佣金列表
    public function getNotSettleList()
    {
        $type = \YunShop::request()->plugin_type;
        $member_id = \YunShop::app()->getMemberId();

        if (empty($type) || empty($member_id)) {
            throw new AppException('参数错误');
        }

        $plugin_income = Income::current()->getItem($type);
        $class = array_get($plugin_income,'class');
        $function = 'getNotSettleInfo';
        if(class_exists($class) && method_exists($class,$function) && is_callable([$class,$function])){
           $result = $class::$function(['member_id' => $member_id])->orderBy('id', 'desc')->paginate(20);
        } else {
            throw new AppException('不存在的类');
        }

        if ($result->isEmpty()) {
            return $this->errorJson('暂无数据', []);
        }
        $data_processing = PluginSettleService::create($type);

        if (is_null($data_processing)) {
            throw new AppException('数据处理出错');
        }
        $data = [
            'type'   => $type,
            'total'   => $result->total(),
            'per_page' => $result->perPage(),
            'last_page' => $result->lastPage(),
            'data'    => $data_processing->sameFormat($result),
        ];

        return $this->successJson('获取数据成功', $data);
    }

    public function incomeReceive()
    {
        $id = intval(\YunShop::request()->id);
        $type = \YunShop::request()->plugin_type;


        if (empty($type) || empty($id)) {
            throw new AppException('参数错误');
        }

        $plugin_income = Income::current()->getItem($type);

        $class = array_get($plugin_income,'class');
        $function = 'getNotSettleInfo';

        if(class_exists($class) && method_exists($class,$function) && is_callable([$class,$function])){
            $result = $class::$function(['id' => $id])->first();
        } else {
            throw new AppException('不存在的类');
        }
        if (empty($result)) {
            throw new AppException('记录不存在');
        }
        //修改为已结算，加入收入
        $data_processing = PluginSettleService::create($type);
        $income_data = $data_processing->getAdvFormat($result);

        if (app('plugins')->isEnabled('store-cashier')) {

            $info = AdvertisementService::getStoreAdv($income_data);

            if ($info['status'] == 1) {
                $info['income_data'] = $income_data;
                return $this->successJson('成功', $info);
            }
        }

        return $this->successJson('成功', $this->getShopAdv($income_data));

    }

    protected function getShopAdv($income_data)
    {

        $adv = Advertisement::getOneData()->first();

        return [
            'status' => 1,
            'income_data' => $income_data,
            'adv_thumb' => $adv->thumb ? yz_tomedia($adv->thumb) : '',
            'adv_url'   => $adv->adv_url ? $adv->adv_url : '',
            'type'   => 'shop',
        ];
    }


    //招商分红
    public function pluginMerchant()
    {

    }

    //分销佣金
    public function pluginCommission()
    {

    }

    //经销商提成
    public function pluginTeamDividend()
    {
//        $member_id = \YunShop::app()->getMemberId();
//
//        $config = \Config::get('income.merchant');

    }

    //区域分红
    public function pluginAreaDividend()
    {

    }
}