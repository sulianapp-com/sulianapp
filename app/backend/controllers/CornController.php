<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/7/9
 * Time: 下午5:32
 */

namespace app\backend\controllers;


use Illuminate\Routing\Controller;

class CornController extends Controller
{
    public function index()
    {
        // Get security key from config
        $cronkeyConfig = \Config::get('liebigCron.cronKey');

// If no security key is set in the config, this route is disabled
        if (empty($cronkeyConfig)) {
            \Log::error('Cron route call with no configured security key');
            \App::abort(404);
        }

// Get security key from request
        $cronkeyRequest = request()->get('key');
// Create validator for security key
        $validator = \Validator::make(
            array('cronkey' => $cronkeyRequest),
            array('cronkey' => 'required|alpha_num')
        );
        if ($validator->passes()) {
            if ($cronkeyConfig === $cronkeyRequest) {
                \Artisan::call('cron:run', array());
            } else {
                // Configured security key is not equals the sent security key
                \Log::error('Cron route call with wrong security key');
                \App::abort(404);
            }
        } else {
            // Validation not passed
            \Log::error('Cron route call with missing or no alphanumeric security key');
            \App::abort(404);
        }
        return;
    }
}