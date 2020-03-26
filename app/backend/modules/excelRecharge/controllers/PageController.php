<?php
/**
 * Created by PhpStorm.
 * User: king/QQ:995265288
 * Date: 2019-06-19
 * Time: 16:52
 */

namespace app\backend\modules\excelRecharge\controllers;


use app\common\components\BaseController;
use Yunshop\Love\Common\Services\SetService;

class PageController extends BaseController
{
    /**
     * @var string
     */
    protected $path = 'app/public/recharge';


    //批量充值页面接口
    public function index()
    {
        $this->makeFilePath();

        return view('excelRecharge.page', $this->resultData());
    }

    /**
     * 创建目录
     */
    private function makeFilePath()
    {
        if (!is_dir(storage_path($this->path))) {
            mkdir(storage_path($this->path), 0777);
        }
    }

    private function resultData()
    {
        return [
            'loveOpen' => $this->lovePluginStatus(),
            'loveName' => $this->loveName(),
        ];
    }

    private function loveName()
    {
        if ($this->lovePluginStatus()) {
            return SetService::getLoveName();
        }
        return '爱心值';
    }

    private function lovePluginStatus()
    {
        return app('plugins')->isEnabled('love');
    }
}
