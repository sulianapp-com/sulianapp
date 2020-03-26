<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/7/12
 * Time: 下午3:35
 */

namespace app\frontend\controllers;

use app\common\components\ApiController;
use app\common\exceptions\NotFoundException;

class NotFoundController extends ApiController
{
    /**
     * @throws NotFoundException
     */
    public function index(){
        throw new NotFoundException('不存在的页面');
    }
}