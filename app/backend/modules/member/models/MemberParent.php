<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/11/19
 * Time: 10:12
 */

namespace app\backend\modules\member\models;


class MemberParent extends \app\common\models\member\MemberParent
{
    public function scopeParent($query, $request)
    {
        $query->where('member_id', $request->id)->with([
            'hasOneMember' => function($q) {
                $q->select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime', 'credit1', 'credit2']);
            },
            'hasOneFans'
        ]);
        if (app('plugins')->isEnabled('team-dividend')) {
            $query->with(['hasOneTeamDividend' => function($q) {
                $q->with(['hasOneLevel']);
            }]);
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

    public function scopeChildren($query, $request)
    {
        $query->where('parent_id', $request->id)->with([
            'hasOneChildMember' => function($q) {
                $q->select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime', 'credit1', 'credit2']);
            },
            'hasOneChildFans',
            'hasOneLower' => function($q) {
                $q->selectRaw('count(member_id) as first, parent_id')->where('level', 1)->groupBy('parent_id');
            }
        ]);

        if ($request->level) {
            $query->where('level', $request->level);
        }
//        else {
//            $query->where('level', 1);
//        }

        if ($request->aid) {
            $query->where('member_id', $request->aid);
        }

        if ($request->keyword) {
            $query->whereHas('hasOneChildMember', function ($q) use ($request) {
                $q->searchLike($request->keyword);
            });
        }

        if ($request->followed != '') {
            $query->whereHas('hasOneChildFans', function ($q) use ($request) {
                $q->where('follow', $request->followed);
            });
        }
        return $query;

    }


    public static function getParentByMemberId($request)
    {
        $query = self::where('member_id', $request->id)->with([
            'hasOneMember' => function($q) {
                $q->select(['uid', 'avatar', 'nickname', 'realname', 'mobile', 'createtime', 'credit1', 'credit2']);
            },
            'hasOneFans'
        ]);

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


    public function hasOneMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'parent_id');
    }

    public function hasOneChild()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }

    public function hasOneFans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'parent_id');
    }

    public function hasOneTeamDividend()
    {
        return $this->hasOne('Yunshop\TeamDividend\models\TeamDividendAgencyModel', 'uid', 'parent_id');
    }


    public function hasOneChildMember()
    {
        return $this->hasOne('app\common\models\Member', 'uid', 'member_id');
    }

    public function hasOneChildFans()
    {
        return $this->hasOne('app\common\models\McMappingFans', 'uid', 'member_id');
    }

    public function hasOneLower()
    {
        return $this->hasOne(self::class, 'parent_id', 'member_id');
    }

    public function hasManyParent()
    {
        return $this->hasMany(self::class, 'member_id', 'parent_id');
    }


}