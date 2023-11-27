<?php

use think\facade\Route;
use think\facade\Config;
use think\Response;
use maike\middleware\AllowCrossDomain;
use app\middleware\UserTokenMiddleware;
use app\middleware\UserAccessMiddleware;

//无需验证Token及权限的路由
Route::group(function () {
    Route::rule('login', 'Login/login');
    Route::post('send_mobile_code', 'Login/send_mobile_code');
    Route::rule('login/:action', 'Login/:action');
    Route::rule('page/:action', 'Page/:action');
    Route::rule('article/list', 'article/list');
    Route::rule('article/detail', 'article/detail');
    Route::rule('article/index', 'article/index');
    Route::rule('article/category', 'article/category');
    Route::rule('help/:action', 'help/:action');
    Route::rule('user/index', 'user/index');
})->middleware([
    AllowCrossDomain::class
]);

//只验证Token不验证权限
Route::group(function () {
    // 表单组件选项
    Route::rule('*/options', '*/options');
})->middleware([
    AllowCrossDomain::class,
    UserTokenMiddleware::class
]);

//验证Token及权限
Route::group(':controller', function () {
    Route::rule(':action', ':controller/:action');
})->middleware([
    AllowCrossDomain::class,
    UserTokenMiddleware::class,
    UserAccessMiddleware::class
]);

Route::miss(function () {
    if (app()->request->isOptions()) {
        $header = Config::get('cookie.header');
        $header['Access-Control-Allow-Origin'] = app()->request->header('origin');
        return Response::create('ok')->code(200)->header($header);
    }
    return Response::create()->code(404);
});
