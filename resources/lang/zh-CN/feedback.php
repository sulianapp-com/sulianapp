<?php

if (env('APP_DEBUG')) {
    return require_once __DIR__.'/debug.php';
} else {
    return require_once __DIR__.'/deploy.php';
}