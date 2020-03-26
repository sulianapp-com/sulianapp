<?php
$file = __DIR__ . '/../../framework/bootstrap.inc.php';

if (file_exists($file)) {
    include_once $file;
}

$extend = '';
$boot_file = __DIR__ . '/../../framework/bootstrap.inc.php';

if (file_exists($boot_file)) {
    include_once $boot_file;
} else {
    $extend = '/../..';
}

include_once __DIR__ . $extend . '/app/laravel.php';

include_once __DIR__ . $extend . '/app/yunshop.php';