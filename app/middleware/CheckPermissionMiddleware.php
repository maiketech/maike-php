<?php

namespace app\middleware;

use Closure;
use think\facade\Config;
use think\Response;
use app\Request;
use maike\interfaces\MiddlewareInterface;
use maike\exceptions\ApiException;
use app\service\CheckPermission;

/**
 * 权限验证中间件
 * @package maike\middleware
 */
class CheckPermissionMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next)
    {
        if (!$request->userId() || !$request->userInfo()) {
            throw new ApiException("未登录", 20000);
        }

        if ($request->userInfo()['role_id']) {
            //权限验证
            // $service = app()->make(CheckPermission::class);
            // $service->check($request);
        } else {
        }

        return $next($request);
    }
}
