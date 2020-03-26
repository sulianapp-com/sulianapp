<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/5/16
 * Time: 15:55
 */

namespace app\backend\modules\setting\controllers;


use app\common\components\BaseController;
use app\common\modules\template\Template;
use app\common\services\notice\WechatApi;
use app\common\models\TemplateMessageDefault;
use app\common\models\notice\MessageTemp;


class DefaultNoticeController extends BaseController
{
    private $WechatApiModel;
    private $TemplateDefaultModel;
    private $wechat_list;
    private $MessageTempModel;

    /**
     * DefaultNoticeController constructor.
     * @throws \app\common\exceptions\ShopException
     */
    public function preAction() {
        parent::preAction();
        $this->WechatApiModel = new WechatApi();
        $this->MessageTempModel = new MessageTemp();
        $this->TemplateDefaultModel = new TemplateMessageDefault();
        $this->wechat_list = collect($this->WechatApiModel->getTmpList()['template_list']);//获取微信模版列表并转化为集合
    }

    public function index() {
        $notice_name = request()->notice_name;
        $setting_name = request()->setting_name;

        $notice = \Setting::get($setting_name);//获取设置信息

        $message_template = $this->MessageTempModel->getTempIdByNoticeType($notice_name);//获取消息通知模版
        $has_template_id = $this->wechat_list->where('template_id',$message_template->template_id)->first();//查询是否存在template_id,不存在则新建

        if ($has_template_id) {
            $notice[$notice_name] = (string)$message_template->id;
        } else {
            $notice[$notice_name] = $this->createDefaultMessageTemp($notice_name);
        }
        \Setting::set($setting_name, $notice);
        echo json_encode([
            'result' => '1',
            'id' => $notice[$notice_name],
        ]);
    }

    public function cancel() {
        $notice_name = \YunShop::request()->notice_name;
        $setting_name = \YunShop::request()->setting_name;
        $notice = \Setting::get($setting_name);
        $notice[$notice_name] = "0";
        \Setting::set($setting_name, $notice);
        echo json_encode([
            'result' => '1',
        ]);
    }

    public function store()
    {
        $notice_name = \YunShop::request()->notice_name;
        $setting_name = \YunShop::request()->setting_name;

        $message_template = $this->MessageTempModel->getTempIdByNoticeType($notice_name);//获取消息通知模版
        $has_template_id = $this->wechat_list->where('template_id',$message_template->template_id)->first();//查询是否存在template_id,不存在则新建

        if ($has_template_id){
            $item = (string)$message_template->id;
        } else {
            if ($message_template) {
                $message_template->delete();
            }
            $item = $this->createDefaultMessageTemp($notice_name);
        }
        \Setting::set($setting_name, $item);
        $setting = explode('.',$setting_name);
        if($setting[0] == 'love') {
            \Cache::forget('plugin.love.set_' . \YunShop::app()->uniacid);
        }
        echo json_encode([
            'result' => '1',
            'id' => (string)$item,
        ]);
    }

    public function storeCancel() {
        $setting_name = \YunShop::request()->setting_name;
        $item = "0";
        \Setting::set($setting_name, $item);
        $setting = explode('.',$setting_name);
        if($setting[0] == 'love') {
            \Cache::forget('plugin.love.set_' . \YunShop::app()->uniacid);
        }
        echo json_encode([
            'result' => '1',
        ]);
    }

    protected function createDefaultMessageTemp($notice_name)
    {
        $template_id_short = '';
        $template_default_data_1 = [];
        foreach(Template::current()->getNoticeItems() as $key => $item) {
            if ($key == $notice_name) {
                $template_id_short = $item['template_id_short'];
                unset($item['template_id_short']);
                $template_default_data_1 = $item;
                break;
            }
        }
        $template_data = $this->TemplateDefaultModel->getData($template_id_short);
        $has_template = $this->wechat_list->where('template_id',$template_data->template_id)->first();
        if (empty($has_template) || empty($template_data->template_id)) {
            if ($template_data) {
                $template_data->delete();
            }
            $template_id = $this->WechatApiModel->getTemplateIdByTemplateIdShort($template_id_short);
            if (empty($template_id)) {
                echo json_encode([
                    'result' => '0',
                    'msg' => '获取微信模版失败',
                ]);exit();
            }
            
            $this->TemplateDefaultModel->template_id_short = $template_id_short;
            $this->TemplateDefaultModel->template_id = $template_id;
            $this->TemplateDefaultModel->uniacid = \YunShop::app()->uniacid;
            $this->TemplateDefaultModel->save();
            $template_data['template_id'] = $template_id;
        }
        $template_default_data_2 = [
            'uniacid' => \YunShop::app()->uniacid,
            'template_id' => $template_data['template_id'],
            'is_default' => 1,
            'notice_type' => $notice_name,
        ];
        $template_default_data = array_merge($template_default_data_1, $template_default_data_2);

        $ret = $this->MessageTempModel->updateOrCreate(['notice_type' => $notice_name], $template_default_data);
        return (string)$ret->id;
    }

}