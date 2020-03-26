<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/11/17
 * Time: 11:14 PM
 */

namespace app\backend\modules\queue\controllers;

use app\common\components\BaseController;
use app\common\exceptions\AppException;
use Illuminate\Support\Facades\Artisan;

class RetryController extends BaseController
{
    public $transactionActions = ['*'];

    public function all()
    {
        ini_set ('memory_limit', '512M');
        $a = Artisan::call('queue:retry', ['id' => ['all']]);
        dump($a);
    }

    /**
     * @throws AppException
     */
    public function ids(){
        if(!request('ids')){
            throw new AppException('缺少ids参数');
        }
        $a = Artisan::call('queue:retry', ['id' => explode(',',request('ids'))]);
        dump($a);

    }
}