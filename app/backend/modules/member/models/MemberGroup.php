<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/23
 * Time: 下午6:04
 */

namespace app\backend\modules\member\models;


use Illuminate\Validation\Rule;

class MemberGroup extends \app\common\models\MemberGroup
{

    static protected $needLog = true;

    public $guarded = [''];

    //关联 member 数据表 一对多
    public function member()
    {
        return $this->hasMany('app\backend\modules\member\models\MemberShopInfo','group_id','id');
    }

    /*
     * 获取会员分页列表 17.3.31 auto::yitian
     *
     * @param int $pageSize
     *
     * @return object */
    public static function getGroupPageList($pageSize)
    {
        return self::uniacid()
            ->with(['member' => function($query){
                return $query->select(['member_id','group_id'])->where('uniacid', \YunShop::app()->uniacid);
            }])
            ->paginate($pageSize);
    }

    /**
     *  Get membership information through member group ID
     *
     * @param int $groupId
     *
     * @return array */
    public static function getMemberGroupByGroupId($groupId)
    {
        return  MemberGroup::where('id', $groupId)->first();
    }
    /**
     * Get a list of members of the current public number
     *
     * @param int $uniacid
     *
     * @return array */
    public static function getMemberGroupList()
    {
        $memberGroup = MemberGroup::select('id', 'group_name', 'uniacid')
            ->uniacid()
            ->with(['member' => function($query){
                return $query->select(['member_id','group_id'])->where('uniacid', \YunShop::app()->uniacid);
            }])
            ->get()
            ->toArray();
        return $memberGroup;
    }

    /**
     * 获取指定"GroupId"下的关联用户数据
     * @param $groupId
     * @return mixed
     */
    public static function getMembersByGroupId($groupId)
    {
        $memberGroup = static::uniacid()
                    ->select('id', 'group_name')
                    ->where('id', '=', $groupId)
                    ->with(['member' => function($query){
                        return $query->select(['member_id','group_id'])->where('uniacid', \YunShop::app()->uniacid);
                    }])
                    ->first();
        return $memberGroup;
    }

    /**
     * Delete member list
     *
     * @param int $groupId
     *
     * @return 1 or 0
     **/
    public static function deleteMemberGroup($groupId)
    {
        return static::where('id', $groupId)->delete();
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'group_name'    => '分组名',
            'uniacid'  => '公众号ID',
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'group_name'    => [
                'required',
                Rule::unique($this->table)->where('uniacid', \YunShop::app()->uniacid)->where('deleted_at','')->ignore($this->id),
                'max:45'
                ],

            'uniacid'       => 'required'
        ];
    }
}
