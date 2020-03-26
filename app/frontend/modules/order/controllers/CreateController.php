<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/28
 * Time: 上午10:39
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\frontend\models\Member;
use app\frontend\modules\member\services\MemberCartService;
use app\frontend\modules\memberCart\MemberCartCollection;

class CreateController extends ApiController
{
    /**
     * @var MemberCartCollection
     */
    private $memberCarts;

    /**
     * @return static
     */
    protected function _getMemberCarts(){
        $goods_params = json_decode(request()->input('goods'), true);

        $memberCarts = collect($goods_params)->map(function ($memberCart) {
            return MemberCartService::newMemberCart($memberCart);
        });
        return $memberCarts;
    }

    /**
     * @return MemberCartCollection
     * @throws \app\common\exceptions\AppException
     */
    protected function getMemberCarts()
    {
        if(!isset($this->memberCarts)){

            $memberCarts = new MemberCartCollection($this->_getMemberCarts());
            $memberCarts->loadRelations();
            $memberCarts->validate();
            $this->memberCarts = $memberCarts;
        }

        return $this->memberCarts;
    }
    protected function validateParam(){

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function index()
    {
        \Log::info('用户下单', request()->input());
        $this->validateParam();

        //订单组
        $trade = $this->getMemberCarts()->getTrade(Member::current());
        $trade->generate();
        $orderIds = $trade->orders->pluck('id')->implode(',');
        //生成订单,触发事件

        return $this->successJson('成功', ['order_ids' => $orderIds]);
    }
}