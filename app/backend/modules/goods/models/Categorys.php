<?php
namespace app\backend\modules\goods\models;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/22
 * Time: 下午2:24
 */

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class Categorys extends \app\common\models\Category
{
    protected $appends = ['url','procedures_url'];

    static protected $needLog = true;


    public static function getCategory(){

       return self::uniacid()->select('id','name','parent_id')->with(['hasManyChildren'=>function($query){
           $query->select('id','name','parent_id')
               ->with(['hasManyChildren'=>function($query){
                   $query->select('id','name','parent_id');
               }]);
           }])->get()->toArray();
    }


    public function hasManyChildren()
    {
        return $this->hasMany(self::class, 'parent_id','id');
    }



    public function getUrlAttribute()
    {

        return yzAppFullUrl('catelist/'.$this->id);
    }

    public function getProceduresUrlAttribute()
    {

        return '/packageB/member/category/catelist/catelist?id='.$this->id;
    }


}