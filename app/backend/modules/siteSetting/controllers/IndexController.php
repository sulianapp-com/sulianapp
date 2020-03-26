<?php

namespace app\backend\modules\siteSetting\controllers;

use app\common\components\BaseController;
use app\common\facades\SiteSetting;

class IndexController extends BaseController
{
    public function index()
    {
        $setting = SiteSetting::get('base');
    
        return view('siteSetting.index', [
            'setting' => json_encode($setting),
        ])->render();
    }
}