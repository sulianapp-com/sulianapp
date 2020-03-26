<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/19
 * Time: 下午4:10
 */

namespace app\backend\modules\setting\controllers;


use app\common\components\BaseController;
use app\common\helpers\Url;

class FormController extends BaseController
{
    public function index()
    {
        $pinyin = app('pinyin');

        $data = [];
        $set = \Setting::get('shop.form');
        $set = json_decode($set, true);

        $form = array_values(array_sort($set['form'], function ($value) {
            return $value['sort'];
        }));

        $set['form'] = $form;

        if (\request()->getMethod() == 'POST') {
            $form = \YunShop::request()->form?:[];
            $base = \YunShop::request()->base;

            if (!empty($form) && !empty($form['name'])) {
                foreach ($form['name'] as $key => $name) {
                    if (empty($name)) {
                        return $this->message('自定义表单数据错误', '', 'error');
                    }

                    $sort = $form['sort'][$key]?:99;
                    $pinyin = implode('', pinyin($name));
                    $data[] =['name'=>$name, 'sort'=>$sort, 'del'=>0, 'pinyin'=>$pinyin, 'value'=>''];

                }
            }

            if (\Setting::set('shop.form', json_encode(['base'=>$base, 'form'=>$data]))) {
                return $this->message('自定义表单数据保存成功', Url::absoluteWeb('setting.form.index'));
            } else {
                $this->error('自定义表单数据保存错误');
            }
        }
        return view('setting.form.index', [
            'set' => $set
        ])->render();
    }
}