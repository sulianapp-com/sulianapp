<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 上午10:44
 */

namespace app\backend\modules\member\controllers;


use app\backend\modules\goods\models\Goods;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberShopInfo;
use app\common\components\BaseController;
use app\common\facades\Setting;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;

class MemberLevelController extends BaseController
{
    /*
     * Member level pager list
     * 17.3,31 restructure
     *
     * @autor yitian */
    public function index()
    {
        //echo '<pre>'; print_r(Setting::get('shop.member')); exit;
        $pageSize = 10;
        $levelList = MemberLevel::getLevelPageList($pageSize);
        $pager = PaginationHelper::show($levelList->total(), $levelList->currentPage(), $levelList->perPage());

        return view('member.level.list', [
            'levelList' => $levelList,
            'pager' => $pager,
            'shopSet' => Setting::get('shop.member')
        ])->render();

    }

    public function searchGoods()
    {
        $goods = Goods::getGoodsByNameLevel(\YunShop::request()->keyword);
        foreach ($goods as $k => $v) {
            $goods[$k]['thumb'] = yz_tomedia($v['thumb']);
        }
        return view('member.goods_query', [
            'goods' => $goods,
        ])->render();
    }

    /*
     * Add member level
     *
     * @autor yitian */
    public function store()
    {
        $levelModel = new memberLevel();

        $requestLevel = \YunShop::request()->level;
        if($requestLevel) {
            //将数据赋值到model
            $levelModel->fill($requestLevel);
            //其他字段赋值
            $levelModel->uniacid = \YunShop::app()->uniacid;
            // if (!$levelModel->goods_id) {
            //     $levelModel->goods_id = 0;
            // }
            // $levelModel->validity = $requestLevel['validity'] ? $requestLevel['validity'] : 0;
            unset($levelModel->goods);
            unset($levelModel->goods_id);

            if ($requestLevel['goods'] ) {

                foreach ($requestLevel['goods'] as $k => $v) {
                    
                    if ($v['goods_id']) {

                        $arr[] = $v['goods_id'];
                    }
                }
            } else {
                $arr[] = [];
            }
            
            if (empty($requestLevel['goods_id'])) {
                
                $levelModel->goods_id = implode(',', array_unique($arr));

            } else {
                $ids = implode(',', array_unique(array_merge(array_filter($arr), array_values($requestLevel['goods_id']))));  
                $levelModel->goods_id = $ids;
            }
            //字段检测
            $validator = $levelModel->validator();
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                //数据保存
                if ($levelModel->save()) {
                    //显示信息并跳转
                    return $this->message('添加会员等级成功', Url::absoluteWeb('member.member-level.index'));
                }else{
                    $this->error('添加会员等级失败');
                }
            }
        }

        return view('member.level.form', [
            'level' => $levelModel,
            'shopSet' => Setting::get('shop.member')
        ])->render();
    }
    /**
     * Modify membership level
     */
    public function update()
    {
        $levelModel = MemberLevel::getMemberLevelById(\YunShop::request()->id);
        if(!$levelModel){
            return $this->message('无此记录或已被删除','','error');
        }
        $requestLevel = \YunShop::request()->level;

        $goods = MemberLevel::getGoodsId($levelModel['goods_id']);

        if($requestLevel) {
            $levelModel->fill($requestLevel);

            if ($requestLevel['goods'] || $requestLevel['goods_id']) {
                unset($levelModel->goods);
                unset($levelModel->goods_id);

                if ($requestLevel['goods']) {

                    foreach ($requestLevel['goods'] as $k => $v) {

                        if ($v['goods_id']) {

                            $arr[] = $v['goods_id'];
                        }
                    }
                } else {
                    $arr[] = '';
                }

                if (empty($requestLevel['goods_id'])) {

                    $levelModel->goods_id = implode(',', array_unique($arr));

                } else {
                    $ids = implode(',', array_unique(array_merge(array_filter($arr), array_values($requestLevel['goods_id']))));
                    $levelModel->goods_id = $ids;
                }
            }

            $validator = $levelModel->validator();
            if ($validator->fails()) {//检测失败
                $this->error($validator->messages());
            } else {
                if ($levelModel->save()) {
                    return $this->message('修改会员等级信息成功', Url::absoluteWeb('member.member-level.index'));
                }else{
                    $this->error('修改会员等级信息失败');
                }
            }
        }
        return view('member.level.form', [
            'levelModel' => $levelModel,
            'goods' => $goods ? $goods->toArray() : [],
            'shopSet' => Setting::get('shop.member')
        ])->render();
    }
    /*
     * Delete membership
     *
     * @author yitain */
    public function destroy()
    {
        $levelModel = MemberLevel::getMemberLevelById(\YunShop::request()->id);
        if(!$levelModel) {
            return $this->message('未找到记录或已删除','','error');
        }
        if($levelModel->delete()) {
            MemberShopInfo::where('level_id',\YunShop::request()->id)->update(['level_id'=>'0']);
            return $this->message('删除等级成功',Url::absoluteWeb('member.member-level.index'));
        }else{
            return $this->message('删除等级失败','','error');
        }
    }



}