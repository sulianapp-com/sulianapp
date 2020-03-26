<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 16/03/2017
 * Time: 08:58
 */

namespace app\common\traits;


use app\common\services\member\MemberRelation;
use ArrayAccess;
use BadMethodCallException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class TreeTrait
 *
 * 使用
 * <?php
 * namespace app\common\models;
 * use app\common\traits\TreeTrait;
 *
 * class MyClass
 * {
 *      use TreeTrait;
 *
 *      // 自定义属性（可选）
 *      protected $treeNodeIdName = 'id';
 *      protected $treeNodeParentIdName = 'parent_id';
 * /**
 *  * 获取待处理的原始节点数据
 *  *
 *  * 必须实现
 *  *
 *  * return \Illuminate\Support\Collection
 *  *
 *      public function getTreeAllNodes()
 *      {
 *
 *      }
 * }
 *
 * 可用方法
 * ```php
 *
 * public function setAllNodes(Collection $nodes)
 * public function getSubLevel($parentId)
 * public function getDescendants($parentId, $depth = 0, $adds = '')
 * public function getLayerOfDescendants($id)
 * public function getSelf($id)
 * public function getParent($id)
 * public function getAncestors($id, $depth = 0)
 *```
 * @package app\common\traits
 */
trait MemberTreeTrait
{
    public $_allNodes = null;

    protected $treeNodeIdName = 'member_id';
    protected $treeNodeParentIdName = 'parent_id';

    public $filter = [];

    /**
     * 数据主ID名.
     *
     * @return string
     */
    protected function getTreeNodeIdName()
    {
        return property_exists($this, 'treeNodeIdName') ? $this->treeNodeIdName : 'id';
    }

    /**
     * 数据父ID名.
     *
     * @return string
     */
    protected function getTreeNodeParentIdName()
    {
        return property_exists($this, 'treeNodeParentIdName') ? $this->treeNodeParentIdName
            : 'parent_id';
    }

    /**
     * 获取待格式树结构的节点数据.
     *
     * @return mixed
     */
    final protected function getAllNodes($uniacid)
    {
        if ($this->_allNodes) {
            \Log::debug('------allnodes----');
            return $this->_allNodes;
        }
        if (!method_exists($this, 'getTreeAllNodes')) {
            throw new BadMethodCallException('Method [getTreeAllNodes] does not exist.');
        }
        $data = $this->getTreeAllNodes($uniacid); // 由use的class来实现
        if (!$data instanceof ArrayAccess) {
            throw new InvalidArgumentException('tree data must be a collection');
        }
        // 重置键值
        $this->_allNodes = collect([]);
        foreach ($data as $item) {
            $this->_allNodes->put($item->{$this->getTreeNodeIdName()}, $item);
        }
        return $this->_allNodes;
    }

    /**
     * 设置 所有节点.
     *
     * @param \Illuminate\Support\Collection $nodes
     */
    public function setAllNodes(Collection $nodes)
    {
        $this->_allNodes = $nodes;
    }

    /**
     * 获取子级（仅子代一级）.
     *
     * @param mixed $parentId
     *
     * @return array
     */
    public function getSubLevel($uniacid, $parentId)
    {
        $data      = $this->getAllNodes($uniacid);
        $childList = collect([]);

        foreach ($data as $val) {
            if ($val->{$this->getTreeNodeParentIdName()} == $parentId) {

                \Log::debug('------add----', [$parentId, $val->member_id]);
                $childList->put($val->{$this->getTreeNodeIdName()}, $val);
            }
        }
        return $childList;
    }

    /**
     * 获取父级（仅一级）.
     *
     * @param mixed $parentId
     *
     * @return array
     */
    public function getParentLevel($uniacid, $subId)
    {
        $data       = $this->getAllNodes($uniacid);
        $parentList = collect([]);

        if (!empty($data[$subId]) && $subId != $data[$subId]['parent_id'] && $data[$subId]['parent_id'] > 0) {
            \Log::debug('--------list put------', [$subId]);
            $parentList->put($subId, $data[$subId]);
        } else {
            file_put_contents(storage_path("logs/" . date('Y-m-d') . "_batchparent.log"),
                print_r([$subId, $data[$subId]['parent_id'], 'repetition'], 1), FILE_APPEND);
        }

        return $parentList;
    }

    /**
     * 获取指定节点的所有后代.
     *
     * @param mixed $parentId
     * @param int $depth
     * @param string $adds
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDescendants($uniacid, $parentId, $depth = 0, $adds = '')
    {
        static $array;
        if (!$array instanceof ArrayAccess || $depth == 0) {
            $array = collect([]);
        }

        if (in_array($parentId, $this->filter)) {
            \Log::debug('--------------parentId 已存在----------', [$parentId]);
            \Log::debug('-------------array----------', $array);
            return $array;
        }
        \Log::debug('---------------查询下级---------------', $parentId);
        \Log::debug('---------------层级-------------------', $depth);
        $this->filter[] = $parentId;

        $child  = $this->getSubLevel($uniacid, $parentId);
        \Log::debug('------child----', $child->count());
        if ($child) {
            $nextDepth = $depth + 1;

            foreach ($child as $val) {
                $val->depth = $depth;
                $array->put($val->{$this->getTreeNodeIdName()}, $val);

                $this->getDescendants($uniacid,
                    $val->{$this->getTreeNodeIdName()},
                    $nextDepth
                );
            }
        }
        return $array;
    }

    public function getNodeParents($uniacid, $subId, $depth = 0)
    {
        static $array;

        if (!$array instanceof ArrayAccess || $depth == 0) {
            $array = collect([]);
        }
        \Log::debug('--------filter------', [$subId, $this->filter]);
        if (!in_array($subId, $this->filter)) {
            $this->filter[] = $subId;

            $parent = $this->getParentLevel($uniacid, $subId);

            if ($parent) {
                $nextDepth = $depth + 1;

                foreach ($parent as $val) {
                    if (!in_array($val->{$this->getTreeNodeParentIdName()}, $this->filter)) {
                        $val->depth = $depth;

                        $array->put($val->{$this->getTreeNodeParentIdName()}, $val);

                        $this->getNodeParents($uniacid,
                            $val->{$this->getTreeNodeParentIdName()},
                            $nextDepth
                        );
                    }
                }
            }
        }

        return $array;
    }

    /**
     * 获取指定节点的所有后代（分层级）.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Support\Collection
     */
    public function getLayerOfDescendants($id)
    {
        $child = $this->getSubLevel($id);
        $data  = collect([]);
        if ($child) {
            foreach ($child as $val) {
                $val->child = $this->getLayerOfDescendants($val->{$this->getTreeNodeIdName()});
                $data->put($val->{$this->getTreeNodeIdName()}, $val);
            }
        }
        return $data;
    }

    /**
     * 获取指定id的数据.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function getSelf($id)
    {
        $data = $this->getAllNodes();
        return $data->get($id);
    }

    /**
     * 获取父一级节点.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function getParent($id)
    {
        $node = $this->getSelf($id);
        if ($node) {
            $parentId = $node->{$this->getTreeNodeParentIdName()};
            return $this->getSelf($parentId);
        }
    }

    /**
     * 获取节点的所有祖先.
     *
     * @param int $id
     * @param int $depth
     *
     * @return array
     */
    public function getAncestors($id, $depth = 0)
    {
        static $array;
        if (!$array instanceof ArrayAccess || $depth == 0) {
            $array = collect([]);
        }
        $parent = $this->getParent($id);
        if ($parent) {
            $nextDepth = $depth + 1;
            $array->prepend($parent);   // 添加到开头
            $this->getAncestors($parent->{$this->getTreeNodeIdName()}, $nextDepth);
        }
        return $array;
    }

    public function chkNodeParents($uniacid, $subId, $depth = 0)
    {
        static $array;

        if (!$array instanceof ArrayAccess || $depth == 0) {
            $array = collect([]);
        }

        if (!in_array($subId, $this->filter)) {
            $this->filter[] = $subId;

            $parent = $this->chkParentLevel($uniacid, $subId);

            if ($parent) {
                $nextDepth = $depth + 1;

                foreach ($parent as $val) {
                    if (!in_array($val->{$this->getTreeNodeParentIdName()}, $this->filter)) {
                        $val->depth = $depth;

                        $array->put($val->{$this->getTreeNodeParentIdName()}, $val);

                        $this->chkNodeParents($uniacid,
                            $val->{$this->getTreeNodeParentIdName()},
                            $nextDepth
                        );
                    }

                }
            }
        } else {
            \Log::debug('---------重复上级------', [$subId, $this->filter]);
            file_put_contents(storage_path("logs/" . date('Y-m-d') . "-parenterror.log"), print_r($subId . ',', 1),
                FILE_APPEND);
        }
    }

    public function chkParentLevel($uniacid, $subId)
    {
        $data       = $this->getAllNodes($uniacid);
        $parentList = collect([]);

        if (!empty($data[$subId]) && $subId != $data[$subId]['parent_id'] && $data[$subId]['parent_id'] > 0) {
            \Log::debug('---------list put------', [$subId]);
            $parentList->put($subId, $data[$subId]);
        }

        return $parentList;
    }

}