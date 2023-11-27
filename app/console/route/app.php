<?php

use think\facade\Route;
use think\facade\Config;
use think\Response;
use maike\middleware\AllowCrossDomain;
use app\middleware\AdminTokenMiddleware;
use app\middleware\AdminAccessMiddleware;
use app\middleware\LogMiddleware;

//Route::get('captcha/[:id]', "\\think\\captcha\\CaptchaController@index");

Route::any("/", function () {
    $path = public_path() . "/console/index.html";
    $html = file_get_contents($path);
    return $html;
});

//无需验证Token及权限的路由
Route::group(function () {
    Route::rule('login', 'Login/login');
    Route::rule('login/:action', 'Login/:action');
})->middleware([
    AllowCrossDomain::class
]);

//只验证Token不验证权限
Route::group(function () {
    // 表单组件选项
    Route::post('clear_cache', 'Home/clear_cache');
    Route::rule('*/get_item_options', '*/get_item_options');
    Route::rule('*/get_options', '*/get_options');
    // 上传附件
    Route::rule('upload/:action', 'upload/:action');
    Route::rule('system.attachment/:action', 'system.attachment/:action');
})->middleware([
    AllowCrossDomain::class,
    AdminTokenMiddleware::class,
    LogMiddleware::class
]);

//验证Token及权限
Route::group(':controller', function () {
    Route::rule(':action', ':controller/:action');
})->middleware([
    AllowCrossDomain::class,
    AdminTokenMiddleware::class,
    AdminAccessMiddleware::class,
    LogMiddleware::class
]);

Route::miss(function () {
    if (app()->request->isOptions()) {
        $header = Config::get('cookie.header');
        $header['Access-Control-Allow-Origin'] = app()->request->header('origin');
        return Response::create('ok')->code(200)->header($header);
    }
    return Response::create()->code(404);
});
