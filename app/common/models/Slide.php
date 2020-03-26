<?php
namespace app\common\models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/2/27
 * Time: 上午9:11
 */

class Slide extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_slide';
    public $attributes = ['display_order' => 0];
    protected $guarded = [''];

    protected $fillable = [''];

    public static function getSlidesIsEnabled()
    {
        return self::uniacid()
            ->where('enabled','1');
    }
    
    /**
     *  定义字段名
     * 可使
     * @return array */
    public  function atributeNames() {
        return [
            'slide_name'=> '幻灯片名称',
            'display_order'=> '排序',
            'thumb'=> '幻灯片图片',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public  function rules() {
        return [
            'slide_name' => 'required',
            'display_order' => 'required',
            'thumb' => 'required',
        ];
    }
}
