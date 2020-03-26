<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 11:34
 */

namespace app\backend\modules\finance\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisement extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_advertisement';

    public $timestamps = true;

    protected $guarded = [''];

    protected $attributes = [
        'status' => 0,
    ];


    public static function getList($search)
    {
        $model = self::uniacid();

        if (isset($search['name'])) {
            $model->where('name', 'LIKE', '%'.$search['name'].'%');
        }

        $model->orderBy('sort_by', 'desc')->orderBy('id');
        return $model;
    }

    public static function getOneData()
    {
        return self::uniacid()->Status()->select()->orderBy('sort_by', 'desc');
    }


    public function scopeStatus($query, $status = 1)
    {
        return $query->where('status', $status);
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'thumb' => 'required',
            'adv_url' => 'required',
        ];
    }

    public function atributeNames()
    {
        return [
            'name' => '标题',
            'thumb' => '广告图片',
            'adv_url' => '链接',
        ];
    }
}