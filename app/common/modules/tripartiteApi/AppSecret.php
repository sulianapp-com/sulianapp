<?php

namespace app\common\modules\tripartiteApi;

class AppSecret
{
    static public function get()
    {
        $secret = \Setting::get('tripartite-app-secret');
        if(!isset($secret)){
            $secret = base64_encode(md5(md5(\YunShop::app()->uniacid) . time() . range(0, 10000)));
            \Setting::set('tripartite-app-secret',$secret);
        }
        return $secret;
    }

}