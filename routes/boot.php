<?php
Route::group(['namespace' => 'frontend\modules\wechat\controllers'], function () {
    Route::any('wechat', 'IndexController@index');
});
