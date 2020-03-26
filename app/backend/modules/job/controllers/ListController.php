<?php


namespace app\backend\modules\job\controllers;


use app\backend\modules\job\models\FailedJob;
use app\backend\modules\job\models\Job;
use app\common\components\BaseController;

class ListController extends BaseController
{
    public function index()
    {
        $list = Job::limit(10)->get();
        dd($list->toArray());
    }
    public function failed(){
        $list = FailedJob::limit(10)->orderBy('id','desc')->get();
        dd($list->toArray());
    }
}