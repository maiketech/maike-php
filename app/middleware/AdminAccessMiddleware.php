<?php

namespace app\middleware;

use Closure;
use think\facade\Config;
use think\Response;
use maike\core\Request;
use maike\interface\MiddlewareInterface;
use maike\exception\ApiException;
use app\service\system\Admin as AdminService;

/**
 * 管理员权限验证中间件
 * @package app\middleware
 */
class AdminAccessMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next)
    {
        if (!$request->adminId() || !$request->adminInfo()) {
            $statusCode = Config::get('core.status_code.not_login', 20000);
            ThrowError("未登录", $statusCode);
        }

        //权限验证
        $service = app()->make(AdminService::class);
        if (!$service->checkAuth($request)) {
            $statusCode = Config::get('core.status_code.access_denied', 30000);
            ThrowError("无权限", $statusCode);
        }

        return $next($request);
    }
}
