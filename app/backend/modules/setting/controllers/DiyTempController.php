<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/11/8
 * Time: 下午4:20
 */

namespace app\backend\modules\setting\controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\notice\MessageTemp;

class DiyTempController extends BaseController
{
    private $temp_model;

    public function index()
    {
        $kwd = request()->keyword;
        $list = MessageTemp::fetchTempList($kwd)->orderBy('id', 'desc')->paginate(10);
        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('setting.diytemp.list', [
            'list' => $list,
            'pager' => $pager,
            'kwd' => $kwd
        ])->render();
    }

    public function add()
    {
        if (request()->temp) {
            $temp_model = new MessageTemp();
            $ret = $temp_model::create($temp_model::handleArray(request()->temp));
            if (!$ret) {
                return $this->message('添加模板失败', Url::absoluteWeb('setting.diy-temp.index'), 'error');
            }
            return $this->message('添加模板成功', Url::absoluteWeb('setting.diy-temp.index'));
        }

        return view('setting.diytemp.detail', [

        ])->render();
    }

    public function edit()
    {
        $this->verifyParam();
        if (request()->temp) {
            $this->temp_model->fill(MessageTemp::handleArray(request()->temp));
            $ret = $this->temp_model->save();
            if (!$ret) {
                return $this->message('修改模板失败', Url::absoluteWeb('setting.diy-temp.index'), 'error');
            }
            return $this->message('修改模板成功', Url::absoluteWeb('setting.diy-temp.index'));
        }

        return view('setting.diytemp.detail', [
            'temp' => $this->temp_model
        ])->render();
    }

    public function del()
    {
        $this->verifyParam();
        $this->temp_model->delete();
        return $this->message('删除成功', Url::absoluteWeb('setting.diy-temp.index'));
    }

    public function tpl()
    {
        return view('setting.diytemp.tpl.common', [
            'kw' => request()->kw,
            'tpkw' => request()->tpkw,
        ])->render();
    }

    private function verifyParam()
    {
        $temp_id = intval(request()->id);
        if (!$temp_id) {
            return $this->message('参数错误', Url::absoluteWeb('setting.diy-temp.index'), 'error');
        }
        $temp_model = MessageTemp::getTempById($temp_id)->first();
        if (!$temp_model) {
            return $this->message('未找到数据', Url::absoluteWeb('setting.diy-temp.index'), 'error');
        }
        $this->temp_model = $temp_model;
    }

    public function query()
    {
        $kwd = trim(request()->keyword);
        if ($kwd) {
            $temp_list = MessageTemp::fetchTempList($kwd)->get();
            return view('setting.diytemp.query', [
                'temp_list' => $temp_list
            ])->render();
        }
    }
}