<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/10/4
 * Time: 16:44
 */

namespace app\backend\modules\charts\models;

use Illuminate\Database\Eloquent\Builder;

class YzMember extends \app\common\models\MemberShopInfo
{

    public function scopeRecommender(Builder $query)
    {
        return $query->select(['member_id','parent_id'])->with(['recommender'=> function ($query) {
//            return $query->select(['id','title','status','type','thumb','sku','market_price','price','cost_price'])->goods();
        }]);
    }

    public function recommender()
    {
        return $this->belongsTo(Member::class,'uid', 'uid');
    }
}