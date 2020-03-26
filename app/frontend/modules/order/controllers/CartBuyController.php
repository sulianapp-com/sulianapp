<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/4/11
 * Time: 上午10:52
 */

namespace app\frontend\modules\order\controllers;

use app\common\components\ApiController;
use app\common\exceptions\AppException;
use app\frontend\modules\memberCart\MemberCartCollection;


class CartBuyController extends ApiController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     * @throws \app\common\exceptions\ShopException
     */
    public function index()
    {
        $this->validateParam();
        $trade = $this->getMemberCarts()->getTrade();
        return $this->successJson('成功', $trade);
    }
    /**
     * @throws \app\common\exceptions\ShopException
     */
    protected function validateParam()
    {
        $this->validate([
            'cart_ids' => 'required',
        ]);
    }

    /**
     * 从url中获取购物车记录并验证
     * @return MemberCartCollection
     * @throws AppException
     */
    protected function getMemberCarts()
    {
        static $memberCarts;
        $cartIds = [];
        if (!is_array($_GET['cart_ids'])) {
            $cartIds = explode(',', $_GET['cart_ids']);
        }
        $cartIds = array_slice($cartIds, 0, 50);
        if (!count($cartIds)) {
            throw new AppException('参数格式有误');
        }
        if (!isset($memberCarts)) {
            $memberCarts = app('OrderManager')->make('MemberCart')->whereIn('id', $cartIds)->get();

            $memberCarts = new MemberCartCollection($memberCarts);
            $memberCarts->loadRelations();
        }

        $memberCarts->validate();
        if ($memberCarts->isEmpty()) {
            throw new AppException('未找到购物车信息');
        }

        if ($memberCarts->isEmpty()) {

            throw new AppException('请选择下单商品');
        }
        return $memberCarts;
    }
}