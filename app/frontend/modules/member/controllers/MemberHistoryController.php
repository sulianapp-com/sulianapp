<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/23
 * Time: 上午10:26
 */

namespace app\frontend\modules\member\controllers;

use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\events\member\MemberGoodsHistoryEvent;
use app\frontend\modules\member\models\MemberFavorite;
use app\frontend\modules\member\models\MemberHistory;


class MemberHistoryController extends ApiController
{
    public function index()
    {
        $memberId = \YunShop::app()->getMemberId();

        $historyList = MemberHistory::getMemberHistoryList($memberId);

        foreach ($historyList as &$value) {
            $value['goods']['thumb'] = yz_tomedia($value['goods']['thumb']);
        }
        return $this->successJson('获取列表成功', $historyList);
    }

    public function store($request, $integrated = null)
    {

        $memberId = \YunShop::app()->getMemberId();
        if( \YunShop::request()->id){
            $goodsId = \YunShop::request()->id ;
        }else{
            $goodsId = \YunShop::request()->goods_id ;
        }

        $owner_id = intval(request()->owner_id);
        if (!$goodsId) {
            if(is_null($integrated)){
                return $this->errorJson('未获取到商品ID，添加失败！');
            }else{
                return show_json(0,'未获取到商品ID，添加失败！');
            }
        }

        if(\YunShop::request()->mark && \YunShop::request()->mark_id)
        {
            event(new MemberGoodsHistoryEvent($goodsId,\YunShop::request()->mark,\YunShop::request()->mark_id));
        }

        $historyModel = MemberHistory::getHistoryByGoodsId($memberId, $goodsId) ?: new MemberHistory();

        $historyModel->goods_id = $goodsId;
        $historyModel->member_id = $memberId;
        $historyModel->uniacid = \YunShop::app()->uniacid;
        $historyModel->owner_id = $owner_id;
        if ($historyModel->save()) {
            if(is_null($integrated)){
                return $this->successJson('更新足迹成功');
            }else{
                return show_json(1,'更新足迹成功');
            }
        }
    }

    public function destroy()
    {
        $historyModel = MemberHistory::getHistoryById(\YunShop::request()->id);
        if (!$historyModel) {
            return $this->errorJson('未找到数据或已删除！');
        }
        if ($historyModel->delete()) {
            return $this->successJson('移除成功');
        }
        return $this->errorJson('未获取到历史记录ID');
    }

}
