<?php

namespace app\middleware;

use Closure;
use think\facade\Config;
use think\Response;
use maike\core\Request;
use maike\interface\MiddlewareInterface;
use app\service\user\UserService;

/**
 * 管理员权限验证中间件
 * @package app\middleware
 */
class UserAccessMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next)
    {
        if (!$request->userId() || !$request->userInfo()) {
            $statusCode = Config::get('core.status_code.not_login', 20000);
            ThrowError("未登录", $statusCode);
        }

        // $statusCode = Config::get('core.status_code.access_denied', 30000);
        // if ($request->userInfo()['role_id']) {
        //     //权限验证
        //     $service = app()->make(UserService::class);
        //     if (!$service->checkPermission($request)) {
        //         ThrowError("无权限", $statusCode);
        //     }
        // } else {
        //     // 无权限
        //     ThrowError("无权限", $statusCode);
        // }

        return $next($request);
    }
}
