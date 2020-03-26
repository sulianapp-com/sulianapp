<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2018/10/22
 * Time: 下午5:29
 */

namespace app\common\models\member;


use app\common\models\BaseModel;
use Illuminate\Support\Facades\DB;

class ParentOfMember extends BaseModel
{
    public $table = 'yz_member_parent';
    protected $guarded = [];
    private $uniacid = 0;
    private $parents = [];

    public function __construct(array $attributes = [])
    {
        $this->uniacid = \YunShop::app()->uniacid;

        parent::__construct($attributes);
    }

    public function CreateData($data)
    {
        \Log::debug('----------insert data-----');
        $rs = DB::table($this->getTable())->insert($data);

        return $rs;
    }

    public function DeletedData($uniacid = 0)
    {
        \Log::debug('----------DeletedData Parent--------');
        return DB::table($this->getTable())->where('uniacid', $uniacid)->delete();
    }

    public function getParentOfMember($uid)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->get();
    }

    public function getMemberIdByParent($parent_id)
    {
        return self::uniacid()
            ->where('parent_id', $parent_id)
            ->pluck('member_id');
    }

    public function delRelationOfParentByMemberId($parent_id, $uid)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->where('parent_id', $parent_id)
            ->delete();
    }

    public function delRelation($member_ids)
    {
        return self::uniacid()
            ->whereIn('member_id', $member_ids)
            ->delete();
    }

    public function hasParentOfMember($uid, $parent, $level)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->where('parent_id', $parent)
            ->where('level', $level)
            ->count();
    }

    public function addNewParentData($uid, $parent_id)
    {
        $attr = [];
        $depth = 1;
        $parents = $this->getParentOfMember($parent_id);

        $default_exists = $this->hasParentOfMember($uid, $parent_id, $depth);

        if (!$default_exists) {
            \Log::debug('------parent level------', [$depth]);

            $attr[] = [
                'uniacid'   => $this->uniacid,
                'parent_id' => $parent_id,
                'level'     => $depth,
                'member_id' => $uid,
                'created_at' => time()
            ];
        }


        if (!empty($parents)) {
            foreach ($parents as $key => $val) {
                $level = ++$val['level'];
                $parent_exists = $this->hasParentOfMember($uid, $val['parent_id'], $level);

                if (!$parent_exists) {
                    \Log::debug('------parent level------', [$level]);

                    $attr[] = [
                        'uniacid'   => $this->uniacid,
                        'parent_id' => $val['parent_id'],
                        'level'     => $level,
                        'member_id' => $uid,
                        'created_at' => time()
                    ];
                }

            }
        }

        $this->CreateData($attr);
    }

    public function delMemberOfRelation(ChildrenOfMember $childObj, $uid, $n_parent_id)
    {
        $parents = $this->getParentOfMember($uid);
        $childs = $childObj->getChildOfMember($uid);

        //删除重新分配节点本身在父表中原父级的记录
        if (!$parents->isEmpty()) {
            foreach ($parents as $val) {
                $this->delRelationOfParentByMemberId($val['parent_id'], $val['member_id']);
            }
        }

        //删除重新分配节点的子级在父表中原父级的记录
        if (!$childs->isEmpty()) {
            foreach ($parents as $val) {
                foreach ($childs as $rows) {
                    $this->delRelationOfParentByMemberId($val['parent_id'], $rows['child_id']);
                }
            }
        }

        //可优化
        if ($n_parent_id > 0) {
            //删除重新分配节点的所有子级(新节点中层级已改变)
            $member_ids = $this->getMemberIdByParent($uid);

            if (!$member_ids->isEmpty()) {
                $this->delRelation($member_ids);
            }
        }
    }

    public function hasRelationOfParent($uid, $depth)
    {
        return $this->getRelationOfParent($uid, $depth);

    }

    public function getParentsOfMember($uid)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->get();
    }

    public function getParents($uid)
    {
        $parents = $this->getParentsOfMember($uid);

        if (!is_null($parents)) {
            foreach ($parents as $val) {
                $this->parents[] = $val['parent_id'];
            }
        }
    }

    public function fixParentData($uid, $parent_id)
    {
        $attr = [];
        $depth = 1;
        $parents = $this->getParentOfMember($parent_id);

        $default_exists = $this->hasParentOfMember($uid, $parent_id, $depth);

        if (!$default_exists) {
            echo '------parent level------' . $depth . '<BR>';
            $attr[] = [
                'uniacid'   => $this->uniacid,
                'parent_id' => $parent_id,
                'level'     => $depth,
                'member_id' => $uid,
                'created_at' => time()
            ];
        }


        if (!empty($parents)) {
            foreach ($parents as $key => $val) {
                $level = ++$val['level'];
                $parent_exists = $this->hasParentOfMember($uid, $val['parent_id'], $level);

                if (!$parent_exists) {
                    echo '------parent level------' . $level . '<BR>';
                    $attr[] = [
                        'uniacid'   => $this->uniacid,
                        'parent_id' => $val['parent_id'],
                        'level'     => $level,
                        'member_id' => $uid,
                        'created_at' => time()
                    ];
                }

            }
        }

        $this->CreateData($attr);
    }

    public function getRelationOfParent($uid, $depth)
    {
        return self::uniacid()
            ->where('member_id', $uid)
            ->where('level', $depth)
            ->get();
    }
}