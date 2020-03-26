<?php

namespace app\common\modules\category;

class CategoryGroup
{
    private $categoryGroup;

    public function __construct($categories)
    {
        $categoryGroup = [];
        foreach ($categories as $category) {
            $categoryGroup[$category['parent_id']][] = $category;
        }
        $this->categoryGroup = $categoryGroup;

    }

    public function find($categoryId)
    {
        return $this->categoryGroup[$categoryId];
    }

}
