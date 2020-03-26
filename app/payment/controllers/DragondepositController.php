<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/15
 * Time: 14:39
 */

namespace app\payment\controllers;

use app\backend\modules\refund\services\RefundOperationService;
use app\backend\modules\refund\services\RefundMessageService;
use app\common\helpers\Url;
use app\common\models\AccountWechats;
use app\payment\PaymentController;
use app\common\services\Pay;
use app\frontend\modules\order\services\OrderService;
use Yunshop\DragonDeposit\common\LcgLog;
use Yunshop\DragonDeposit\common\sdk\business\Notice;
use Yunshop\DragonDeposit\models\LcgRefund;
use Yunshop\DragonDeposit\models\LcgWithdraw;
use Yunshop\DragonDeposit\models\Merchant;
use Yunshop\DragonDeposit\models\MerchantMajor;
use Yunshop\DragonDeposit\models\MerchantOrder;
use Yunshop\DragonDeposit\models\MerchantUpdate;
use Yunshop\DragonDeposit\services\OrderTypeService;

class DragondepositController  extends PaymentController
{

    //支付类型
    protected $pay_type = [
        'A' => [
            'name' => '存管银行-余额支付',
            'id' => 42,
        ],
        'G' => [
            'name' => '存管银行-银行卡支付',
            'id' => 43,
        ],
    ];

    protected $parameters;

    public function __construct()
    {
        parent::__construct();

        if (empty(\YunShop::app()->uniacid)) {
            $this->setShopUniacid();
        }

        if (!app('plugins')->isEnabled('dragon-deposit')) {
            echo 'Not turned on dragon deposit';exit();
        }
        LcgLog::debug('GET------------------------', $_GET);
        LcgLog::debug('POST------------------------', $_POST);

        $this->parameterDecryption();
    }

    public function setShopUniacid()
    {
        $i = $_GET['i'];
        if (isset($i)) {
            \Setting::$uniqueAccountId = \YunShop::app()->uniacid = $i;
            AccountWechats::setConfig(AccountWechats::getAccountByUniacid(\YunShop::app()->uniacid));
        }
    }

    public function parameterDecryption()
    {
        $return_info = (new Notice())->asynchroNotice($_POST);
        if (isset($return_info['returnCode'])) {
            //todo 记录接口返回错误信息
            LcgLog::debug('通知错误----------------------->', $return_info);
            echo $return_info['returnCode'];exit();
        }
        $this->parameters = $return_info;
    }

    //通用异步通知
    public function bgRetUrl()
    {
        LcgLog::debug('lcg异步通知------------------------', $this->parameters);
        echo 'SUCCESS';exit();
    }

    //通用同步通知
    public function pageRetUrl()
    {
        //LcgLog::debug('<-----------lcg同步通知---------------->', $this->parameters);
        redirect(Url::absoluteApp('myWallet', ['i' => \YunShop::app()->uniacid]))->send();
    }


    //支付异步通知
    public function payBgRetUrl()
    {
        LcgLog::debug('支付异步通知返回参数------------------------', $this->parameters);
        if ($this->verify() && $this->getInfoParameter('retCode') == 'MCG00000') {

            $this->log();

            (new OrderTypeService())->merchantWithdraw($this->getBodyParameter('Lists'));

            $data = [
                'total_fee' => floatval($this->getBodyParameter('trAmt')),
                'out_trade_no' => $this->getBodyParameter('mercOrdNo'),
                'trade_no' => $this->getBodyParameter('jrnno'),
                'unit' => 'fen',
                'pay_type' => $this->pay_type[$this->getBodyParameter('payTyp')]['name'],
                'pay_type_id' => $this->pay_type[$this->getBodyParameter('payTyp')]['id'],

            ];
            $this->payResutl($data);

            echo 'SUCCESS';exit();
        } else {
            LcgLog::debug('支付异步通知返回错误信息------------------------', $this->getInfoParameter('errMsg'));
            echo 'failure';exit();
        }

    }

    //支付同步通知
    public function payPageRetUrl()
    {
        //LcgLog::debug('<-----------lcg支付同步通知---------------->', $this->parameters);
        redirect(Url::absoluteApp('member/orderList/0', ['i' => \YunShop::app()->uniacid]))->send();
    }

    //用户确认订单退款异步通知
    public function orderAcceptRefund()
    {
        LcgLog::debug('用户确认订单退款异步通知------------------------merchantNotifyUrl', $this->parameters);

        $resCode =  substr($this->getInfoParameter('retCode'), -5);
        if ($resCode != '00000') { exit('failure'); }


        $lcgRefund = LcgRefund::uniacid()->where('jrnno', $this->getBodyParameter('jrnno'))->with(['hasOneOrder'])->first();

        if (is_null($lcgRefund->hasOneOrder)) {
            exit('failure');
        }
        $refundApply = \app\common\models\refund\RefundApply::where('order_id', $lcgRefund->hasOneOrder->id)->first();

        if ($refundApply) {
            //退款状态设为完成
            RefundOperationService::refundComplete(['id' => $refundApply->id]);
            RefundMessageService::passMessage($refundApply);//通知买家
        }
        MerchantOrder::uniacid()->where('tradeOrdNo',$lcgRefund->tradeOrdNo)->update(['status' => MerchantOrder::STATUS_CANCEL]);

        echo 'SUCCESS';exit();
    }


    //用户确认订单退款同步通知
    public function orderAcceptRefundUrl()
    {
        //LcgLog::debug('<-----------lcg同步通知---------------->', $this->parameters);

        if (isset($_GET['admin'])) {
            redirect(Url::absoluteWeb('index.index'))->send();
        } else {
            redirect(Url::absoluteApp('myWallet', ['i' => \YunShop::app()->uniacid]))->send();
        }
    }

    //添加电子登记簿异步
    public function merchantNotifyUrl()
    {
        LcgLog::debug('添加电子登记簿异步------------------------merchantNotifyUrl', $this->parameters);

        //if ($this->getInfoParameter('retCode') != 'MCA00000') {
            //exit('failure');
        //}

        $reqSn = $this->getInfoParameter('reqSn');
        $mbrCode = $this->getBodyParameter('mbrCode');
        $jrnno = $this->getBodyParameter('jrnno');
        $userSts = $this->getBodyParameter('userSts');
        $bankMbl = $this->getBodyParameter('bankMbl');
        $bankName = $this->getBodyParameter('bankName');
        $bankCode = $this->getBodyParameter('bankCode');
        $bankNum = $this->getBodyParameter('bankNum');
        $bankNm = $this->getBodyParameter('bankNm');

        $major = MerchantMajor::uniacid()->where("reqSn",$reqSn)->first();

        if (empty($major)) {
            lcgLog::debug('添加信息报错-------------merchantUpdateNotifyUrl',$reqSn);
            exit('failure');
        }

        $user = Merchant::uniacid()->where("member_id",$major['member_id'])->first();

        is_null($user) && $user = new Merchant();
        //记录信息
        $data = array(
            'uniacid' => $major['uniacid'],
            'member_id' => $major['member_id'],
            'reqSn' => $reqSn,
            'platCusNO' => $major['platCusNO'],
            'mbrCode' => $mbrCode,
            'platRoleID' => $major['platRoleID'],
            'jrnno' => $jrnno,
            'userSts' => $userSts,
            'rstMess' => $this->getBodyParameter('rstMess'),
        );

        $user->fill($data);
        $user->save();
        
        $major_data = array(
            'jrnno' => $jrnno,
            'userSts' => $userSts,
            'mbrCode' => $mbrCode,
            'bankMbl' => $bankMbl,
            'bankName' => $bankName,
            'bankCode' => $bankCode,
            'bankNum' => $bankNum,
            'bankNm' => $bankNm,
            'account' => $this->getBodyParameter('account'),
            'walt_status' => 1,
        );

        $major_data = array_filter($major_data);
        $major_data['userSts'] = $userSts;
        $major->fill($major_data);
        $major->save();

        echo 'SUCCESS';exit;
    }
    //添加电子登记簿同步
    public function merchantReturnUrl()
    {
        LcgLog::debug('添加电子登记簿同步------------------------merchantReturnUrl', $this->parameters);

        $reqSn = $this->getInfoParameter('reqSn');
        MerchantMajor::uniacid()->where("reqSn",$reqSn)->update(['walt_status' => 1]);

        redirect(Url::absoluteApp('member', ['i' => \YunShop::app()->uniacid]))->send();
    }

    //修改电子登记簿异步
    public function merchantUpdateNotifyUrl()
    {
        LcgLog::debug('修改电子登记簿异步------------------------merchantUpdateNotifyUrl', $this->parameters);

        $reqSn = $this->getInfoParameter('reqSn');

        $update = MerchantUpdate::uniacid()->where("reqSn",$reqSn)->first();

        if (empty($update))
        {
            lcgLog::debug('修改信息报错-------------merchantUpdateNotifyUrl',$reqSn);
            exit('failure');
        }

        $type = ['08','09','10','11'];

        $rstCode = $this->getBodyParameter('rstCode');
        $operType = $this->getBodyParameter('operType');

        $person = ['realNm','idNo','account','bankMbl','mbrCode'];

        $company = ['accountNm','bankCodeName','account','bankNum','bankNm','legalPerIdTyp','legalPerIdNo','agent','agentIdNo','agentMbl','authorizer1Nm','authorizer1IdNum','authorizer1Mbl','authorizer2Nm','authorizer2IdTyp','authorizer2IdNum','authorizer2Mbl'];

        if ($rstCode == 0) {
            $up_data = array();
            if (in_array($operType,$type)) {
                foreach ($person as $v) {
                    $up_data[$v] = $this->getBodyParameter($v);
                }
            } else {
                foreach($company as $v) {
                    $up_data[$v] = $this->getBodyParameter($v);
                }
                $up_data['legalFront'] = $update['legalFront'];
                $up_data['legalBack'] = $update['legalBack'];
                $up_data['cert'] = $update['cert'];
            }

            //修改信息
            $up_data = array_filter($up_data);

            MerchantMajor::uniacid()->where("mbrCode",$update['mbrCode'])->update($up_data);

            echo 'SUCCESS';exit;
        } elseif ($rstCode == 1) {
            MerchantUpdate::uniacid()->where("reqSn",$reqSn)->update(['upStatus'=> 1]);

            echo 'SUCCESS';exit;
        }

        MerchantUpdate::uniacid()->where("reqSn",$reqSn)->update(['upStatus'=> 2]);

        echo 'SUCCESS';exit;
    }

    //修改电子登记簿同步
    public function merchantUpdateReturnUrl()
    {
        LcgLog::debug('修改电子登记簿同步------------------------merchantReturnUrl', $this->parameters);

        redirect(Url::absoluteApp('myWallet', ['i' => \YunShop::app()->uniacid]))->send();
    }

    //密码修改回调地址
    public function passwdUpdateNotifyUrl()
    {
        LcgLog::debug('修改密码异步------------------------merchantReturnUrl', $this->parameters);

    }

    //密码修改同步地址
    public function passwdUpdateReturnUrl()
    {
        //LcgLog::debug('密码修改同步地址------------------------merchantReturnUrl', $this->parameters);
        redirect(Url::absoluteApp('myWallet', ['i' => \YunShop::app()->uniacid]))->send();

    }

    //出金异步
    public function withdrawBgRetUrl()
    {
        LcgLog::debug('出金异步------------------------withdrawBgRetUrl', $this->parameters);


        if ($this->getInfoParameter('retCode') != 'MCG00000') {
            exit('failure');
        }

        $withdraw = LcgWithdraw::uniacid()->where('reqSn', $this->getInfoParameter('reqSn'))->first();

        if (empty($withdraw)) {
            LcgLog::debug('withdrawBgRetUrl------------------------提现记录不存在');
            exit('failure');
        }

        $withdraw->status = $this->getBodyParameter('rstCode');
        $withdraw->save();

        echo 'SUCCESS';exit;
    }

    //出金同步
    public function withdrawPageRetUrl()
    {
        //LcgLog::debug('出金同步------------------------withdrawPageRetUrl', $this->parameters);

        redirect(Url::absoluteApp('myWallet', ['i' => \YunShop::app()->uniacid]))->send();
    }

    /**
     * 用户确认收货异步通知
     * @throws \app\common\exceptions\AppException
     */
    public function orderReceiveBgRetUrl()
    {
        LcgLog::debug('用户确认收货异步通知------------------------orderReceiveBgRetUrl', $this->parameters);

        if ($this->getInfoParameter('retCode') != 'MCG00000') {
            exit('failure');
        }

        $MerchantOrder = MerchantOrder::uniacid()->sentSn($this->getBodyParameter('jrnno'))->with(['hasOneOrder'])->first();

        if (empty($MerchantOrder)) {
            exit('failure');
        }
        //跟新商户订单状态
        MerchantOrder::updateData(['id'=>$MerchantOrder->id],['status'=>MerchantOrder::STATUS_END]);
        //订单收货
        OrderService::orderReceive(['order_id' => $MerchantOrder->hasOneOrder->id]);

        echo 'SUCCESS';exit();
    }
    //用户确认收货同步返回
    public function orderReceivePageRetUrl()
    {
        //LcgLog::debug('用户确认收货同步返回------------------------orderReceivePageRetUrl', $this->parameters);

        redirect(Url::absoluteApp('member/orderList/0', ['i' => \YunShop::app()->uniacid]))->send();
    }

    protected function verify()
    {
        return ($this->parameters['body'] && $this->parameters['info']);
    }

    protected function getBodyParameter($key)
    {
        return isset($this->parameters['body'][$key])?$this->parameters['body'][$key]: '';
    }
    protected function getInfoParameter($key)
    {
        return isset($this->parameters['info'][$key])?$this->parameters['info'][$key]: '';
    }




    /**
     * 支付日志
     *
     * @param $post
     */
    public function log()
    {
        //访问记录
        Pay::payAccessLog();
        //保存响应数据
        Pay::payResponseDataLog($this->getBodyParameter('mercOrdNo'), '龙存管', json_encode($this->parameters));
    }
}