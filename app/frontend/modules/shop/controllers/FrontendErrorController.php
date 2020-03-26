<?php
/**
 * Created by PhpStorm.
 * User: CSY
 * Date: 2019/10/16
 * Time: 17:36
 */

namespace app\frontend\modules\shop\controllers;


use app\common\components\ApiController;
use app\framework\Log\FrontendErrorLog;

class FrontendErrorController extends ApiController
{
   public function doLog()
   {
       (new FrontendErrorLog())
           ->add('JS错误公众号:'.\YunShop::app()->uniacid.'用户ID:'.\YunShop::app()->getMemberId(),[request()->error_info]);

       return $this->successJson('成功', []);
  }
}