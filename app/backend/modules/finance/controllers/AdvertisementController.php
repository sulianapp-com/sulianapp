<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 13:46
 */

namespace app\backend\modules\finance\controllers;

use app\backend\modules\finance\models\Advertisement;
use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;

class AdvertisementController extends BaseController
{
    public function index()
    {
        $search = request()->input('search');

        $list = Advertisement::getList($search)->paginate(15);

        $pager  = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view('finance.advertisement.adv_list', [
            'list' => $list,
            'pager'=> $pager,
            'search' => $search,
        ])->render();
    }

    public function add()
    {
        $adv = request()->input('adv');

        if (request()->isMethod('post')) {

            $adv_model = new Advertisement();

            $adv_model->setRawAttributes($adv);

            $validator = $adv_model->validator($adv_model->getAttributes());

            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                //其他字段赋值
                $adv_model->uniacid = \YunShop::app()->uniacid;
                if ($adv_model->save()) {
                    //显示信息并跳转
                    return $this->message('添加成功', Url::absoluteWeb('finance.advertisement.index'));
                } else {
                    $this->error('添加失败');
                }
            }
        }

        return view('finance.advertisement.adv_form', [
            'adv' => $adv,
        ])->render();
    }

    public function edit()
    {
        $id = intval(\Yunshop::request()->id);
        $adv_model = Advertisement::find($id);
        if (!$adv_model) {
            return $this->message('无记录或已被删除', '', 'error');
        }

        $requestData = \Yunshop::request()->adv;
        if ($requestData) {

            //数据保存
            $adv_model->setRawAttributes($requestData);
            $validator = $adv_model->validator($adv_model->getAttributes());

            if ($validator->fails()) {
                $this->error($validator->messages());
            } else {
                if ($adv_model->save()) {
                    //显示信息并跳转
                    return $this->message('修改成功', Url::absoluteWeb('finance.advertisement.index'));
                } else {
                    $this->error('修改失败');
                }
            }

        }

        return view('finance.advertisement.adv_form', [
            'adv' => $adv_model,
            'id' => $id,
        ])->render();
    }

    public function del()
    {
        $id = intval(\Yunshop::request()->id);
        $adv_model = Advertisement::find($id);
        if (!$adv_model) {
            return $this->message('无记录或已被删除', '', 'error');
        }


        if ($adv_model->delete()) {
            return $this->message('删除成功', Url::absoluteWeb('finance.advertisement.index'));
        }
        return $this->message('删除失败', '', 'error');
    }

    public function setStatus()
    {
        $id = \YunShop::request()->id;
        $adv_model = Advertisement::find($id);

       $type = $adv_model->status == 1 ? 0:1;

       $adv_model->status = $type;

        $adv_model->save();
        echo json_encode(["status" => $type, "result" => 1]);
    }
}