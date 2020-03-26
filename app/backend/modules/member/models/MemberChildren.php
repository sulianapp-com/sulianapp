<?php
/**
 * Created by PhpStorm.
 * User: BC
 * Date: 2018/11/20
 * Time: 22:55
 */

namespace app\backend\modules\member\models;


use app\backend\modules\charts\models\Order;
use app\backend\modules\charts\modules\team\models\MemberMonthOrder;
use Illuminate\Support\Facades\DB;

class MemberChildren extends \app\common\models\member\MemberChildren
{
    public function scopeChildren($query, $request)
    {
        $query->where('member_id', $request->id)->with([
            'hasOneMember' => function($q) {
                $q->select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime', 'credit1', 'credit2']);
            },
            'hasOneFans',
            'hasOneChild' => function($q) {
                $q->selectRaw('count(child_id) as first, member_id')->where('level', 1)->groupBy('member_id');
            }
        ]);

        if ($request->level) {
            $query->where('level', $request->level);
        } else {
            $query->where('level', 1);
        }

        if ($request->member_id) {
            $query->where('parent_id', $request->member_id);
        }

        if ($request->member) {
            $query->whereHas('hasOneMember', function ($q) use ($request) {
                $q->searchLike($request->member);
            });
        }

        if ($request->followed != '') {
            $query->whereHas('hasOneFans', function ($q) use ($request) {
                $q->where('follow', $request->followed);
            });
        }
        return $query;

    }

    public static function getTeamCount($search,$uniacid)
    {
        $teamModel=DB::table('yz_member_children')
            ->where('yz_member_children.uniacid',$uniacid)
            ->where('yz_member_children.level','<',3)
            ->leftJoin('mc_members',function ($join){
                $join->on('yz_member_children.member_id', '=', 'mc_members.uid');
            } );
        if(!empty($search['member_id'])){
            $teamModel ->where('mc_members'.'.uid',$search['member_id']);
        };
        if(!empty($search['nickname'])){
            $teamModel ->where('mc_members'.'.nickname','like','%'.$search['nickname'].'%');
        };
        if(!empty($search['realname'])){
            $teamModel ->where('mc_members'.'.realname','like','%'.$search['realname'].'%');
        };
        if(!empty($search['mobile'])){
            $teamModel   ->where('mc_members'.'.mobile',$search['mobile']);
        };
          $teamModel->leftJoin('yz_member_month_order',function ($join) use ($search){
                $join->on('yz_member_children.child_id', '=', 'yz_member_month_order.member_id')
                    ->where('yz_member_month_order.year',$search['year'])
                    ->where('yz_member_month_order.month',$search['month']);
                 } )
             ->select(DB::raw('ims_yz_member_children.*,ims_mc_members.avatar,ims_mc_members.nickname,ims_mc_members.realname,ims_mc_members.mobile,ims_yz_member_month_order.order_price,ims_yz_member_month_order.order_price, ims_yz_member_month_order.member_id as uid,SUM(CASE WHEN ims_yz_member_children.level<3 THEN 1 ELSE 0 END) as level_num,SUM(ims_yz_member_month_order.order_num) as order_all,SUM(ims_yz_member_month_order.order_price) as price_all'))
             ->groupBy('yz_member_children.member_id')
              ->havingRaw('SUM(ims_yz_member_month_order.order_price) != 0')
             ->orderBy('price_all', 'desc');
        return $teamModel;
    }

    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'child_id');
    }

    public function hasOneFans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'child_id');
    }

    public function hasOneChild()
    {
        return $this->hasOne(self::class, 'member_id', 'child_id');
    }

    public function hasManyOrder()
    {
        return $this->hasMany('\app\common\models\Order','uid','child_id');
    }

    public function hasManyMonth()
    {
        return $this->hasMany('\app\common\models\member\MemberMonthOrder','member_id','child_id');
    }

}