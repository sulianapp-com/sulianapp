<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 19/03/2017
 * Time: 00:48
 */

namespace app\backend\controllers;


use app\backend\modules\charts\models\Supplier;
use app\common\components\BaseController;
use app\common\models\user\WeiQingUsers;
use Illuminate\Support\Facades\DB;
use Yunshop\StoreCashier\store\admin\StoreIndexController;
use Yunshop\Supplier\supplier\controllers\SupplierIndexController;
use Yunshop\StoreCashier\common\models\Store;

class IndexController extends BaseController
{
    protected $isPublic = true;
    public function index()
    {

            $uid = \YunShop::app()->uid;
            $user = WeiQingUsers::getUserByUid($uid)->first();

            if (app('plugins')->isEnabled('store-cashier')) {
                $store = Store::getStoreByUserUid($uid)->first();

                if ($store && $user) {
                    return StoreIndexController::index();
                }
            }

            if (app('plugins')->isEnabled('supplier')) {
                $supplier = Supplier::getSupplierByUid($uid)->first();

                if ($supplier && $user) {
                    return SupplierIndexController::index();
                }
            }

       return view('index',[])->render();
    }



    public function changeField()
    {
        $sql = 'ALTER TABLE `' . DB::getTablePrefix() . 'mc_members` MODIFY `pay_password` varchar(30) NOT NULL DEFAULT 0';

        try {
            DB::select($sql);
            echo '数据已修复';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function changeAgeField()
    {
        $sql = 'ALTER TABLE `' . DB::getTablePrefix() . 'mc_members` MODIFY `age` tinyint(3) NOT NULL DEFAULT 0';

        try {
            DB::select($sql);
            echo '数据已修复';
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}