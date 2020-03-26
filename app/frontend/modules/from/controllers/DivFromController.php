<?php
/****************************************************************
 * Author:  libaojia
 * Date:    2017/8/25 下午1:51
 * Email:   livsyitian@163.com
 * QQ:      995265288
 * User:    芸众商城 www.yunzshop.com
 ****************************************************************/

namespace app\frontend\modules\from\controllers;


use app\common\components\ApiController;
use app\common\services\DivFromService;
use app\common\services\IDCardService;
use app\frontend\models\Member;

class DivFromController extends ApiController
{
    /**
     * 商品表单是否显示
     * @return \Illuminate\Http\JsonResponse
     */
    public function isDisplay($request, $integrated = null)
    {
        $goods_ids = json_decode(request()->input('goods_ids'),true);


        if (!is_array($goods_ids)) {
            if(is_null($integrated)){
                return $this->errorJson('未获取到商品ID集');
            }else{
                return show_json(0,'未获取到商品ID');
            }
        }
        $status = DivFromService::isDisplay($goods_ids,\YunShop::app()->getMemberId());
        if(is_null($integrated)){
            return $this->successJson('ok',['status'=> $status,'member_status'=>DivFromService::getMemberStatus(\YunShop::app()->getMemberId())]);
        }else{
            return show_json(1,['status'=> $status,'member_status'=>DivFromService::getMemberStatus(\YunShop::app()->getMemberId())]);
        }

    }

    /**
     * 商品表单规则说明
     * @return \Illuminate\Http\JsonResponse
     */
    public function explain($request, $integrated = null)
    {
        $explain = array_pluck(\Setting::getAllByGroup('div_from')->toArray(), 'value', 'key');
        if (is_null($integrated)){
            return $this->successJson('ok',$explain );
        }else{
            return show_json(1,$explain);
        }

    }

    //判断是否开启发票
    public function isinvoice($request, $integrated = null)
    {

        $trade = \Setting::get('shop.trade');
        $invoice['papery'] = $trade['invoice']['papery']!=0 ? $trade['invoice']['papery'] :0;
        $invoice['electron'] = $trade['invoice']['electron']!=0 ? $trade['invoice']['electron'] :0;
        if(is_null($integrated)){
            return $this->successJson('ok',['invoice'=>$invoice]);
        }else{
            return show_json(1,['invoice'=>$invoice]);
        }

    }
    /**
     * 修改会员真实姓名、身份证ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMemberInfo()
    {
        $member_name = \YunShop::request()->member_name;
        if (!$member_name) {
            return $this->errorJson('会员真实名称不能为空');
        }

        $member_card = \YunShop::request()->member_card;
        if (!$member_card) {
            return $this->errorJson('会员身份证号码不能为空');
        }
        if (!IDCardService::isCard($member_card)) {
            return $this->errorJson('请输入正确的身份证号码');
        }

        if (!\YunShop::app()->getMemberId()) {
            return $this->errorJson('未获取到会员ID');
        }
        Member::where('uid',\YunShop::app()->getMemberId())->update(['realname'=>$member_name,'idcard'=>$member_card]);

        return $this->successJson('ok');
    }

    public function getMemberInfo($request, $integrated = null)
    {
        $MemberInfo = DivFromService::getMemberCardAndName(\YunShop::app()->getMemberId());
        if (!$MemberInfo) {
            if(is_null($integrated)){
                return $this->errorJson('未获取到会员信息！！');
            }else{
                return show_json(0,'未获取到会员信息！！');
            }

        }
        if(is_null($integrated)){
            return $this->successJson('ok',$MemberInfo);
        }else{
            return show_json(1,$MemberInfo);
        }

    }

    public function getParams($request)
    {
        $this->dataIntegrated($this->isinvoice($request, true), 'sinvoice');
        $this->dataIntegrated($this->isDisplay($request, true), 'isDisplay');
        $this->dataIntegrated($this->getMemberInfo($request, true), 'getMemberInfo');
        $this->dataIntegrated($this->explain($request, true), 'explain');
        $this->dataIntegrated(\app\frontend\modules\shop\controllers\IndexController::getPayProtocol($request, true),'getPayProtocol');
        return $this->successJson('', $this->apiData);
    }
}