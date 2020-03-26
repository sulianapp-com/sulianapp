<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/6
 * Time: 上午9:26
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\frontend\modules\goods\services\GoodsService;
use app\frontend\modules\member\models\MemberFavorite;

class MemberFavoriteController extends ApiController
{
    public function index()
    {
        $memberId = \YunShop::app()->getMemberId();
        $favoriteList = MemberFavorite::getFavoriteList($memberId);

        if (!empty($favoriteList)) {
            foreach ($favoriteList as &$item) {
                $item['goods']['thumb'] = replace_yunshop(yz_tomedia($item['goods']['thumb']));
            }
        }
        return $this->successJson('成功', $favoriteList);
    }

    public function isFavorite($request, $integrated = null)
    {
        $memberId = \YunShop::app()->getMemberId();
        $goodsId = \YunShop::request()->goods_id;
        if(!$goodsId){
            $goodsId = \YunShop::request()->id;
        }
        if ($goodsId){
            if (MemberFavorite::getFavoriteByGoodsId($goodsId, $memberId)){
                $data = array(
                    'status' => 1,
                    'message' => '商品已收藏'
                );
            } else {
                $data = array(
                    'status' => 0,
                    'message' => '商品未收藏'
                );
            }
            if(is_null($integrated)){
                return $this->successJson('接口访问成功', $data);
            }else{
                return show_json(1,$data);
            }
        }

        if(is_null($integrated)){
            return $this->errorJson('未获取到商品ID');
        }else{
            return show_json(0,'未获取到商品ID');
        }
    }

    public function store()
    {
        if (\YunShop::request()->goods_id) {
            $memberId = \YunShop::app()->getMemberId();
            if (MemberFavorite::getFavoriteByGoodsId(\YunShop::request()->goods_id, $memberId)){
                return $this->errorJson('商品已收藏，不需要重复添加！');
            }
            $requestFaveorit = array(
                'member_id' => $memberId,
                //'member_id' => \YunShop::app()->getMemberId(),
                'goods_id' => \YunShop::request()->goods_id,
                'uniacid' => \YunShop::app()->uniacid
            );

            $favoriteModel = new MemberFavorite();

            $favoriteModel->setRawAttributes($requestFaveorit);
            $favoriteModel->uniacid = \YunShop::app()->uniacid;
            $validator = $favoriteModel->validator($favoriteModel->getAttributes());
            if ($validator->fails()) {
                return $this->errorJson($validator->messages());
            }
            if ($favoriteModel->save()) {
                return $this->successJson('添加收藏成功');
            }
            return $this->errorJson("数据写入出错，请重试！");
        }
        return $this->errorJson("未获取到商品ID");
    }


    public function destroy()
    {
        if (\YunShop::request()->goods_id) {
            $memberId = \YunShop::app()->getMemberId();
            $favoriteModel = MemberFavorite::getFavoriteByGoodsId(\YunShop::request()->goods_id, $memberId);
            if (!$favoriteModel) {
                return $this->errorJson("未找到记录或已删除");
            }
            if ($favoriteModel->delete()) {
                return $this->successJson("移除收藏成功");
            } else {
                return $this->errorJson("数据写入出错，移除收藏失败");
            }
        }
        return $this->errorJson("未获取到商品ID");
    }
}
