<?php

namespace app\platform\controllers;

class ClearController extends BaseController
{
    public function index()
	{
        \Artisan::call('config:cache');
        \Cache::flush();
		return $this->successJson('操作成功');
	}
}
