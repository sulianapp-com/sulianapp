<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/2
 * Time: 下午8:34
 */

namespace app\frontend\modules\member\models;


class MemberAddress extends \app\common\models\MemberAddress
{
    protected $guarded = [''];
    /*
     *  Get a list of members receiving addresses
     *
     *  @param int $memberId
     *
     *  @return array
     * */
    public static function getAddressList($memberId)
    {
        return static::select('id', 'username', 'mobile', 'zipcode', 'province', 'city', 'district', 'address', 'isdefault')
            ->uniacid()->where('uid', $memberId)->get()->toArray();
    }

    /*
     *  Get the receiving address information through the receiving address ID
     *
     *  @param int $addressId
     *
     *  @return array
     * */
    public static function getAddressById($addressId)
    {
        return static::uniacid()->where('id', $addressId)->first();
    }

    /*
     *  Delete the receiving address by receiving address ID
     *
     *  @param int $addressId
     *
     *  @return int 0 or 1
     * */
    public static function destroyAddress($addressId)
    {
        return static::where('id', $addressId)->delete();
    }

    /*
     *  Cancel the default address
     *
     *  @param int $memberId
     *
     *  @return int 0or 1
     * */
    public static function cancelDefaultAddress($memberId)
    {
        return static::uniacid()->where('uid', $memberId)->where('isdefault', '1')->update(['isdefault' => '0']);
    }

    /**
     * 定义字段名
     *
     * @return array */
    public  function atributeNames() {
        return [
            'mobile'    => 'mobile_phone',
            'username'  => '收货人',
            'province'  => '省份',
            'city'      => '城市',
            'district'  => '区域',
        ];
    }

    /**
     * 字段规则
     *
     * @return array */
    public  function rules()
    {
        return [
            'mobile'    => 'regex:/^[0-9]*$/',
            'username'  => 'required|max:45',
            'province'  => 'required',
            'city'      => 'required',
            'district'  => 'required',
        ];
    }

}
