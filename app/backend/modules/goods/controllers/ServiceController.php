<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 下午1:51
 */

namespace app\backend\modules\goods\controllers;

use app\common\components\BaseController;

use Setting;
use app\common\helpers\Url;

class ServiceController extends BaseController
{
     public function index(){

         $setting = request()->input('setting');
         if ($setting){
             if (empty($setting['service']['name'])){
                 $setting['service']['name'] = '服务费';
             }
             Setting::set('goods.service',$setting);
             return $this->successJson("设置保存成功", Url::absoluteWeb('goods.service'));
         }
         return view('goods.service',['setting' =>json_encode( Setting::get('goods.service')),])->render();
     }
}