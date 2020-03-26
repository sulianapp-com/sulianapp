<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 22/02/2017
 * Time: 18:48
 */

namespace app\backend\modules\member\services;


use app\common\extensions\Validation;

class TestMemberValidation extends Validation
{
    /**
     * Validate a comment before publishing it.
     *
     * @throws ValidateException
     * @return void
     */
    public function publish()
    {
        $this->rules = array(
            'name'    => array('required'),
            'email'   => array('required', 'email'),
            'comment' => array('required', 'max:200')
        );

        $this->validate();
    }
}