<?php


namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\models\Member;
use app\common\models\Order;
use app\common\modules\memberCart\PreMemberCart;
use app\framework\Http\Request;
use app\frontend\modules\memberCart\MemberCartCollection;

class RequestController extends BaseController
{
    private $order;
    private $member;

    public function index()
    {

        dd($this->order()->orderRequest->request);
    }

    private function order()
    {
        if (!isset($this->order)) {

            $this->order = Order::find(request()->input('order_id'));
        }
        return $this->order;
    }

    private function uid()
    {
        if (request()->input('uid')) {
            return request()->input('uid');
        }
        return $this->order()->uid;
    }

    public function member()
    {
        if (!isset($this->member)) {

            $this->member = Member::find($this->uid());
        }
        return $this->member;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \app\common\exceptions\AppException
     */
    public function reappear()
    {
        $request = new Request($this->order()->orderRequest->request);
        $goods = json_decode($request->input('goods'), true);
        $trade = $this->getMemberCarts($goods)->getTrade($this->order()->belongsToMember, $request);
        return $this->successJson('成功', $trade);
    }

    /**
     * @param $goods
     * @return MemberCartCollection
     */
    private function getMemberCarts($goods)
    {
        app('OrderManager')->bind('MemberCart', function ($manager, $params) {
            return new PreMemberCart($params);
        });

        $result = new MemberCartCollection();

        foreach ($goods as $memberCart) {
            $memberCart['member_id'] = $this->uid();
            $cart = app('OrderManager')->make('MemberCart', $memberCart);
            $result->push($cart);
        }

        return $result;
    }

}