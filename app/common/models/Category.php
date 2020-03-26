<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 下午5:54
 */
class Category extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_category';
    public $attributes = [
        'display_order' => 0,
        'thumb' => '',
        'description' => '',
        'adv_img' => '',
        'adv_url' => '',
    ];


    /**
     *  不可填充字段.
     *
     * @var array
     */
    protected $guarded = [''];

    //protected $fillable = [''];

    /**
     * @param $parent_id
     * @param $pageSize
     * @return mixed
     */
    public static function getCategorys($parentId)
    {
        return self::uniacid()
            ->where('parent_id', $parentId)
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * @param $parentId
     * @param $set
     * @return mixed
     */
    public static function getChildrenCategorys($parentId, $set = [])
    {
        $model = self::uniacid();

        if ($set['cat_level'] == 3) {
            $model->with(['hasManyChildren'=>function($qurey){
                return $qurey->where('enabled', 1)
                    ->orderBy('display_order', 'asc');
            }]);
        }

        $model->where('parent_id', $parentId);
        $model->where('enabled', 1);
        $model->orderBy('display_order', 'asc');
        return $model;
    }

    /**
     * @return mixed
     */
    public static function getRecommentCategoryList()
    {
        $model = self::uniacid();
        return $model;

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyChildren()
    {
        return $this->hasMany(self::class, "parent_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsCategories()
    {
        return $this->hasMany('app\common\models\GoodsCategory', 'category_id', 'id');
    }

    public function scopePluginId($query)
    {
        return $query->where('plugin_id', 0);
    }
}