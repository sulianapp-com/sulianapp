<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 下午18:16
 */
namespace app\backend\modules\goods\models;

use app\backend\modules\goods\observers\GoodsObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Goods extends \app\common\models\Goods
{
    public $widgets = [];


    //todo 兼容后端模拟支付时，调用
    public function hasOneSale()
    {
        return $this->hasOne(\app\frontend\models\goods\Sale::class);
    }

    /**
     * 在boot()方法里注册下模型观察类
     * boot()和observe()方法都是从Model类继承来的
     * 主要是observe()来注册模型观察类，可以用TestMember::observe(new TestMemberObserve())
     * 并放在代码逻辑其他地方如路由都行，这里放在这个TestMember Model的boot()方法里自启动。
     */
    public static function boot()
    {
        parent::boot();
        //注册观察者
        static::observe(new GoodsObserver);

        static::addGlobalScope(function (Builder $builder) {
            $builder->isPlugin();
        });
    }

    public function scopeGoods($query){
        return $query->select(['id','status','display_order','title','thumb','thumb_url','product_sn','market_price','price','cost_price']);
    }

    //根据商品ID获取商品名称
    public static function getGoodNameByGoodIds($goodIds){
        if(is_array($goodIds)){
            $res = static::uniacid()
                        ->select('title')
                        ->whereIn('id', $goodIds)
                        ->orderByRaw(DB::raw("FIELD(id, ".implode(',', $goodIds).')')) //必须按照goodIds的顺序输出商品名称
                        ->get()
                        ->map(function($goodtitle){ //遍历
                            return $goodtitle['title'];
                        })
                        ->toArray();
        } else{
            $res = static::uniacid()
                        ->select('title')
                        ->where('id', '=', $goodIds)
                        ->first();
        }
        return $res;
    }
}