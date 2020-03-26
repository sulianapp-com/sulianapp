<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/23
 * Time: 下午6:08
 */

namespace app\backend\modules\member\controllers;


use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\member\models\MemberShopInfo;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;

class MemberGroupController extends BaseController
{
    /*
     * Member group pager list
     * 17.3,31 restructure
     *
     * @autor yitian */
    public function index()
    {
        $pageSize = 20;
        $groupList = MemberGroup::getGroupPageList($pageSize);
        $pager = PaginationHelper::show($groupList->total(), $groupList->currentPage(), $groupList->perPage());
        //echo '<pre>'; print_r($groupList->toArray()); exit;
        return view('member.group.list', [
            'groupList' => $groupList,
            'pager' => $pager
        ])->render();
    }
    /*
     * Add member group
     * 17.3,31 restructure
     *
     * @autor yitian */
    public function store()
    {
        $groupModel = new MemberGroup();

        $requestGroup = \YunShop::request()->group;
        if ($requestGroup) {
            $groupModel->setRawAttributes($requestGroup);
            $groupModel->uniacid = \YunShop::app()->uniacid;

            $validator = $groupModel->validator($groupModel->getAttributes());
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($groupModel->save()) {
                    return $this->message("添加会员分组成功",Url::absoluteWeb('member.member-group.index'),'success');
                } else {
                    $this->error("添加会员分组失败");
                }
            }
        }
        return view('member.group.form', [
            'groupModel' => $groupModel
        ])->render();
    }
    /*
     *  Update member group
     * */
    public function update()
    {
        $groupModel = MemberGroup::getMemberGroupByGroupId(\YunShop::request()->group_id);
        if(!$groupModel) {
            return $this->message('未找到会员分组或已删除', Url::absoluteWeb('member.member-group.index'));
        }
        $requestGroup = \YunShop::request()->group;
        if ($requestGroup) {
            $groupModel->setRawAttributes($requestGroup);

            $validator = $groupModel->validator($requestGroup);
            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($groupModel->save()) {
                    return $this->message('修改会员分组信息成功。', Url::absoluteWeb('member.member-group.index'));
                } else {
                    $this->error('修改会员分组信息失败！！！');
                }
            }
        }
        return view('member.group.form', [
            'groupModel' => $groupModel
        ])->render();
    }
    /*
     * Destory member group
     *
     * */
    public function destroy()
    {
        $groupModel = MemberGroup::getMemberGroupByGroupId(\YunShop::request()->group_id);
        if (!$groupModel) {
            $this->error('未找到会员分组或已删除', Url::absoluteWeb('member.member-group.index'));
        }
        if ($groupModel->delete()) {
            MemberShopInfo::where('group_id',\YunShop::request()->id)->update(['group_id'=>'0']);
            return $this->message("删除会员分组成功。", Url::absoluteWeb('member.member-group.index'));
        } else {
            $this->error("删除会员分组失败");
        }
    }
}
