<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/10
 * Time: 下午6:21
 */

namespace app\platform\modules\user\requests;


class AdminUserUpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=>'required|unique:yz_admin_users,name,'.$this->get('id').'|max:255',
            'email'=>'required|email|unique:yz_admin_users,email,'.$this->get('id').'|max:255',
            'password'=>'confirmed|min:6|max:50'
        ];
    }
}