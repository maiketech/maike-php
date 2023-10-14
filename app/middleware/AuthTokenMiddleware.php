<?php

namespace app\middleware;

use Closure;
use think\facade\Config;
use app\Request;
use think\Response;
use maike\interfaces\MiddlewareInterface;
use maike\exceptions\ApiException;
use app\model\User as UserModel;

/**
 * Token认证
 * @package maike\middleware
 */
class AuthTokenMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next)
    {
        $token = $request->header(Config::get('api.token_key', 'M-Token'));
        if (empty($token)) {
            throw new ApiException("invalid M-Token", 30000);
        }
        
        $service = app()->make(UserModel::class);
        $userInfo = $service->parseToken($token);
        if ($userInfo) {
            $userInfo = $userInfo->toArray();
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
