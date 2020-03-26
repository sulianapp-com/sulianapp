<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/7
 * Time: 17:21
 */

namespace app\backend\widgets\goods;


use app\common\components\Widget;
use app\common\models\goods\InvitePage;

class InvitePageWidget extends Widget
{
    public function run()
    {
        $goods_id = request()->id;
        $invitePageModel = InvitePage::getDataByGoodsId($goods_id);

        return view('goods.widgets.invite-page',[
            'data' => $invitePageModel
        ])->render();
    }
}