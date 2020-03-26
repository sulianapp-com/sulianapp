<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/27 上午11:14
 * Email: livsyitian@163.com
 */

namespace app\backend\modules\withdraw\controllers;


class DetailController extends PreController
{
    /**
     * 提现记录详情 接口
     *
     * @throws \Throwable
     */
    public function index()
    {
        return view('withdraw.detail', $this->resultData());
    }


    public function validatorWithdrawModel($withdrawModel)
    {
    }

    private function resultData()
    {
        return [
            'item'      => $this->withdrawModel,
        ];
    }


}
