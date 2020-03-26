<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/7/27 下午4:09
 * Email: livsyitian@163.com
 */

namespace app\backend\modules\withdraw\controllers;


use app\backend\models\Withdraw;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;

abstract class PreController extends BaseController
{

    /**
     * @var Withdraw
     */
    protected $withdrawModel;


    public function __construct()
    {
        parent::__construct();
        $this->withdrawModel = $this->getWithdrawModel();
    }


    /**
     * 相关验证
     *
     * @param Withdraw $withdrawModel
     * @return ShopException
     */
    abstract function validatorWithdrawModel($withdrawModel);


    /**
     * 获取 withdrawModel
     *
     * @return Withdraw
     * @throws ShopException
     */
    protected function getWithdrawModel()
    {
        $withdraw_id = $this->getPostWithdrawId();

        $withdrawModel = Withdraw::find($withdraw_id);
        if (!$withdrawModel) {
            throw new ShopException('数据不存在或已被删除!');
        }

        $this->validatorWithdrawModel($withdrawModel);

        return $withdrawModel;
    }


    /**
     * 获取 POST 提交的ID主键
     *
     * @return int
     * @throws ShopException
     */
    protected function getPostWithdrawId()
    {
        $withdraw_id = \YunShop::request()->id;
        if (empty($withdraw_id)) {
            throw new ShopException('数据错误，请重试!');
        }
        return $withdraw_id;
    }
}
