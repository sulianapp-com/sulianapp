<?php

namespace app\backend\modules\setting\controllers;


use app\backend\modules\setting\models\Slide;
use app\common\components\BaseController;
use app\common\helpers\Url;
use Illuminate\Support\Facades\DB;
use app\common\models\Adv;

/**
* 商城广告
*/
class ShopAdvsController extends BaseController
{

	public function index()
	{
		$adv = Adv::first();

		if (request()->isMethod('post')) {

			$adv =  $adv ? $adv : (new Adv());

			$data['advs'] = request()->adv;
            $data['uniacid'] = \YunShop::app()->uniacid;

            $adv->fill($data);
            $bool = $adv->save();

            if (!$bool) {
            	$this->error('广告位保存失败');
            }

            return $this->message('广告位保存成功', Url::absoluteWeb('setting.shop-advs.index'));
		}

		return view('setting.adv.advertisement', [
			'adv' => $adv,
		]);
	}
}