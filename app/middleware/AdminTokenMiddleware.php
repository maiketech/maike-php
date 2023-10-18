<?php

namespace maike\middlewares;

use Closure;
use think\facade\Config;
use think\Response;
use maike\interfaces\MiddlewareInterface;
use maike\exceptions\ApiException;
use maike\core\Request;
use app\model\Admin as AdminModel;

/**
 * 后台管理员Token认证
 * @package maike\middlewares
 */
class AdminTokenMiddleware implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next)
    {
        $coreConfig = Config::get('core');
        // 判断Token是否有效
        $tokenKey = isset($coreConfig['token_key']) && !empty($coreConfig['token_key']) ? $coreConfig['token_key'] : 'M-Token';
        $token = $request->header($tokenKey);
        if (empty($token)) {
            // 无效Token
            $statusCode = isset($coreConfig['status_code']['access_denied']) ? $coreConfig['status_code']['access_denied'] : 30000;
            throw new ApiException("Invalid " . $tokenKey, $statusCode);
        }

        //获取管理员用户登录
        $service = app()->make(AdminModel::class);
        $adminInfo = $service->parseToken($token);
        if ($adminInfo) {
            $adminInfo = $adminInfo->hidden(['password'])->toArray();
        }

        Request::macro('adminId', function () use (&$adminInfo) {
            return isset($adminInfo['admin_id']) ? $adminInfo['admin_id'] : 0;
        });

        Request::macro('adminInfo', function () use (&$adminInfo) {
            return $adminInfo;
        });

        return $next($request);
    }
}
