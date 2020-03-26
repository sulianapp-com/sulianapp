<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 11:33
 */

namespace app\backend\modules\charts\modules\member\services;


use app\backend\modules\charts\modules\member\models\MemberLowerCount;
use app\common\models\UniAccount;
use app\Jobs\MemberLowerJob;
use app\Jobs\MemberLowerOrderJob;
use Illuminate\Support\Facades\DB;

class LowerCountService
{
    public function memberCount()
    {
        $uniAccount = UniAccount::getEnable();
        foreach ($uniAccount as $u) {
            \YunShop::app()->uniacid = $u->uniacid;
            \Setting::$uniqueAccountId = $u->uniacid;
            $result = [];

            $uniacid = \YunShop::app()->uniacid;
            $level_member = DB::table('yz_member_children')->select('member_id', 'level', DB::raw('count(1) as total'))->where('uniacid', $uniacid)->whereIn('level', [1,2,3])->groupBy('member_id', 'level')->get();
            $level_all_member = DB::table('yz_member_children')->select('member_id', DB::raw('count(1) as total'))->where('uniacid', $uniacid)->groupBy('member_id')->get();

            foreach ($level_member as $val) {
                if (!isset($result[$val['member_id']])) {
                    $result[$val['member_id']] = [
                        'uid' => $val['member_id'],
                        'uniacid' => $uniacid,
                        'first_total' => $val['total'],
                        'second_total' => 0,
                        'third_total' => 0,
                    ];
                } else {
                    switch ($val['level']) {
                        case 2:
                            $result[$val['member_id']]['second_total'] = $val['total'];
                            break;
                        case 3:
                            $result[$val['member_id']]['third_total'] = $val['total'];
                            break;
                    }
                }
            }

            foreach ($level_all_member as $val) {
                $result[$val['member_id']]['uid'] = $val['member_id'];
                $result[$val['member_id']]['team_total'] = $val['total'];
            }

            $memberModel = new MemberLowerCount();
            foreach ($result as $item) {
                $memberModel->updateOrCreate(['uid' => $item['uid']], $item);
            }
        }
    }
}