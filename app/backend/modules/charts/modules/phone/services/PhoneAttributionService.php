<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 17:59
 */

namespace app\backend\modules\charts\modules\phone\services;


use app\backend\modules\charts\modules\phone\models\PhoneAttribution;
use app\common\models\UniAccount;
use Illuminate\Support\Facades\DB;

class PhoneAttributionService
{
    public function phoneStatistics()
    {
        $uniAccount = UniAccount::get();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;

            $uniacid = \YunShop::app()->uniacid;
            $member_phone = DB::table('mc_members')->select('uid', 'mobile', 'uniacid')->where('uniacid', $uniacid)->where('mobile', '<>', '')->get()->toArray();
            foreach ($member_phone as $k => $item) {
                    $data[$k] = $this->getPhoneApi($item['mobile']);
                    $phone[$k]['uid'] = $item['uid'];
                    $phone[$k]['uniacid'] = $item['uniacid'];
                    $phone[$k]['phone'] = json_decode(file_get_contents($data[$k]));
            }

            foreach ($phone as $k => $item) {
                $result[$k]['uid'] = $item['uid'];
                $result[$k]['uniacid'] = $item['uniacid'];
                $result[$k]['province'] = $item['phone']->data->province;
                $result[$k]['city'] = $item['phone']->data->city;
                $result[$k]['sp'] = $item['phone']->data->sp;
            }

            $phoneModel = new PhoneAttribution();
            $phoneData = [];
            foreach ($result as $k => $item) {
                $phoneData['uid']= $item['uid'];
                $phoneData['uniacid']= $item['uniacid'];
                $phoneData['province']= $item['province'];
                $phoneData['city']= $item['city'];
                $phoneData['sp']= $item['sp'];

                $phoneModel->updateOrCreate(['uid' => $item['uid']], $phoneData);
            }
        }
    }

    public function getPhoneApi($mobile)
    {
//        $url = "https://cx.shouji.360.cn/phonearea.php?number=18520632247";  //360接口
//        $url = "https://www.iteblog.com/api/mobile.php?mobile=18519101034";  //ITEBLOG接口

        return "https://cx.shouji.360.cn/phonearea.php?number=".$mobile;
    }
}