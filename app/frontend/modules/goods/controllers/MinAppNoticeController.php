<?php
/**
 * Created 
 * Author: 芸众商城 www.yunzshop.com 
 * Date: 2018/1/24 
 * Time: 下午1:43 
 */

namespace app\frontend\modules\goods\controllers;


use app\common\components\ApiController;
use app\common\models\Goods;
use app\common\models\Store;
use app\common\models\notice\MinAppTemplateMessage;
use app\common\models\MemberMiniAppModel;
use app\common\services\notice\SmallProgramNotice;
/**
 * 小程序发送模板消息
 */
class MinAppNoticeController extends ApiController
{
    /**
     * 支付成功后的 服务号通知消息的发送
     * form_id  表单提交场景下， 为 submit 事件带上的 formId；
     * 支付场景下，为本次支付的 prepay_id
     * $rawPost TODO 其参数解释请参考 sendTemplate()!!!
     */
    public function sendTemplatePaySuccess(){
             \YunShop::request()->storeid;
            $mini_app = MemberMiniAppModel::getFansById(\YunShop::request()->member_id);
            $openId =$mini_app->openid;        //接受人open_id
            $url = \YunShop::request()->url;    //跳转路径
            $form_id = \YunShop::request()->form_id;  //类型

            $min_small = new MinAppTemplateMessage;
            $temp_date = $min_small::getTemp(14);//获取数据表中的数据
            $rawPost = [
                'touser' => $openId ,
                'template_id' =>$temp_date->template_id,
                'page'=>$url,
                'form_id' => $form_id,
            ];
        $arr=explode(',',$temp_date->keyword_id);
        $i=1;
        foreach ($arr as $value){
            $keyword =  'keyword'.$i;
            $rawPost['data'][$keyword]['value'] = explode(":",$value)[1];
            $i++;
        }
        SmallProgramNotice::sendTemplate($rawPost,'sendTemplatePaySuccess');
    }

}
