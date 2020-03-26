<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/14
 * Time: 上午11:01
 */

namespace app\common\models;


/**
 * Class OrderAddress
 * @package app\common\models
 * @property string address
 * @property string mobile
 * @property string realname
 * @property int order_id
 * @property int province_id
 * @property int city_id
 * @property int district_id
 * @property string note
 * @property int street_id
 */
class OrderAddress extends BaseModel
{
    public $table = 'yz_order_address';
    protected $guarded = ['id'];
    protected $hidden = ['id', 'order_id'];

    public $province;
    public $city;
    public $district;
    public $street;
    protected $attributes = [
        'street_id' => 0,
        'zipcode' => '',
    ];
    /**
     *  定义字段名
     * 可使
     * @return array */
    public function atributeNames() {
        return [
            'address'=> '收货详细地址',
            'mobile'=> '收货电话',
            'realname'=> '收货人姓名',
            'province_id'=> '收货省份',
            'city_id'=> '收货城市',
            'district_id'=> '收货地区',
            'zipcode' => '收件地址邮编'
        ];
    }

    /**
     * 字段规则
     * @return array */
    public function rules() {

        $rule =  [
            //具体unique可看文档 https://laravel.com/docs/5.4/validation#rule-unique
            'address'=> 'required',
            //'mobile'=> 'required',
            //'realname'=> 'required',
            'province_id'=> 'required',
            'city_id'=> 'required',
            'district_id'=> 'required',
            // 'zipcode'=> ''
        ];

        return $rule;
    }
    public function save(array $options = [])
    {
        if ($this->validator()->fails()) {
            return true;
        }
        return parent::save($options);
    }
}