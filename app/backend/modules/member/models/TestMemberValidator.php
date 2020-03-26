<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 22/02/2017
 * Time: 23:54
 */

namespace app\backend\modules\member\models;


use Prettus\Validator\LaravelValidator;

class TestMemberValidator extends LaravelValidator
{
    protected $rules = [
        'title' => 'required',
        'email' => 'required',
        'text'  => 'min:3',
        'author'=> 'required'
    ];

    protected $messages = [
        'required' => 'The :attribute field is required.',
        'email.required' => 'We need to know your e-mail address!',
    ];
}