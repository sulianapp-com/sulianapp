<?php
Route::group(['namespace' => 'platform\controllers'], function () {
    Route::get('login', 'LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout');
    Route::post('logout', 'LoginController@logout');

    Route::get('register', 'RegisterController@showRegistrationForm')->name('admin.register');
    Route::post('register', 'RegisterController@register');

    Route::any('changePwd', 'ResetpwdController@changePwd'); //修改密码
    Route::any('sendCode', 'ResetpwdController@sendCode'); //发送验证码
    Route::any('getCaptcha', 'ResetpwdController@getCaptcha'); //发送图形验证码
    Route::any('checkCode', 'ResetpwdController@checkCode'); //检查验证码
    Route::any('detail', 'ResetpwdController@detail'); //检查验证码
    Route::any('auth', 'ResetpwdController@authPassword'); // 管理员修改密码


    Route::get('/', 'IndexController@index');

    // 安装向导
    Route::post('install/agreement', 'InstallController@agreement');     // 安装协议
    Route::post('install/check', 'InstallController@check');     // 运行环境检测
    Route::post('install/file_power', 'InstallController@filePower');     // 文件权限设置
    Route::post('install/set_info', 'InstallController@setInformation');     // 账号设置
    Route::post('install/create_data', 'InstallController@createData');     // 创建数据
    Route::post('install/delete', 'InstallController@delete');     // 删除控制器
    Route::get('login/site', 'LoginController@site');               // 登录页面返回数据
});

Route::group(['prefix' => 'system/upload', 'namespace' => 'platform\modules\system\controllers'], function (){
    // 上传
    Route::any('upload', 'UploadController@upload');     // 上传图片 or 视频 or 音频
    Route::any('image', 'UploadController@image');       // 图片列表
    Route::any('fetch', 'UploadController@fetch');       // 获取远程图片
    Route::any('delete', 'UploadController@delete');     // 删除图片
    Route::any('video', 'UploadController@video');       // 音频视频列表
});

Route::group(['middleware' => ['auth:admin', 'authAdmin', 'globalparams', 'shopbootstrap', 'check']], function () {

    Route::get('index', ['as' => 'admin.index', 'uses' => '\app\platform\controllers\IndexController@index']);
    //清除缓存
    Route::get('clear', ['as' => 'admin.clear', 'uses' => '\app\platform\controllers\ClearController@index']); 

    //用户管理
    Route::group(['namespace' => 'platform\modules\user\controllers'], function () {
        //权限管理路由
        Route::get('permission/{parentId}/create', ['as' => 'admin.permission.create', 'uses' => 'PermissionController@create']);
        Route::post('permission/{parentId}/create', ['as' => 'admin.permission.create', 'uses' => 'PermissionController@store']);
        Route::get('permission/{id}/edit', ['as' => 'admin.permission.edit', 'uses' => 'PermissionController@edit']);
        Route::post('permission/{id}/edit', ['as' => 'admin.permission.edit', 'uses' => 'PermissionController@update']);
        Route::get('permission/{id}/delete', ['as' => 'admin.permission.destroy', 'uses' => 'PermissionController@destroy']);
        Route::get('permission/{parentId}/index', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']);
        Route::get('permission/index', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']);
        Route::post('permission/index', ['as' => 'admin.permission.index', 'uses' => 'PermissionController@index']);

        //角色管理路由
        Route::get('role/index', ['as' => 'admin.role.index', 'uses' => 'RoleController@index']);
        Route::post('role/index', ['as' => 'admin.role.index', 'uses' => 'RoleController@index']);
        Route::get('role/create', ['as' => 'admin.role.create', 'uses' => 'RoleController@create']);
        Route::post('role/create', ['as' => 'admin.role.create', 'uses' => 'RoleController@store']);
        Route::get('role/{id}/edit', ['as' => 'admin.role.edit', 'uses' => 'RoleController@edit']);
        Route::post('role/{id}/edit', ['as' => 'admin.role.edit', 'uses' => 'RoleController@update']);
        Route::get('role/{id}/delete', ['as' => 'admin.role.destroy', 'uses' => 'RoleController@destroy']);
    });

    // 站点管理
    Route::group(['prefix' => 'system', 'namespace' => 'platform\modules\system\controllers'], function (){
        // 站点设置
        Route::any('site', 'SiteController@index');
        // 附件设置-全局设置
        Route::any('globals', 'AttachmentController@globals');
         // 附件设置-远程设置
        Route::any('remote', 'AttachmentController@remote');
        // 附件设置-远程设置-阿里云搜索bucket
        Route::post('bucket', 'AttachmentController@bucket');
        // 附件设置-远程设置-测试阿里云配置
        Route::any('oss', 'AttachmentController@oss');
        // 附件设置-远程设置-测试腾讯云配置
        Route::any('cos', 'AttachmentController@cos');
        // 系统升级
        Route::any('update/index', 'UpdateController@index');
        // 检查更新
        Route::get('update/verifyCheck', 'UpdateController@verifyCheck');
        // 后台文件
        Route::any('update/fileDownload', 'UpdateController@fileDownload');
        // 版权
        Route::any('update/pirate', 'UpdateController@pirate');
        // 前端压缩包下载
        Route::any('update/startDownload', 'UpdateController@startDownload');
        // 框架压缩包下载
        Route::any('update/FrameworkDownload', 'UpdateController@startDownloadFramework');
        //短信设置
        Route::any('sms', 'AttachmentController@sms');
        // 站点注册-显示
        Route::get('siteRegister/index', 'SiteRegisterController@index');
        // 站点注册-获取城市
        Route::post('siteRegister/getcity', 'SiteRegisterController@getcity');
        // 站点注册-获取地区
        Route::post('siteRegister/getarea', 'SiteRegisterController@getarea');
        // 站点注册-获取手机验证码
        Route::post('siteRegister/sendSms', 'SiteRegisterController@sendSms');
        // 站点注册-注册
        Route::post('siteRegister/register', 'SiteRegisterController@register');
        // 站点注册-重置密钥
        Route::post('siteRegister/reset', 'SiteRegisterController@resetSecretKey');
    });

    // 用户管理
    Route::group(['prefix' => 'user', 'namespace' => 'platform\modules\user\controllers'], function (){
        // 用户列表
        Route::post('index', 'AdminUserController@index');
        // 添加用户
        Route::any('create', 'AdminUserController@create');
        // 用户编辑
        Route::any('edit', 'AdminUserController@edit');
        // 用户修改状态
        Route::post('status', 'AdminUserController@status');
        // 用户修改密码
        Route::post('change', 'AdminUserController@change');
        // 用户信息修改
        Route::post('modify_user', 'AdminUserController@modifyCurrentUser');
        // 发送手机验证码
        Route::post('send_code', 'AdminUserController@sendCode');
        // 发送新手机号验证码
        Route::post('send_new_code', 'AdminUserController@sendNewCode');
        // 修改手机号
        Route::post('modify_mobile', 'AdminUserController@modifyMobile');
        // 平台列表
        Route::post('app_list', 'AdminUserController@applicationList');
        // 店员用户列表
        Route::post('clerk_list', 'AdminUserController@clerkList');
    });
 
    Route::group(['namespace' => 'platform\modules\application\controllers'], function () {
		// 平台管理
        Route::any('application/', 'ApplicationController@index');
		//修改应用
		Route::post('application/update/{id}', 'ApplicationController@update');
		//启用禁用或恢复应用及跳转链接
		Route::get('application/switchStatus/{id}', 'ApplicationController@switchStatus');
		//置顶或取消置顶平台
		Route::get('application/setTop/{id}', 'ApplicationController@setTop');
        //详情
        Route::any('application/getApp', 'ApplicationController@getApp');
		//添加应用
		Route::post('application/add/', 'ApplicationController@add');
        //添加应用权限检测
        Route::any('application/checkAddRole/', 'ApplicationController@checkAddRole');
		//删除 加入回收站
		Route::get('application/delete/{id}', 'ApplicationController@delete');
		//回收站
		Route::any('application/recycle/', 'ApplicationController@recycle');
		//图片上传
        Route::post('all/upload/', 'AllUploadController@upload');
        //本地图片列表
        Route::any('all/list/', 'AllUploadController@getLocalList');
        //删除图片列表中的数据
        Route::any('all/delImg/', 'AllUploadController@delLocalImg');

		//平台用户管理
		Route::any('appuser/', 'AppuserController@index');
		//添加平台用户
		Route::any('appuser/add', 'AppuserController@add');
		//删除平台用户
		Route::get('appuser/delete', 'AppuserController@delete');
		//搜索会员
		Route::any('appuser/checkname', 'AppuserController@checkname');
	});
});

Route::get('/', function () {
    return redirect('/admin/index');
});