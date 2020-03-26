<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/11/21 上午11:14
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\backend\modules\finance\controllers;


use app\backend\modules\member\models\Member;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\facades\Setting;
use app\common\helpers\Url;
use app\common\models\finance\PointLoveSet;

class PointLoveController extends BaseController
{
    public function index()
    {

        return view('finance.point.point_love',[
            'memberModel' => $this->getMemberModel(),
            'love_name' => $this->getLoveName()
        ])->render();
    }


    public function update()
    {
        $member_id = $this->getPostMemberId();

        $_model = PointLoveSet::where('member_id',$member_id)->first();
        !$_model && $_model = new PointLoveSet();

        $_model->rate = trim(trim(\YunShop::request()->rate));
        $_model->transfer_love = trim(trim(\YunShop::request()->transfer_love));
        $_model->transfer_integral = trim(trim(\YunShop::request()->transfer_integral));
        $_model->member_id = $member_id;
        $_model->uniacid = \YunShop::app()->uniacid;

        $validator = $_model->validator();
        if ($validator->fails()) {

            $this->error($validator->messages()->first());

        } else {

            $result = $_model->save();
            if ($result) {
                return $this->message('修改成功',Url::absoluteWeb('finance.point-love.index',['member_id' => $member_id]));
            }
            $this->error('数据储存失败，请重试');
        }



        return $this->index();
    }




    private function getLoveName()
    {
        $love_name = Setting::get('love.name');

        return $love_name ? $love_name : '爱心值';
    }




    private function getMemberModel()
    {
        $_model =  Member::select('uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime')
            ->where('uid',$this->getPostMemberId())
            ->with('pointLove')
            ->first();
        if (!$_model) {
            throw new ShopException('数据错误，请刷新重试');
        }
        return $_model;
    }




    private function getPostMemberId()
    {
        return \YunShop::request()->member_id;
    }

}
