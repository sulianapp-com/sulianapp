<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 03/03/2017
 * Time: 12:19
 */

namespace app\backend\widgets\goods;


use app\common\components\Widget;
use app\backend\modules\goods\models\Privilege;
use app\backend\modules\member\models\MemberLevel;
use app\backend\modules\member\models\MemberGroup;
use app\backend\modules\goods\services\GoodsPrivilegeService;


class PrivilegeWidget extends Widget
{

    public function run()
    {
        $privilege = new Privilege();
        if ($this->goods_id && Privilege::getInfo($this->goods_id)) {
            $privilege = Privilege::getInfo($this->goods_id);
            $privilege->show_levels = (!empty($privilege->show_levels) || ($privilege->show_levels === 0 || $privilege->show_levels === '0')) ? explode(',', $privilege->show_levels) : '';
            $privilege->buy_levels = (!empty($privilege->buy_levels) || ($privilege->buy_levels === 0 || $privilege->buy_levels === '0')) ? explode(',', $privilege->buy_levels) : '';
            $privilege->show_groups = (!empty($privilege->show_groups) || ($privilege->show_groups === 0 || $privilege->show_groups === '0')) ? explode(',', $privilege->show_groups) : '';
            $privilege->buy_groups = (!empty($privilege->buy_groups) || ($privilege->buy_groups === 0 || $privilege->buy_groups === '0')) ? explode(',', $privilege->buy_groups) : '';
        }
        $levels = MemberLevel::getMemberLevelList();
        $groups = MemberGroup::getMemberGroupList();
        return view('goods.widgets.privilege', [
            'privilege' => $privilege,
            'levels' => $levels,
            'groups' => $groups
        ])->render();
    }
}