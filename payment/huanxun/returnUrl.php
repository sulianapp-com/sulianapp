<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/8/4
 * Time: 下午3:49
 */

define('IN_IA', true);

$boot_file = __DIR__ . '/../../../../framework/bootstrap.inc.php';

if (file_exists($boot_file)) {

    @include_once $boot_file;

}

include_once __DIR__ . '/../../app/laravel.php';

include_once __DIR__ . '/../../app/yunshop.php';