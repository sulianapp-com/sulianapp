<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/9
 * Time: 15:07
 */

namespace app\backend\modules\member\controllers;

use app\backend\modules\member\models\Member;
use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\services\MemberServices;
use app\common\components\BaseController;
use Yunshop\Commission\models\Agents;
use app\common\models\Income;

/**
 * 收入
 */
class MemberIncomeController extends BaseController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $groups = MemberGroup::getMemberGroupList();
        $levels = MemberLevel::getMemberLevelList();
        $uid = \YunShop::request()->id ? intval(\YunShop::request()->id) : 0;
        if ($uid == 0 || !is_int($uid)) {
            $this->message('参数错误', '', 'error');
            exit;
        }

        $member = Member::getMemberInfoById($uid);

        if (!empty($member)) {
            $member = $member->toArray();

            if (1 == $member['yz_member']['is_agent'] && 2 == $member['yz_member']['status']) {
                $member['agent'] = 1;
            } else {
                $member['agent'] = 0;
            }

            $myform = json_decode($member['yz_member']['member_form']);
        }

//        $set = \Setting::get('shop.member');
//
//        if (empty($set['level_name'])) {
//            $set['level_name'] = '普通会员';
//        }
//
//        if (0 == $member['yz_member']['parent_id']) {
//            $parent_name = '总店';
//        } else {
//            $parent = Member::getMemberById($member['yz_member']['parent_id']);
//
//            $parent_name = $parent->nickname;
//        }

        //检测收入数据
//        $status = $member['yz_member']['status'];
        $incomeModel = Income::getIncomes()->where('member_id', $uid)->get();
        $config = \app\backend\modules\income\Income::current()->getItems();
        unset($config['balance']);
//        if ($status !== null && $status >= '0') {
//            $incomeModel = $incomeModel->where('status', $status);
//        }
//        $config = \app\common\modules\shop\ShopConfig::current()->get('plugin');
//        dump($config);exit();
        $incomeAll = [
            'title' => '推广收入',
            'type' => 'total',
            'type_name' => '推广佣金',
            'income' => $incomeModel->sum('amount'),
            'withdraw' => $incomeModel->where('status', 1)->sum('amount'),
            'no_withdraw' => $incomeModel->where('status', 0)->sum('amount')
        ];

//        $incomeGroup = Income::where('member_id', $uid)->select('type_name')->distinct()->get();
//        foreach ($incomeGroup as $key => $item) {
//            $incomeData[$key] = [
//                'type_name' => $item['type_name'],
//                'income' => $incomeModel->where('type_name', $item['type_name'])->sum('amount'),
//                'withdraw' => $incomeModel->where('type_name', $item['type_name'])->where('status', 1)->sum('amount'),
//                'no_withdraw' => $incomeModel->where('type_name', $item['type_name'])->where('status', 0)->sum('amount')
//            ];
//        }
//        foreach ($incomeModel as $key => $item) {
//
//            $typeModel = $incomeModel->where('incometable_type', $item['class']);
//            $incomeData[$key] = [
//                'title' => $item['title'],
//                'ico' => $item['ico'],
//                'type' => $item['type'],
//                'type_name' => $item['title'],
//                'income' => $typeModel->sum('amount')
//            ];
//            if ($item['agent_class']) {
//                $agentModel = $item['agent_class']::$item['agent_name'](\YunShop::app()->getMemberId());
//
//                if ($item['agent_status']) {
//                    $agentModel = $agentModel->where('status', 1);
//                }
//
//                //推广中心显示
//                if (!$agentModel) {
//                    $incomeData[$key]['can'] = false;
//                } else {
//                    $agent = $agentModel->first();
//                    if ($agent) {
//                        $incomeData[$key]['can'] = true;
//                    } else {
//                        $incomeData[$key]['can'] = false;
//                    }
//                }
//            } else {
//                $incomeData[$key]['can'] = true;
//            }
//
//        }
//        dump($incomeData);exit();
//        if ($incomeData) {
//            return $this->successJson('获取数据成功!', $incomeData);
//        }
//        return $this->errorJson('未检测到数据!');
        foreach ($config as $key => $item) {

            $typeModel = $incomeModel->where('incometable_type', $item['class']);
            $incomeData[$key] = [
                'title' => $item['title'],
                'ico' => $item['ico'],
                'type' => $item['type'],
                'type_name' => $item['title'],
                'income' => $typeModel->sum('amount'),
                'withdraw' => $typeModel->where('status', 1)->sum('amount'),
                'no_withdraw' => $typeModel->where('status', 0)->sum('amount')
            ];
            if ($item['agent_class']) {
                $agentModel = $item['agent_class']::{$item['agent_name']}(\YunShop::app()->getMemberId());

                if ($item['agent_status']) {
                    $agentModel = $agentModel->where('status', 1);
                }

                //推广中心显示
                if (!$agentModel) {
                    $incomeData[$key]['can'] = false;
                } else {
                    $agent = $agentModel->first();
                    if ($agent) {
                        $incomeData[$key]['can'] = true;
                    } else {
                        $incomeData[$key]['can'] = false;
                    }
                }
            } else {
                $incomeData[$key]['can'] = true;
            }

        }

//        if ($incomeData) {
//            return $this->successJson('获取数据成功!', $incomeData);
//        }
//        return $this->errorJson('未检测到数据!');


        return view('member.income', [
            'member' => $member,
            'levels' => $levels,
            'groups' => $groups,
            'incomeAll' => $incomeAll,
            'myform' => $myform,
//            'parent_name' => $parent_name,
            'item' => $incomeData
        ])->render();
    }
}