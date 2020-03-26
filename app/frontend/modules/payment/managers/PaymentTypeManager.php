<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2017/10/27
 * Time: 上午9:56
 */

namespace app\frontend\modules\payment\managers;

use app\common\models\PayType;
use app\common\models\OrderPay;
use app\frontend\modules\order\models\PreOrder;
use app\frontend\modules\order\OrderCollection;
use app\frontend\modules\payment\orderPayments\BasePayment;
use app\frontend\modules\payment\PaymentConfig;
use app\frontend\modules\payment\orderPayments\NormalPayment;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;

/**
 * 支付管理者
 * Class OrderPaymentManager
 * @package app\frontend\modules\payment\managers
 */
class PaymentTypeManager extends Container
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;
    /**
     * @var array
     */
    private $paymentConfig;

    /**
     * OrderPaymentManager constructor.
     * @param PaymentManager $paymentManager
     */
    public function __construct(PaymentManager $paymentManager)
    {

        $this->paymentManager = $paymentManager;

        // 支付设置数组 todo 使用laravel的config定义并读取
        $this->paymentConfig = PaymentConfig::get();


    }

    /**
     * 追加设置
     * @param $paymentConfig
     */
    public function addPaymentConfig($paymentConfig)
    {
        $this->paymentConfig = array_merge_recursive($this->paymentConfig, $paymentConfig);
    }

    /**
     * 获取设置管理者
     * @return Container
     */
    private function getSettingManager()
    {
        return app('PaymentManager')->make('OrderPaymentTypeSettingManager');
    }

    /**
     * 绑定支付设置类,订单支付设置类会复写这个方法 todo 感觉这个里有问题
     */
    private function bindSettings()
    {

        // 支付方式集合
        collect($this->paymentConfig)->each(function ($payment, $code) {
            // 绑定支付方式对应的设置管理者

            $this->getSettingManager()->singleton($code, function (Container $container) use ($payment) {
                return $payment['settings'];
            });
        });
    }

    /**
     * 绑定支付方式类
     */
    private function bindPayments()
    {
        // 支付方式集合

        collect($this->paymentConfig)->each(function ($payment, $code) {

            /**
             * 分别绑定支付方式与支付方式设置类. 只定义不实例化,以便于插件在支付方式实例化之前,追加支付方式与支付方式的设置类
             */
            // 绑定支付方式
            $this->bind($code, function ($manager, array $params) use ($payment, $code) {
                list($orderPay, $payType) = $params;
                /**
                 * @var OrderPaymentTypeSettingManager $settingManager
                 */
                // 从管理者取取到自己对应的设置集合
                $settingManager = $this->getSettingManager();

                $settings = $settingManager->getOrderPaymentSettingCollection($code, $orderPay);

                if (isset($payment['payment']) && $payment['payment'] instanceof \Closure) {

                    return call_user_func($payment['payment'], $orderPay, $payType, $settings);
                }
                return new NormalPayment($orderPay, $payType, $settings);
            });


        });
    }

    public function getAllOrderPaymentTypes(OrderPay $orderPay)
    {
        $this->bindSettings();
        $this->bindPayments();

        /**
         * 商城中存在的支付方式集合
         * @var \Illuminate\Database\Eloquent\Collection $paymentTypes
         */
        $paymentTypes = PayType::get();
        if ($paymentTypes->isEmpty()) {
            return collect();
        }

        // 实例化订单所有支付方式
        $orderPaymentTypes = $paymentTypes->map(function (PayType $payType) use ($orderPay) {

            // 对应的类在容器中注册过
            if ($this->bound($payType->code)) {

                return $this->make($payType->code, [$orderPay, $payType]);
            }
            return null;
        });

        return $orderPaymentTypes;

    }

    /**
     * 获取订单可用的支付方式
     * @param OrderPay $orderPay
     * @return Collection|static
     */
    public function getOrderPaymentTypes(OrderPay $orderPay = null)
    {
        if(!isset($orderPay)){
            // 虚拟支付单
            $orderPay = OrderPay::newVirtual();
        }

        // 过滤掉无效的
        $orderPaymentTypes = $this->getAllOrderPaymentTypes($orderPay)->filter(function ($paymentType) {
            // 可用的
            return isset($paymentType) && $paymentType instanceof BasePayment && $paymentType->canUse();
        });
        return $orderPaymentTypes;
    }
}