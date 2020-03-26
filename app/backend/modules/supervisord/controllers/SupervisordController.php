<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 下午5:26
 */

namespace app\backend\modules\supervisord\controllers;

use app\backend\modules\supervisord\services\Supervisor;
use app\common\components\BaseController;
use app\common\facades\SiteSetting;
use app\common\helpers\Cache;
use app\common\helpers\Url;
use app\common\facades\Setting;


class SupervisordController extends BaseController
{
    private $supervisor = null;

    public function preAction()
    {
        $this->supervisor = app('supervisor');
        //$this->supervisor = new Supervisor('dev4.yunzshop.com', 9001);
        //$this->supervisor = new Supervisor(\Request::server('HTTP_HOST'), 9001);
        $this->supervisor->setTimeout(5000);  // microseconds
    }

    /**
     * 商城设置
     * @return mixed
     */
    public function index()
    {
        //print_r($supervisor->getState());
        //$allProcessInfo = $this->supervisor->getAllProcessInfo();
        //$allProcessInfo = $supervisor->stopProcess("dev1-worker:dev1-worker_01");
        //$allProcessInfo = $supervisor->readLog(0);
        //$allProcessInfo = $supervisor->logMessage();

        //dd($allProcessInfo);
        return view('supervisor.index', [
        ])->render();

    }
    public function store(){
        $setting = request()->input('setting');
        if ($setting){

            $set['address']['ip'] = $setting['address']['ip']?:'http://127.0.0.1';
            SiteSetting::set('supervisor',$set);
            return $this->successJson("设置保存成功", Url::absoluteWeb('supervisord.supervisord.store'));
        }
        $supervisord  = SiteSetting::get('supervisor');
        $supervisord['address']['ip'] ? : SiteSetting::set('supervisor', $supervisord['address']['ip'] = 'http://127.0.0.1');

        return view('supervisor.store',[
             'setting'=>json_encode($supervisord)
        ])->render();
    }

    public function queue(){
        $setting = request()->input('setting');
        if ($setting){
            $set['queue']['is_classify'] = $setting['queue']['is_classify'] ?: 0;
            SiteSetting::set('supervisor',$set);
            return $this->successJson("设置保存成功", Url::absoluteWeb('supervisord.supervisord.store'));
        }
        $supervisord  = Setting::getNotUniacid('supervisor');
        $supervisord['queue']['is_classify'] ? : SiteSetting::set('supervisor', $supervisord['queue']['is_classify'] = 0);

        return view('supervisor.queue',[
             'setting'=>json_encode($supervisord)
        ])->render();
    }

    public function process()
    {
        //print_r($supervisor->getState());
        $allProcessInfo = $this->supervisor->getAllProcessInfo();

        $state = $this->supervisor->getState();
        // dd($state);

        foreach($allProcessInfo->val as $key => &$val) {
            $val['cstate'] = false;
            // echo $val;

        }
        // $allProcessInfo = $this->supervisor->stopProcess("dev1-worker:dev1-worker_01");
        // $allProcessInfo = $this->supervisor->readLog(0);
        //$allProcessInfo = $this->supervisor->logMessage();
        // dd($allProcessInfo);
        return json_encode([
            'process' => $allProcessInfo,
            'state' => $state
        ]);
    }

    public function showlog()
    {
        $process = \YunShop::request()->process;

        //print_r($supervisor->getState());
        $result = $this->supervisor->tailProcessStdoutLog($process, 1, 100000);
        //$allProcessInfo = $supervisor->readLog(0);
        //$allProcessInfo = $supervisor->logMessage();
        return json_encode($result);

    }

    public function clearlog()
    {
        $process = \YunShop::request()->process;

        //print_r($supervisor->getState());
        $result = $this->supervisor->clearProcessLogs($process);
        //$allProcessInfo = $supervisor->readLog(0);
        //$allProcessInfo = $supervisor->logMessage();
        return json_encode($result);

    }

    public function stop()
    {
        $process = \YunShop::request()->process;

        //print_r($supervisor->getState());
        $result = $this->supervisor->stopProcess($process);
        //$allProcessInfo = $supervisor->readLog(0);
        //$allProcessInfo = $supervisor->logMessage();

        return json_encode($result);
    }

    public function stopAll()
    {
        //print_r($supervisor->getState());
        $result = $this->supervisor->stopAllProcesses();
        //$allProcessInfo = $supervisor->readLog(0);
        //$allProcessInfo = $supervisor->logMessage();

        return json_encode($result);

    }

    public function start()
    {
        $process = \YunShop::request()->process;

        //print_r($supervisor->getState());
        $result = $this->supervisor->startProcess($process);
        //$allProcessInfo = $supervisor->readLog(0);
        //$allProcessInfo = $supervisor->logMessage();

        return json_encode($result);

    }

    public function startAll()
    {
        //print_r($supervisor->getState());
        $result = $this->supervisor->startAllProcesses();
        //$allProcessInfo = $supervisor->readLog(0);
        //$allProcessInfo = $supervisor->logMessage();

        return json_encode($result);

    }

    public function restart()
    {
        //print_r($supervisor->getState());
        $result = $this->supervisor->restart();
        //$allProcessInfo = $supervisor->readLog(0);
        //$allProcessInfo = $supervisor->logMessage();

        return json_encode($result);

    }

}