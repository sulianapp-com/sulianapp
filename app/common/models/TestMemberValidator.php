<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 22/02/2017
 * Time: 14:39
 */

namespace app\common\models;


use Illuminate\Contracts\Validation\Validator;

class TestMemberValidator
{
    public static function rule()
    {
        return [
            'username'=>'required|max:255',
            'email'=>'required|email|max:25',
            'password'=>'required|min:6|confirmed',
        ];
    }

    public static function validator(array $data)
    {
        return Validator::make($data, self::rule());
    }
}