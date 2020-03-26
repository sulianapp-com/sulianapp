<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/5/24
 * Time: 下午5:08
 */

namespace app\common\services\finance;


use app\common\events\MessageEvent;
use app\common\exceptions\AppException;
use app\common\models\finance\Balance;
use app\common\models\Member;
use app\common\services\credit\ConstService;
use app\common\services\credit\Credit;
use app\common\models\notice\MinAppTemplateMessage;
use app\common\services\MessageService as MsgService;
use app\common\models\notice\MessageTemp;

class BalanceChange extends Credit
{

    private $new_value;

    private $balanceSet = array();

    /**
     * 实现基类中的抽象方法
     * 通过基类 data 中的 member_id 获取会员信息
     * @return mixed
     */
    public function getMemberModel()
    {
        return $this->memberModel = Member::select('uid', 'avatar', 'mobile','nickname', 'realname', 'credit2')->where('uid', $this->data['member_id'])->lockForUpdate()->first() ?: false;
    }

    /**
     * 实现基类中的抽象方法
     * 记录数据写入
     * @return bool|\Illuminate\Support\MessageBag|string
     */
    public function recordSave()
    {
        $recordModel = new Balance();

        $recordModel->fill($this->getRecordData());
        $validator = $recordModel->validator();
        if ($validator->fails()) {
            return $validator->messages();
        }
        return $recordModel->save() ? true : '明细记录写入出错';
    }

    /**
     * 实现基类中的抽项方法
     * @return bool|string
     */
    public function updateMemberCredit()
    {
        $this->memberModel->credit2 = $this->new_value;

        if ($this->memberModel->save()) {
            $this->sendSmsMessage();
            $this->sendMessage();

            return true;
        }
        return '写入会员余额失败';
        //return $this->memberModel->save() ? true : '写入会员余额失败';
    }


    /**
     * @return bool
     * @throws AppException
     */
    public function validatorData()
    {
        $this->new_value = bcadd($this->memberModel->credit2, $this->change_value, 2);
        if ($this->new_value < 0) {
            throw new AppException('余额不足');
        }
        if (!$this->relation()) {
            throw new AppException('该订单已经提交过，不能重复使用');
        }

        return true;
    }

    /**
     * 实现基类中的抽项方法
     * @return bool
     * @throws AppException
     */
    public function validatorOzy()
    {
        return true;
    }


    public function transfer(array $data)
    {
        if (!$data['recipient']) {
            throw new AppException('被转让者不存在');
        }

        $result = parent::transfer($data);

        $data['member_id'] = $data['recipient'];
        return $result === true ? $this->addition($data) : $result;
    }

    public function convert(array $data)
    {
        $result = parent::convert($data);
        return $result;
    }

    public function convertCancel(array $data)
    {
        $result = parent::convertCancel($data);
        return $result;
    }

    public function deduct(array $data)
    {
        $result = parent::consume($data);
        return $result;
    }

    /**
     * 检测单号是否可用，为空则生成唯一单号
     * @return bool|string
     */
    private function relation()
    {
        if ($this->data['relation']) {
            $result = Balance::ofOrderSn($this->data['relation'])->ofSource($this->source)->ofMemberId($this->data['member_id'])->first();
            //dd($result);
            if ($result) {
                return false;
            }
            return $this->data['relation'];
        }
        return $this->createOrderSN();
    }

    /**
     * 生成唯一单号
     * @return string
     */
    public function createOrderSN()
    {
        $ordersn = createNo('BC', true);
        while (1) {
            if (!Balance::ofOrderSn($ordersn)->first()) {
                break;
            }
            $ordersn = createNo('BC', true);
        }
        return $ordersn;
    }

    /**
     * 明细记录 data 数组
     * @return array
     */
    private function getRecordData()
    {
        $thirdStatus = empty($this->data['thirdStatus']) ? 1 : $this->data['thirdStatus'];

        return [
            'uniacid' => \YunShop::app()->uniacid,
            'member_id' => $this->memberModel->uid,
            'old_money' => $this->memberModel->credit2 ?: 0,
            'change_money' => $this->change_value,
            'new_money' => $this->new_value,
            'type' => $this->type,
            'service_type' => $this->source,
            'serial_number' => $this->relation(),
            'operator' => $this->data['operator'],
            'operator_id' => $this->data['operator_id'],
            'remark' => $this->data['remark'],
            'thirdStatus' => $thirdStatus
        ];
    }

    /**
     * 余额变动消息通知
     */
    private function sendMessage()
    {
        date_default_timezone_set('PRC');
        if ($this->change_value == 0) {
            return;
        }
        $this->balanceSet = \Setting::get('finance.balance');
        //检查余额是否达到下限

        if ($this->balanceSet['blance_floor'] > $this->new_value && $this->balanceSet['blance_floor_on'] == 1) {
            $this->checkBalanceFloor();
        }

        $template_id = \Setting::get('shop.notice')['balance_change'];
        \Log::info('余额变动通知', $template_id);
        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '昵称', 'value' => $this->memberModel->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i', time())],
            ['name' => '余额变动金额', 'value' => $this->change_value],
            ['name' => '余额变动类型', 'value' => (new ConstService(''))->sourceComment()[$this->source]],
            ['name' => '变动后余额数值', 'value' => $this->new_value]
        ];
        $news_link = MessageTemp::find($template_id)->news_link;
        $news_link = $news_link ?: '';
        event(new MessageEvent($this->memberModel->uid, $template_id, $params, $url = $news_link));

        //小程序消息通知
        $is_open = MinAppTemplateMessage::getTitle('账户余额提醒');
        if (!$is_open->is_open) {
            return;
        }
        $miniParams = [
            'keyword1' => ['value' => $this->memberModel->nickname],// 会员昵称
            'keyword2' => ['value' => date('Y-m-d H:i', time())],//变动时间
            'keyword3' => ['value' => $this->change_value],// 变动金额
            'keyword4' => ['value' => $this->new_value],//  当前余额
            'keyword5' => ['value' => (new ConstService(''))->sourceComment()[$this->source]],// 变动类型
        ];
        $this->miniSendToShops($is_open->template_id, $miniParams, $this->memberModel->uid);
    }

    protected function miniSendToShops($templateId, $msg, $uid)
    {
        if (empty($templateId)) {
            return;
        }

        \Log::debug('===============', [$templateId]);
        MsgService::MiniNotice($templateId, $msg, $uid);
    }

    //余额下限消息通知
    public function checkBalanceFloor()
    {
        $template_id = \Setting::get('shop.notice')['balance_deficiency'];
        if(!$template_id){
            return true;
        }

        if(!$this->balanceSet['blance_floor']){
            return true;
        }

        //未设置直接报错
        if(empty($this->balanceSet['balance_message_type'])){
            return true;
        }

        //只有三种情况
        if(in_array($this->balanceSet['balance_message_type'],[1,2,3]) != true){
            return true;
        }

        //指定会员等级
        if($this->balanceSet['balance_message_type'] == 2){
            if($this->memberModel->yzMember->level_id != $this->balanceSet['level_limit']){
                return true;
            }
        }

        //指定会员
        if($this->balanceSet['balance_message_type'] == 1){
            if(in_array($this->memberModel->uid,explode(',',$this->balanceSet['uids'])) != true){
                return true;
            }
        }

        //指定会员分组
        if($this->balanceSet['balance_message_type'] == 3){
            if($this->memberModel->yzMember->group_id != $this->balanceSet['group_type']){
                return true;
            }
        }

        $params = [
            ['name' => '商城名称', 'value' => \Setting::get('shop.shop')['name']],
            ['name' => '昵称', 'value' => $this->memberModel->nickname],
            ['name' => '时间', 'value' => date('Y-m-d H:i', time())],
            ['name' => '通知额度', 'value' => $this->balanceSet['blance_floor']],
            ['name' => '当前余额', 'value' => $this->new_value]
        ];
        $news_link = MessageTemp::find($template_id)->news_link;
        $news_link = $news_link ?: '';
        event(new MessageEvent($this->memberModel->uid, $template_id, $params, $url = $news_link));
    }

    /**
     *  充值短信
     * @return bool
     */
    private function sendSmsMessage()
    {
        try {
            if($this->source != 1){
                \Log::debug('不是充值不需要发送短信');
                return true;
            }

            if (!$this->memberModel->mobile) {
                \Log::debug('未获取到该会员手机号');
                return true;
            }

            $smsSet = \Setting::get('shop.sms');
            if ($smsSet['type'] != 3 && $smsSet['aly_templatereChargeCode'] == null) {
                \Log::debug('短信功能设置' . $smsSet);
                return true;
            }

            $name = \Setting::get('shop.shop')['name'];
            $aly_sms = new \app\common\services\aliyun\AliyunSMS(trim($smsSet['aly_appkey']), trim($smsSet['aly_secret']));
            $response = $aly_sms->sendSms(
                $smsSet['aly_signname'], // 短信签名
                $smsSet['aly_templatereChargeCode'], // 发货提醒短信
                $this->memberModel->mobile, // 短信接收者
                Array(  // 短信模板中字段的值
                    'preshop' => $name,
                    'date' => date("m月d日",time()),
                    'amount' => $this->change_value,
                    'amounts' => $this->new_value,
                    'endshop' => $name,
                )
            );
            if ($response->Code == 'OK' && $response->Message == 'OK') {
                \Log::debug($this->memberModel->mobile. '阿里云短信发送成功');
            } else {
                \Log::debug($this->memberModel->mobile . '阿里云短信发送失败' . $response->Message);
            }
            return true;
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
            return true;
        }

    }

}