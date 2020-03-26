<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/4
 * Time: 上午11:23
 */

namespace app\common\models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Area
 * @package app\common\models
 * @property int id
 * @property int level
 * @property int parentid
 * @property string areaname
 */
class Area   extends Model
{
    public $table = 'yz_address';

    protected $guarded = [''];

    public static function getAreaList()
    {
        return self::get();
    }

    public static function getProvincesList()
    {
        return self::where('parentid', 0)
            ->get();
    }

    public static function getProvinces($parentId)
    {
        return self::where('parentid', $parentId)
            ->get();
    }

    public static function getCitysByProvince($parentId)
    {
        return self::where('parentid', $parentId)
            ->get();
    }

    public static function getAreasByCity($parentId)
    {
        return self::where('parentid', $parentId)
            ->get();
    }

    /**
     * @return array message
     */
    public static function validationMessages()
    {
        return [
            'required' => ' :attribute不能为空!',
            'min' => ' :attribute不能少于:min!',
            'max' => ' :attribute不能少于:max!',
            'integer' => ':attribute请填写数字',


        ];
    }

    /**
     * 校验表单数据
     *
     * @param $data
     * @return \Illuminate\Validation\Validator
     */
    public static function validator($data)
    {
        $validator = Validator::make($data, [
            'display_order' => 'accepted|integer',
            'dispatch_name' => 'required|max:50',
            'first_weight' => 'accepted|max:50',
            'first_weight_price' => 'accepted',
            'another_weight' => 'accepted',
            'another_weight_price' => 'accepted',
            'first_piece' => 'accepted',
            'first_piece_price' => 'accepted',
            'another_piece' => 'accepted',
            'another_piece_price' => 'accepted',
        ], self::validationMessages());

        return $validator;
    }

    /**
     * 是叶节点
     * @return bool
     */
    public function isLeaf(){
        // 区县并且没开启街道
        return $this->level == 3 && !\Setting::get('shop.trade.is_street');
    }

}