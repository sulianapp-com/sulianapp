<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 15:55
 */

namespace app\backend\modules\charts\modules\phone\controllers;


use app\common\components\BaseController;
use Illuminate\Support\Facades\DB;

class PhoneAttributionController extends BaseController
{

    public function index()
    {
        $uniacid = \YunShop::app()->uniacid;
        $phone_data = DB::table('yz_phone_attribution')->select('province', DB::raw('count(1) as num'))->where('uniacid', $uniacid)->groupBy('province')->get()->toArray();

        return view('charts.phone.phone_attribution',[
            'phone_data' => $phone_data,
            'phone_map_data' => json_encode($phone_data,256),
        ]);
    }

}