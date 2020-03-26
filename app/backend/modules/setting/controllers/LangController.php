<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/3/9
 * Time: 下午5:26
 */

namespace app\backend\modules\setting\controllers;

use app\common\components\BaseController;
use app\common\helpers\Url;
use app\common\facades\Setting;
use app\common\models\AccountWechats;
use app\common\services\MyLink;

class LangController extends BaseController
{
    public $_lang;

    public function preAction()
    {
        parent::preAction();
        $this->_lang = 'zh_cn';
    }

    /**
     * 商城设置
     * @return mixed
     */
    public function index()
    {
        $lang = Setting::get('shop.lang', ['lang' => 'zh_cn']);

        $requestModel = \YunShop::request()->setdata;

        if ($requestModel) {

            $data['lang'] = $this->_lang;
            $data[$this->_lang] = $requestModel;

            $request = Setting::set('shop.lang', $data);

            if ($request) {
                return $this->message('语言设置成功', Url::absoluteWeb('setting.lang.index'));
            } else {
                $this->error('语言设置失败');
            }
        }

        return view('setting.shop.lang', [
            'set' => $lang[$lang['lang']]
        ])->render();
    }


}