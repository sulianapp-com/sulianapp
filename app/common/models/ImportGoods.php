<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 19:35
 */

namespace app\common\models;

use app\backend\modules\goods\models\Sale;
use app\backend\modules\goods\observers\GoodsObserver;
use app\common\exceptions\AppException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use app\common\models\Coupon;

class ImportGoods extends BaseModel
{

    use SoftDeletes;

    public $table = 'yz_goods';

    public static function getGoodsByIdAll($goodsId)
    {
        $model = self::where('id', $goodsId);

        $model->with(['hasManySpecs'=>function($query){
            return $query->with('hasManySpecsItem');
        }]);
        $model->with('hasManyOptions');
        $model->with('hasManyParam');
        $model->with('hasOneShare');

        return $model;
    }

    public function hasManyParam()
    {
        return $this->hasMany('app\common\models\GoodsParam','goods_id','id');
    }

    public function hasManySpecs()
    {
        return $this->hasMany('app\common\models\GoodsSpec','goods_id','id');
    }
    public function hasManyOptions()
    {
        return $this->hasMany('app\common\models\GoodsOption','goods_id','id');
    }
    public function hasOneShare()
    {
        return $this->hasOne('app\common\models\goods\Share','goods_id','id');
    }

}
