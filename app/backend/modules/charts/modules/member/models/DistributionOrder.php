<?php

namespace app\backend\modules\charts\modules\member\models;

use app\common\models\BaseModel;

class DistributionOrder extends BaseModel
{
    public $table = 'yz_distribution_order';
   	public $timestamps = true;
    protected $fillable = [];
    protected  $guarded = [''];
    
    public function scopeSearch($q, $search)
    {
    	$model = $q->where('uniacid', \YunShop::app()->uniacid)->with('hasOneMember');
       
        if (!empty($search['member_id'])) {
            $model->whereHas('hasOneMember', function ($q) use($search) {
                $q->where('uid', $search['member_id']);
            });
        }

        if (!empty($search['member_info'])) {
            $model->whereHas('hasOneMember', function ($q) use($search) {
                $q->where('nickname', 'like' , '%' . $search['member_info'] . '%')
                    ->orWhere('realname', 'like' , '%' . $search['member_info'] . '%')
                    ->orWhere('mobile', 'like' , '%' . $search['member_info'] . '%');
            });
        }
    	return $model;
    }

   	public function hasOneMember()
   	{
   		return $this->hasOne('app\common\models\Member', 'uid', 'uid');
   	}
}
