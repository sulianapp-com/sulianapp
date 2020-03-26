<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2019/2/19
 * Time: 上午9:49
 */

namespace app\platform\controllers;

class IndexController extends BaseController
{

    public function index()
    {
        $role = 0;

        $user = \Auth::guard('admin')->user();

        if (1 == $user['uid']) {
            $role = 1;
        }

        $pattern = "/(\d{3})\d{4}(\d{4})/";
        $replacement = "\$1****\$2";
        $mobile = preg_replace($pattern, $replacement, $user['hasOneProfile']['mobile']);

        $data = [
            'username' => $user['username'],
            'role' => $role,
            'avatar' => $user['hasOneProfile']->avatar,
            'mobile' => $mobile
        ];

        return $this->successJson('成功', $data);
    }
}
