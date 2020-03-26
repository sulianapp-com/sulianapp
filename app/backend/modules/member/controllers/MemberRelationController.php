<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 17/3/8
 * Time: 上午10:11
 */

namespace app\backend\modules\member\controllers;

use app\backend\modules\member\models\Member;
use app\backend\modules\member\models\MemberRelation;
use app\backend\modules\member\models\MemberShopInfo;
use app\common\components\BaseController;
use app\backend\modules\member\models\MemberRelation as Relation;
use app\common\facades\Setting;
use app\common\helpers\Cache;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Goods;
use app\common\models\notice\MessageTemp;


class MemberRelationController extends BaseController
{
    public $pageSize = 20;

    /**
     * 列表
     * @return string
     * @throws \Throwable
     */
    public function index()
    {
        $relation = Relation::uniacid()->first();
        $setting = \Setting::get('member.relation');

        if (!empty($relation)) {
            $relation = $relation->toArray();
        }

        if (!empty($relation['become_term'])) {
            $relation['become_term'] = unserialize($relation['become_term']);
        }
        if (!empty($relation['become_goods'])) {
            $goods = unserialize($relation['become_goods']);

//            $goods = Goods::getGoodsById($relation['become_goods_id']);
//
//            if (!empty($goods)) {
//                $goods = $goods->toArray();
//            } else {
//                $goods = [];
//            }

        } else {
            $goods = [];
        }
//        if (!empty($relation['become_goods_id'])) {
//            $relation['become_goods_id'] = explode(',',$relation['become_goods_id']);
//        }

        return view('member.relation', [
            'set' => $relation,
            'setting' => $setting,
            'goods' => $goods,
        ])->render();
    }

    /**
     * 保存关系链数据
     *
     * @return mixed
     */
    public function save()
    {
        $setData = \YunShop::request()->setdata;
        $setting = \YunShop::request()->setting;
        if ($setting) {
            \Setting::set('member.relation',$setting);
        }
        $setData['uniacid'] = \YunShop::app()->uniacid;

        if (empty($setData['become_ordercount'])) {
            $setData['become_ordercount'] = 0;
        }

        if (!empty($setData['become_term'])) {
            $setData['become_term'] = serialize($setData['become_term']);
        }

        if (empty($setData['become_moneycount'])) {
            $setData['become_moneycount'] = 0;
        }

        $setData['become_goods_id'] = !empty($setData['become_goods_id']) ? implode(',',$setData['become_goods_id']) : 0;

        $setData['become_goods'] = !empty($setData['become_goods']) ? serialize($setData['become_goods']) : 0;

        if (empty($setData['become_selfmoney'])) {
            $setData['become_selfmoney'] = 0;
        }

        $relation = Relation::uniacid()->first();

        if (!empty($relation)) {
            $relation->setRawAttributes($setData);
            (new \app\common\services\operation\RelationLog($relation, 'update'));
            $relation->save();
        } else {
            Relation::create($setData);
        }

        Cache::forget('member_relation');
        return $this->message('保存成功', yzWebUrl('member.member-relation.index'));

    }

    /**
     * 成为推广员 指定商品查询
     *
     * @return string
     */
    public function query()
    {
        $kwd = trim(\YunShop::request()->keyword);

        $goods_model= Goods::getGoodsByName($kwd);

        if (!empty($goods_model)) {
            $data = $goods_model->toArray();
            foreach ($data as &$good) {
                $good['thumb'] = tomedia($good['thumb']);
            }
        } else {
            $data = [];
        }

        return view('member.goods_query', [
            'goods' => $data
        ])->render();
    }

    /**
     * 会员资格申请列表
     *
     * @return string
     */
    public function apply()
    {
        $starttime = strtotime('-1 month');
        $endtime = time();

        $requestSearch = \YunShop::request()->search;

        if (isset($requestSearch['searchtime']) && $requestSearch['searchtime'] == 1) {
            if ($requestSearch['times']['start'] != '请选择' && $requestSearch['times']['end'] != '请选择') {
                $starttime = strtotime($requestSearch['times']['start']);
                $endtime = strtotime($requestSearch['times']['end']);
            }
        }

        $list = Member::getMembersToApply($requestSearch)
            ->paginate($this->pageSize)
            ->toArray();

        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('member.apply', [
            'list' => $list,
            'total' => $list['total'],
            'pager' => $pager,
            'requestSearch' => $requestSearch,
            'starttime' => $starttime,
            'endtime' => $endtime,
        ])->render();
    }

    /**
     * 申请协议
     *
     * @return mixed|string
     */
    public function applyProtocol()
    {
        $info = Setting::get("apply_protocol");

        $requestProtocol = \YunShop::request()->protocol;
        if($requestProtocol){
            $request = Setting::set('apply_protocol',$requestProtocol);
            if($request){
                return $this->message('保存成功', Url::absoluteWeb('member.member-relation.apply-protocol'));
            }
        }
        
        return view('member.apply-protocol', [
            'info' => $info,
        ])->render();
    }

    public function base()
    {
        $info = \Setting::get('relation_base');

        $base = \YunShop::request()->base;

        if($base){
            $request = Setting::set('relation_base',$base);
            if($request){
                return $this->message('保存成功', Url::absoluteWeb('member.member-relation.base'));
            }
        }

        $temp_list = MessageTemp::getList();

        return view('member.relation-base', [
            'banner'  => tomedia($info['banner']),
            'content' => $info['content'],
            'base'      => $info,
            'temp_list' => $temp_list,
            'relation_level' => $info['relation_level']
        ])->render();
    }

    /**
     * 检查审核
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function chkApply()
    {
        $id = \YunShop::request()->id;

        $member_shop_info_model = MemberShopInfo::getMemberShopInfo($id);

        if ($member_shop_info_model) {
            $member_shop_info_model->is_agent = 1;
            $member_shop_info_model->status = 2;

            if ($member_shop_info_model->inviter == 0) {
                $member_shop_info_model->inviter = 1;
            }

            if ($member_shop_info_model->save()) {
                Member::setMemberRelation($member_shop_info_model->member_id, $member_shop_info_model->parent_id);

                Relation::sendGeneralizeNotify($member_shop_info_model->member_id);

                return $this->successJson('审核通过');
            } else {
                return $this->errorJson('审核失败');
            }
        } else {
            return $this->errorJson('会员不存在');
        }
    }

    /**
     * 数据导出
     *
     */
    public function export()
    {
        $file_name = date('Ymdhis', time()) . '会员资格申请导出';

        $requestSearch = \YunShop::request()->search;

        $list = Member::getMembersToApply($requestSearch)
            ->get()
            ->toArray();

        $export_data[0] = ['会员ID', '推荐人姓名', '粉丝姓名', '会员姓名', '手机号', '申请时间'];

        foreach ($list as $key => $item) {
            if (!empty($item['yz_member']) && !empty($item['yz_member']['agent'])) {
                $agent_name = $item['yz_member']['agent']['nickname'];

            } else {
                $agent_name = '';
            }


            $export_data[$key + 1] = [$item['uid'], $agent_name, $item['nickname'], $item['realname'],
                $item['mobile'], date('Y.m.d', $item['apply_time'])];
        }

        \Excel::create($file_name, function ($excel) use ($export_data) {
            // Set the title
            $excel->setTitle('Office 2005 XLSX Document');

            // Chain the setters
            $excel->setCreator('芸众商城')
                ->setLastModifiedBy("芸众商城")
                ->setSubject("Office 2005 XLSX Test Document")
                ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
                ->setKeywords("office 2005 openxml php")
                ->setCategory("report file");

            $excel->sheet('info', function ($sheet) use ($export_data) {
                $sheet->rows($export_data);
            });


        })->export('xls');
    }
}