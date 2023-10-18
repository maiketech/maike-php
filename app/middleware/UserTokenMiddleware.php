<?php

namespace maike\middlewares;

use Closure;
use think\facade\Config;
use think\Response;
use maike\interfaces\MiddlewareInterface;
use maike\exceptions\ApiException;
use maike\core\Request;
use app\model\User as UserModel;

/**
 * 用户Token认证
 * @package maike\middlewares
 */
class UserTokenMiddleware implements MiddlewareInterface
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
            throw new ApiException("Invalid " . $tokenKey, $coreConfig['access_denied'] ?? 30000);
        }

        //获取用户登录
        $service = app()->make(UserModel::class);
        $userInfo = $service->parseToken($token);
        if ($userInfo) {
            $userInfo = $userInfo->hidden(['password'])->toArray();
        }

        Request::macro('userId', function () use (&$userInfo) {
            return isset($userInfo['user_id']) ? $userInfo['user_id'] : 0;
        });

        Request::macro('userInfo', function () use (&$userInfo) {
            return $userInfo;
        });

        return $next($request);
    }
}
