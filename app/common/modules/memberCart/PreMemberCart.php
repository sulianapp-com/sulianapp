<?php

namespace app\common\modules\memberCart;

use app\common\models\MemberCart;

class PreMemberCart extends MemberCart
{

    public function getUniacidAttribute()
    {
        if (!$this->attributes['uniacid']) {
            $this->attributes['uniacid'] = \YunShop::app()->uniacid;
        }
        return $this->attributes['uniacid'];
    }

    public function getOptionIdAttribute()
    {
        if (!$this->attributes['option']) {
            $this->attributes['option'] = 0;
        }
        return $this->attributes['option'];
    }

    public function getTotalAttribute()
    {
        if (!$this->attributes['total']) {
            $this->attributes['total'] = 1;
        }
        return $this->attributes['total'];
    }
}