<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/3/10
 * Time: 下午12:54
 */

namespace app\platform\modules\user\requests;


class PermissionUpdateRequest extends Request
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
            'name'=>'required|unique:yz_admin_permissions,name,'.$this->get('id').'|max:255',
            'label'=>'unique:yz_admin_permissions,label,'.$this->get('id').'|max:255',
            'parent_id'=>'int',
        ];
    }
}
