<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 上午11:18
 */

namespace app\common\models;



use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberLevel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_member_level';

    protected $guarded = [''];

    /**
     * 设置全局作用域 拼接 uniacid()
     */
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('uniacid',function (Builder $builder) {
            return $builder->uniacid();
        });
    }

    public function scopeRecords($query)
    {
        return $query->select('id','level','level_name');
    }

    /**
     * 获取默认等级
     *
     * @return mixed
     */
    public static function getDefaultLevelId()
    {
        return self::select('id')
            ->uniacid()
            ->where('is_default', 1);
    }
    /**
     * 商品全局等级折扣后价格
     * @param $goodsPrice
     * @return float|int
     */
    public function getMemberLevelGoodsDiscountAmount($goodsPrice)
    {
        // 商品折扣 默认 10折
        $this->discount = trim($this->discount);
        $this->discount = $this->discount == false ? 10 : $this->discount;
        // 折扣/10 得到折扣百分比
        return (1 - $this->discount / 10) * $goodsPrice;
    }
}
