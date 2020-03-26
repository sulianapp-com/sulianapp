<?php

namespace app\backend\controllers;

use app\common\components\BaseController;

class QuickEntryController extends BaseController
{
    public function index(){
        $entry = module_entry(request()->input('eid'));

        switch ($entry['do']) {
            case 'shop':
                return redirect('?c=site&a=entry&do=shop&m=yun_shop&route=index.index');
                break;
            case 'member':
                return redirect('?c=site&a=entry&do=shop&m=yun_shop&route=member.member.index');
                break;
            case 'order':
                return redirect('?c=site&a=entry&do=shop&m=yun_shop&route=order.list');
                break;
            case 'finance':
                return redirect('?c=site&a=entry&do=shop&m=yun_shop&route=finance.withdraw-set.see');
                break;
            case 'plugins':
                return redirect('?c=site&a=entry&do=shop&m=yun_shop&route=plugins.get-plugin-data');
                break;
            case 'system':
                return redirect('?c=site&a=entry&do=shop&m=yun_shop&route=setting.shop.index');
                break;
            default:
                return redirect('?c=site&a=entry&do=shop&m=yun_shop&route=index.index');
        }
    }
}