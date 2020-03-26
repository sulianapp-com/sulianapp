<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/8/2
 * Time: 下午2:01
 */

namespace app\common\modules\order;

use app\common\models\Order;
use app\frontend\modules\order\operations\OrderOperation;
use app\frontend\modules\order\operations\OrderOperationInterface;

class OrderOperationsCollector
{
    /**
     * @param Order $order
     * @return array
     * @throws \app\common\exceptions\AppException
     */
    public function getOperations(Order $order)
    {
        $operationsSettings = $order->getOperationsSetting();
        $operations = array_map(function ($operationName) use ($order) {
            /**
             * @var OrderOperationInterface $operation
             */
            $operation = new $operationName($order);
            if (!$operation->enable()) {
                return null;
            }
            $result['name'] = $operation->getName();
            $result['value'] = $operation->getValue();
            $result['api'] = $operation->getApi();
            $result['type'] = $operation->getType();

            return $result;
        }, $operationsSettings);

        $operations = array_filter($operations);
        return array_values($operations) ?: [];
    }

    /**
     * @param Order $order
     * @return array
     */
    public function getAllOperations(Order $order)
    {
        $operations = array_map(function ($operationName) use ($order) {
            /**
             * @var OrderOperation $operation
             */
            $operation = new $operationName($order);
            $result['name'] = $operation->getName();
            $result['value'] = $operation->getValue();
            return $result;
        }, $this->getContract($order->statusCode));
        return $operations;
    }
}