<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/1
 * Time: 下午5:01
 */

namespace app\frontend\modules\order\operations;


interface OrderOperationInterface
{
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';
    const TYPE_PRIMARY = 'primary';
    const TYPE_INFO = 'info';

    public function enable();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getValue();

    /**
     * @return string
     */
    public function getApi();
    /**
     * @return string
     */
    public function getType();

}