<?php


namespace app\common\modules\category;


class Category
{
    private $attributes;
    private $children;
    private $categoryGroup;
    private $tree;

    public function __construct($attributes, CategoryGroup $categoryGroup)
    {
        unset($attributes['parent_id']);
        $this->attributes = $attributes;
        $this->categoryGroup = $categoryGroup;
    }

    private function getChildren()
    {
        if (!isset($this->children)) {

            $childrenAttributes = $this->categoryGroup->find($this->attributes['id']);
            $this->children = [];

            foreach ($childrenAttributes as $attributes) {

                $this->children[] = new static($attributes, $this->categoryGroup);
            }
        }

        return $this->children;
    }

    public function getChildrenTree($level = 99)
    {
        return $this->tree($level)['childrens'] ?: [];
    }

    public function tree($level)
    {
        // 缓存指定层级数据
        if (!isset($this->tree[$level])) {
            $this->tree[$level] = $this->attributes;
            $this->tree[$level]['childrens'] = [];
            if ($level == 0) {
                $this->tree[$level]['childrens'] = [];
            } else {
                // 按层级递减,返回$level层
                foreach ($this->getChildren() as $category) {
                    $this->tree[$level]['childrens'][] = $category->tree($level - 1);
                }
            }
        }
        return $this->tree[$level];

    }
}