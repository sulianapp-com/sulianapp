<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/9/30
 * Time: 下午5:42
 */

namespace app\backend\modules\member\controllers;

use app\backend\modules\member\models\Member;
use app\common\components\BaseController;

class FastSearchController extends BaseController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $values = Member::searchLike(request('keyword'))->get();
        return $this->successJson('成功',$values);
    }
}